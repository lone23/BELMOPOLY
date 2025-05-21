<?php

namespace models;

use PDOException;


class GestioneRoom
{
    private $conn;

    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $this->conn = \libs\Database::getConnection();
    }

    public function creaRoom($username)
    {

        try {

            $sth = $this->conn->prepare("SELECT id FROM utente WHERE username = :username");
            $sth->bindValue(":username", $username);
            $sth->execute();
            $row = $sth->fetch(\PDO::FETCH_ASSOC);

            $id = $row['id'];

            $_SESSION['uuid'] = uniqid('', true);
            setcookie('uuid', $_SESSION['uuid'], time() + 3600, '/');

            $sth = $this->conn->prepare("INSERT INTO partita (turno_player, unique_key) VALUES (:turno_player, :unique_key)");
            $sth->bindValue(':turno_player', $id);
            $sth->bindValue(':unique_key', $_SESSION['uuid']);
            $sth->execute();

            $sth = $this->conn->prepare("SELECT id FROM partita WHERE unique_key = :unique_key");
            $sth->bindValue(':unique_key', $_SESSION['uuid']);
            $sth->execute();
            $partita_id = $sth->fetchColumn();

            $sth = $this->conn->prepare("INSERT INTO fa_parte (utente_id, partita_id, capo_partita, richiesta, utente_prigione, posizione_pedina) 
                                 VALUES (:utente_id, :partita_id, :capo_partita, :richiesta, :utente_prigione, :posizione_pedina)");
            $sth->bindValue(':utente_id', $id);
            $sth->bindValue(':partita_id', $partita_id);
            $sth->bindValue(':capo_partita', true, \PDO::PARAM_BOOL);
            $sth->bindValue(':richiesta', false, \PDO::PARAM_BOOL);
            $sth->bindValue(':utente_prigione', false, \PDO::PARAM_BOOL);
            $sth->bindValue(':posizione_pedina', 0, \PDO::PARAM_INT);
            $sth->execute();


            \libs\Logger::log("INFO -> Room creata con successo, ID: " . $partita_id);

        } catch (\Exception $e) {
            \libs\Logger::log("WARN -> Creazione room fallita: " . $e->getMessage());
        }


    }

    public function elliminaRoom($username)
    {
        try {
            $this->conn->beginTransaction();

            // Ottieni l'id della partita in base alla unique_key
            $sth = $this->conn->prepare("SELECT id FROM partita WHERE unique_key = :unique_key");
            $sth->execute(['unique_key' => $_SESSION['uuid']]);
            $partita = $sth->fetch();

            if ($partita) {
                $partitaId = $partita['id'];

                // Elimina prima i riferimenti in fa_parte
                $sth = $this->conn->prepare("DELETE FROM fa_parte WHERE partita_id = :partita_id");
                $sth->execute(['partita_id' => $partitaId]);

                // Ora elimina la partita
                $sth = $this->conn->prepare("DELETE FROM partita WHERE id = :partita_id");
                $sth->execute(['partita_id' => $partitaId]);

                $this->conn->commit();
                \libs\Logger::log("INFO -> Room eliminata con successo: " . $_SESSION['uuid']);

                $_SESSION['uuid'] = null;
            } else {
                \libs\Logger::log("WARN -> Nessuna room trovata con uuid: " . $_SESSION['uuid']);
                $this->conn->rollBack();
            }

        } catch (PDOException $e) {
            $this->conn->rollBack();
            \libs\Logger::log("ERROR -> Tentativo di eliminare la room fallito: " . $e->getMessage());
        }
    }



    public function invitaAmicoRoom($username)
    {

        try {
            echo "ciao";
            $sth = $this->conn->prepare("SELECT id FROM utente WHERE username = :username");
            $sth->bindValue(":username", $username);
            $sth->execute();
            $row = $sth->fetch(\PDO::FETCH_ASSOC);

            $id = $row['id'];

            $sth = $this->conn->prepare("SELECT id FROM partita WHERE unique_key = :unique_key");
            $sth->bindValue(':unique_key', $_SESSION['uuid']);
            $sth->execute();
            $row = $sth->fetch(\PDO::FETCH_ASSOC);
            $partita_id = $row['id'];


            $sth = $this->conn->prepare("INSERT INTO fa_parte (utente_id, partita_id, capo_partita, richiesta, utente_prigione, posizione_pedina) 
                                 VALUES (:utente_id, :partita_id, :capo_partita, :richiesta, :utente_prigione, :posizione_pedina)");
            $sth->bindValue(':utente_id', $id);
            $sth->bindValue(':partita_id', $partita_id);
            $sth->bindValue(':capo_partita', false, \PDO::PARAM_BOOL);
            $sth->bindValue(':richiesta', true, \PDO::PARAM_BOOL);
            $sth->bindValue(':utente_prigione', false, \PDO::PARAM_BOOL);
            $sth->bindValue(':posizione_pedina', 0, \PDO::PARAM_INT);
            $sth->execute();


            \libs\Logger::log("INFO -> Invito room effettuato con successo, ID room: " . $partita_id . " ID player: " . $id);
        } catch (\Exception $e) {
            \libs\Logger::log("INFO -> Invito room fallito, ID room: " . $partita_id . " ID player: " . $id . " error: " . $e->getMessage());
        }
    }


    public function getInvitiConUsernameCapo($username)
    {
        $richieste = [];

        try {
            // 1. Prendo l'id dell'utente
            $sth = $this->conn->prepare("SELECT id FROM utente WHERE username = :username");
            $sth->bindValue(":username", $username);
            $sth->execute();
            $userRow = $sth->fetch(\PDO::FETCH_ASSOC);

            if (!$userRow) {
                throw new \Exception("Utente non trovato.");
            }

            $userId = $userRow['id'];

            $sth = $this->conn->prepare("
            SELECT u.username AS capo_username
            FROM fa_parte f_mio
            INNER JOIN fa_parte f_capo ON f_mio.partita_id = f_capo.partita_id AND f_capo.capo_partita = TRUE
            INNER JOIN utente u ON f_capo.utente_id = u.id
            WHERE f_mio.utente_id = :utente_id AND f_mio.richiesta = TRUE
        ");
            $sth->bindValue(":utente_id", $userId, \PDO::PARAM_INT);
            $sth->execute();

            while ($row = $sth->fetch(\PDO::FETCH_ASSOC)) {
                $richieste[] = $row['capo_username'];
            }

            return $richieste;

        } catch (\Exception $e) {
            \libs\Logger::log("WARN -> Errore nel recupero inviti: " . $e->getMessage());
            return [];
        }
    }

        public function accettaInvito($username)
    {
        try {

            $sth = $this->conn->prepare("SELECT id FROM utente WHERE username = :username");
            $sth->bindValue(":username", $username);
            $sth->execute();
            $row = $sth->fetch(\PDO::FETCH_ASSOC);

            $id = $row['id'];

            $sth = $this->conn->prepare("
            SELECT DISTINCT f_mio.partita_id as id
            FROM fa_parte f_mio
            INNER JOIN fa_parte f_capo 
                ON f_mio.partita_id = f_capo.partita_id 
                AND f_capo.capo_partita = TRUE
            WHERE f_mio.utente_id = :utente_id 
                AND f_mio.richiesta = TRUE
        ");
            $sth->bindValue(":utente_id", $id, \PDO::PARAM_INT);
            $sth->execute();
            $row = $sth->fetch(\PDO::FETCH_ASSOC);
            $partita_id = $row['id'];



            $sth = $this->conn->prepare("SELECT unique_key FROM partita WHERE id = :id");
            $sth->bindValue(":id", $partita_id);
            $sth->execute();
            $row = $sth->fetch(\PDO::FETCH_ASSOC);
            $uuid = $row['unique_key'];

            $_SESSION['uuid'] = $uuid;
            setcookie('uuid', $uuid, time() + 3600, '/');


            $sth = $this->conn->prepare("UPDATE fa_parte 
                             SET capo_partita = :capo_partita 
                             WHERE utente_id = :utente_id AND partita_id = :partita_id");

            $sth->bindValue(':capo_partita', false, \PDO::PARAM_BOOL);
            $sth->bindValue(':utente_id', $id, \PDO::PARAM_INT);
            $sth->bindValue(':partita_id', $partita_id, \PDO::PARAM_INT);
            $sth->execute();


            \libs\Logger::log("INFO -> Invito room effettuato con successo, ID room: " . $partita_id . " ID player: " . $id);
        } catch (\Exception $e) {
            \libs\Logger::log("INFO -> Invito room fallito, ID room: " . $partita_id . " ID player: " . $id . " error: " . $e->getMessage());
        }
    }


    public function eliminaInvitoGiocatore($capoUsername, $giocatoreUsername)
    {
        try {
            $sth = $this->conn->prepare("SELECT id FROM utente WHERE username = :username");
            $sth->execute(['username' => $capoUsername]);
            $capoRow = $sth->fetch(\PDO::FETCH_ASSOC);

            if (!$capoRow) {
                throw new \Exception("Capo non trovato.");
            }
            $capoId = $capoRow['id'];

            $sth = $this->conn->prepare("
            SELECT partita_id 
            FROM fa_parte 
            WHERE utente_id = :capo_id AND capo_partita = TRUE
        ");
            $sth->execute(['capo_id' => $capoId]);
            $partitaRow = $sth->fetch(\PDO::FETCH_ASSOC);

            if (!$partitaRow) {
                throw new \Exception("Partita non trovata per il capo.");
            }
            $partitaId = $partitaRow['partita_id'];

            $sth = $this->conn->prepare("SELECT id FROM utente WHERE username = :username");
            $sth->execute(['username' => $giocatoreUsername]);
            $utenteRow = $sth->fetch(\PDO::FETCH_ASSOC);

            if (!$utenteRow) {
                throw new \Exception("Giocatore non trovato.");
            }
            $utenteId = $utenteRow['id'];

            $sth = $this->conn->prepare("
            DELETE FROM fa_parte 
            WHERE partita_id = :partita_id AND utente_id = :utente_id
        ");
            $sth->execute([
                'partita_id' => $partitaId,
                'utente_id' => $utenteId
            ]);

            \libs\Logger::log("INFO -> Invito eliminato per '$giocatoreUsername' nella partita del capo '$capoUsername'.");

        } catch (\Exception $e) {
            \libs\Logger::log("ERROR -> Errore nell'eliminazione invito: " . $e->getMessage());
        }
    }


    public function startGame()
    {
        try {
            $uuidPartita = $_SESSION['uuid'];

            // 1. Recupera l'id della partita tramite uuid
            $sth = $this->conn->prepare("SELECT id FROM partita WHERE unique_key = :uuid");
            $sth->execute(['uuid' => $uuidPartita]);
            $partitaRow = $sth->fetch(\PDO::FETCH_ASSOC);

            if (!$partitaRow) {
                throw new \Exception("Partita non trovata con uuid: $uuidPartita");
            }

            $partitaId = $partitaRow['id'];

            // 2. Aggiorna la colonna `room` a false per tutti i partecipanti
            $sth = $this->conn->prepare("
            UPDATE fa_parte 
            SET room = FALSE 
            WHERE partita_id = :partita_id
        ");
            $sth->execute(['partita_id' => $partitaId]);

            \libs\Logger::log("INFO -> Partita con UUID '$uuidPartita' avviata. Room impostata su FALSE per tutti.");

        } catch (\Exception $e) {
            \libs\Logger::log("ERROR -> Errore nell'avvio della partita con uuid '$uuidPartita': " . $e->getMessage());
        }
    }



    public function isStartGame($username)
    {

        try {

            $sth = $this->conn->prepare("SELECT id FROM utente WHERE username = :username");
            $sth->execute(['username' => $username]);
            $userRow = $sth->fetch(\PDO::FETCH_ASSOC);

            if (!$userRow) {
                throw new \Exception("Utente non trovato.");
            }

            $userId = $userRow['id'];


            $sth = $this->conn->prepare("
            SELECT room 
            FROM fa_parte 
            WHERE utente_id = :utente_id
            LIMIT 1
        ");
            $sth->execute(['utente_id' => $userId]);
            $roomRow = $sth->fetch(\PDO::FETCH_ASSOC);


            if (!$roomRow) {
                return true; // Non è in nessuna room => considerata "chiusa"
            }


            return !$roomRow['room'];

        } catch (\Exception $e) {
            \libs\Logger::log("ERROR -> Errore nel controllo room: " . $e->getMessage());
            return true; // Per sicurezza ritorna true (come se fosse chiusa)
        }


    }

    public function numeroGiocatori($uuid)
    {
        // Ottieni l'id della partita tramite UUID
        $sth = $this->conn->prepare("SELECT id FROM partita WHERE unique_key = :uuid");
        $sth->execute(['uuid' => $uuid]);
        $idPartita = $sth->fetch(\PDO::FETCH_ASSOC);

        if (!$idPartita || !isset($idPartita['id'])) {
            return []; // Nessuna partita trovata
        }

        $idPartita = $idPartita['id'];

        // Ottieni tutti gli username dei giocatori che fanno parte della partita, ordinati per utente_id
        $sth = $this->conn->prepare("
        SELECT u.username
        FROM fa_parte fp
        JOIN utente u ON fp.utente_id = u.id
        WHERE fp.partita_id = :id
        ORDER BY u.id ASC
    ");
        $sth->execute(['id' => $idPartita]);
        $usernames = $sth->fetchAll(\PDO::FETCH_COLUMN); // Prende solo la colonna 'username'

        return $usernames;
    }

    public function salvaPosizionePedina($posizione)
    {

        \libs\Logger::log("INFO -> Posizione: " . $posizione);
        // Ottieni l'id della partita tramite UUID
        $sth = $this->conn->prepare("SELECT id FROM partita WHERE unique_key = :uuid");
        $sth->execute(['uuid' => $_SESSION['uuid']]);
        $idPartita = $sth->fetch(\PDO::FETCH_ASSOC);

        if (!$idPartita || !isset($idPartita['id'])) {
            return []; // Nessuna partita trovata
        }

        $idPartita = $idPartita['id'];
        \libs\Logger::log("INFO -> idPartita: " . $idPartita);

        // Ottieni tutti gli username dei giocatori che fanno parte della partita, ordinati per utente_id
        $sth = $this->conn->prepare("
            UPDATE fa_parte
            SET posizione_pedina = :posizione
            WHERE partita_id = :idPartita AND utente_id = :utenteid;

    ");
        $sth->execute([
            'idPartita' => $idPartita,
            'utenteid' => $_COOKIE['id'],
            'posizione' => $posizione
        ]);
        \libs\Logger::log("INFO -> id: " . $_COOKIE['id']);

        $usernames = $sth->fetchAll(\PDO::FETCH_COLUMN); // Prende solo la colonna 'username'

    }

    public function prendiPosizionePedina()
    {
        // Ottieni l'id della partita tramite UUID
        $sth = $this->conn->prepare("SELECT id FROM partita WHERE unique_key = :uuid");
        $sth->execute(['uuid' => $_SESSION['uuid']]);
        $idPartita = $sth->fetch(\PDO::FETCH_ASSOC);

        if (!$idPartita || !isset($idPartita['id'])) {
            return []; // Nessuna partita trovata
        }

        $idPartita = $idPartita['id'];

        // Ottieni tutte le posizioni dei giocatori nella partita, unendo con utente per avere username
        $sth = $this->conn->prepare("
        SELECT u.username, fp.posizione_pedina
        FROM fa_parte fp
        JOIN utente u ON u.id = fp.utente_id
        WHERE fp.partita_id = :idPartita
    ");
        $sth->execute(['idPartita' => $idPartita]);

        $risultati = $sth->fetchAll(\PDO::FETCH_ASSOC);

        // Costruisci array associativo username => posizione_pedina
        $posizioni = [];
        foreach ($risultati as $riga) {
            $posizioni[$riga['username']] = $riga['posizione_pedina'];
            \libs\Logger::log("INFO -> prova: " . $riga['username'] . "   " . $riga['posizione_pedina']);
        }

        return $posizioni;
    }










}