<?php

namespace models;

class GestionePartita
{
    private $conn;
    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $this->conn = \libs\Database::getConnection();
    }

    public function getSaldo(){
        $uniqueKey = $_SESSION['uuid'];

        $stmt = $this->conn->prepare("SELECT id FROM partita WHERE unique_key = :unique_key");
        $stmt->execute(['unique_key' => $uniqueKey]);
        $partita = $stmt->fetch();

        $stmt = $this->conn->prepare("
            SELECT u.username, f.saldo
            FROM fa_parte f
            JOIN utente u ON u.id = f.utente_id
            WHERE f.partita_id = :partita_id
            ORDER BY f.utente_id
        ");
        $stmt->execute(['partita_id' => $partita['id']]);

        $results = $stmt->fetchAll();
        return $results;
    }

    public function setSaldo($value){

        $uniqueKey = $_SESSION['uuid'];

        $stmt = $this->conn->prepare("SELECT id FROM partita WHERE unique_key = :unique_key");
        $stmt->execute(['unique_key' => $uniqueKey]);
        $partita = $stmt->fetch();


        $stmt = $this->conn->prepare("
            UPDATE fa_parte
            SET saldo = saldo - :deltaSaldo
            WHERE utente_id = :utente_id AND partita_id = :partita_id
        ");

        \libs\Logger::log("INFO -> Nella partita: " . $partita['id'] . " Il giocatore: " . $_SESSION['id'] . " Ha speso: " . $value);

        return $stmt->execute([
            'deltaSaldo' => $value,
            'utente_id' => $_SESSION['id'],
            'partita_id' => $partita['id']
        ]);


    }

    public function acquistaProprieta($idProprieta) {
        try {
            $uniqueKey = $_SESSION['uuid'];
            \libs\Logger::log("INFO -> UUID: " . $uniqueKey);

            // Recupera ID partita
            $stmt = $this->conn->prepare("SELECT id FROM partita WHERE unique_key = :unique_key");
            $stmt->execute(['unique_key' => $uniqueKey]);
            $idPartita = $stmt->fetchColumn();

            \libs\Logger::log("INFO -> ID PARTITA: " . $idPartita);

            $idUtente = $_SESSION['id'];
            \libs\Logger::log("INFO -> ID UTENTE: " . $idUtente);
            \libs\Logger::log("INFO -> ID PROPRIETA: " . $idProprieta);

            // Controlla se è già stata acquistata in questa partita
            $stmt = $this->conn->prepare("
            SELECT COUNT(*) 
            FROM proprietaAppartengono 
            WHERE id_proprieta = :idProprieta AND id_partita = :idPartita
        ");
            $stmt->execute([
                'idProprieta' => $idProprieta,
                'idPartita' => $idPartita
            ]);
            $esiste = $stmt->fetchColumn();

            if ($esiste > 0) {
                \libs\Logger::log("INFO -> PROPRIETÀ GIÀ ACQUISTATA, NON FARE NULLA.");
                return false; // oppure return true; se vuoi considerarlo "ok ma già presa"
            }

            // Inserisci la relazione proprietà - utente
            \libs\Logger::log("INFO -> INSERITO => PROPRIETAID: " . $idProprieta . " | PARTITAID: " . $idPartita . " | UTENTEID: " . $idUtente);
            $stmt = $this->conn->prepare("
            INSERT INTO proprietaAppartengono (id_proprieta, id_partita, id_utente) 
            VALUES (:idProprieta, :idPartita, :idUtente)
        ");
            return $stmt->execute([
                'idProprieta' => $idProprieta,
                'idPartita' => $idPartita,
                'idUtente' => $idUtente
            ]);

        } catch (PDOException $e) {
            \libs\Logger::log("ERRORE acquistaProprieta: " . $e->getMessage());
            return false;
        }
    }


    public function proprietaPosseduta($idProprieta)
    {
        $uniqueKey = $_SESSION['uuid'];

        // Otteniamo l'ID della partita attiva
        $stmt = $this->conn->prepare("SELECT id FROM partita WHERE unique_key = :unique_key");
        $stmt->execute(['unique_key' => $uniqueKey]);
        $idPartita = $stmt->fetch()['id'];

        // Controlliamo se la proprietà è posseduta nella partita corrente
        $stmt = $this->conn->prepare("
        SELECT id_utente 
        FROM proprietaAppartengono 
        WHERE id_proprieta = :idProprieta 
        AND id_partita = :idPartita
    ");
        $stmt->execute([
            'idProprieta' => $idProprieta,
            'idPartita' => $idPartita
        ]);

        $row = $stmt->fetch();

        if ($row) {
            return [
                'possessore' => true,
                'id_utente' => $row['id_utente']
            ];
        } else {
            return [
                'possessore' => false
            ];
        }
    }


    public function getAffittoBase($idProprieta) {


        $sql = "SELECT affitto FROM proprietaNormali WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':id' => $idProprieta
        ]);

        $risultato = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($risultato && isset($risultato['affitto'])) {
            return $risultato['affitto'];
        }

        return 0;
    }




    public function getGiocatore($uuidPartita, $idCarta) {
        // 1. Recupera l'id della partita dall'UUID
        $sqlIdPartita = "SELECT id FROM partita WHERE unique_key = :uuid";
        $stmt = $this->conn->prepare($sqlIdPartita);
        $stmt->execute([
            ':uuid' => $uuidPartita
        ]);

        $partita = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$partita || !isset($partita['id'])) {
            throw new \Exception("Partita non trovata con UUID: $uuidPartita");
        }

        $idPartita = $partita['id'];

        // 2. Recupera l'id del giocatore dalla tabella proprietaAppartengono
        $sqlGiocatore = "SELECT id_utente FROM proprietaAppartengono WHERE id_partita = :id_partita AND id_proprieta = :id_proprieta;";
        $stmt = $this->conn->prepare($sqlGiocatore);
        $stmt->execute([
            ':id_partita' => $idPartita,
            ':id_proprieta' => $idCarta
        ]);

        $risultato = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($risultato && isset($risultato['id_utente'])) {
            return $risultato['id_utente'];
        }

        // Nessun giocatore trovato
        return null;
    }


    public function aggiornaSaldoAffitto($uuidPartita, $idPagante, $idProprietario, $affitto) {
        try {
            $this->conn->beginTransaction();

            $sql = "SELECT id FROM partita WHERE unique_key = :uuid";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':uuid' => $uuidPartita]);
            $idPartita = $stmt->fetchColumn();

            if (!$idPartita) {
                throw new Exception("Partita non trovata");
            }

            $sql = "UPDATE fa_parte
            SET saldo = saldo - :affitto
            WHERE utente_id = :idPagante AND partita_id = :idPartita";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':affitto' => $affitto,
                ':idPagante' => $idPagante,
                ':idPartita' => $idPartita
            ]);

            if ($stmt->rowCount() === 0) {
                error_log("Nessuna riga aggiornata per il pagante");
            }

            $sql = "UPDATE fa_parte
            SET saldo = saldo + :affitto
            WHERE utente_id = :idProprietario AND partita_id = :idPartita";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':affitto' => $affitto,
                ':idProprietario' => $idProprietario,
                ':idPartita' => $idPartita
            ]);

            if ($stmt->rowCount() === 0) {
                error_log("Nessuna riga aggiornata per il proprietario");
            }

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Errore aggiornaSaldoAffitto: " . $e->getMessage());
            return false;
        }
    }




}