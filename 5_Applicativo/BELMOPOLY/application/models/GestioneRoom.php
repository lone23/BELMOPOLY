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
            while ($row = $sth->fetch( \PDO::FETCH_ASSOC)) {
                $id = $row['id'];
            }

            $sth = $this->conn->prepare("INSERT INTO partita (turno_player) VALUES (:turno_player)");
            $sth->bindValue(':turno_player', $id);
            $sth->execute();

            $partita_id = $this->conn->lastInsertId();

            $sth = $this->conn->prepare("INSERT INTO fa_parte (utente_id, partita_id, capo_partita, richiesta, utente_prigione, posizione_pedina) VALUES (:utente_id, :partita_id, :capo_partita, :richiesta, :utente_prigione, :posizione_pedina)");
            $sth->bindValue(':utente_id', $id);
            $sth->bindValue(':partita_id', $partita_id);
            $sth->bindValue(':capo_partita', $id);
            $sth->bindValue(':richiesta', 'FALSE');
            $sth->bindValue(':utente_prigione', 'FALSE');
            $sth->bindValue(':posizione_pedina', 0);
            $sth->execute();

            \libs\Logger::log("INFO -> Tentativo di creare la room effettuato con successo room: ".$partita_id);

        } catch (PDOException $e) {
            \libs\Logger::log("WARN -> Tentativo di creare la room fallito: "+ $e->getMessage());
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


    public function invitaAmicoRoom()
    {

    }


}