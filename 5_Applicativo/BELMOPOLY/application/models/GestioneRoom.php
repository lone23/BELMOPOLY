<?php

namespace models;

use PDOException;

class GestioneRoom
{
    private $conn;

    public function __construct()
    {
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

    public function elliminaRoom($username){

        try{
            $sth = $this->conn->prepare("SELECT id FROM utente WHERE username = :username");
            $sth->execute(['username' => $username]);
            $utente = $sth->fetch();

            $utente_id = $utente['id'];

            $sth = $this->conn->prepare("SELECT partita_id FROM fa_parte WHERE utente_id = :utente_id");
            $sth->execute(['utente_id' => $utente_id]);
            $partita = $sth->fetch();

            $partita_id = $partita['partita_id'];

            $sth = $this->conn->prepare("DELETE FROM partita WHERE id = :partita_id");
            $sth->execute(['partita_id' => $partita_id]);


            \libs\Logger::log("WARN -> Tentativo di elliminare la room effettuato con successo room: ". $partita_id);


        }catch (PDOException $e){
            \libs\Logger::log("WARN -> Tentativo di elliminare la room fallito: "+ $e->getMessage());
        }



    }


    public function invitaAmicoRoom($username)
    {

        try {

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
        public function accettaInvito($username,$uuid)
    {
        try {

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





}