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

            $stmt = $this->conn->prepare("SELECT id FROM partita WHERE unique_key = :unique_key");
            $stmt->execute(['unique_key' => $uniqueKey]);
            $idPartita = $stmt->fetch()['id'];

            \libs\Logger::log("INFO -> ID PARTITA: " . $idPartita);

            $idUtente = $_SESSION['id'];

            \libs\Logger::log("INFO -> ID UTENTE: " . $idUtente);

            \libs\Logger::log("INFO -> ID PROPRIETA: " . $idProprieta);


            // Prima controlla se la proprietà è già acquistata
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM proprietaAppartengono WHERE id_proprieta = :idProprieta");
            $stmt->execute(['idProprieta' => $idProprieta]);
            if ($stmt->fetchColumn() > 0) {
                \libs\Logger::log("INFO -> PROPRIETA GIA VENDUTA");
                return false;  // proprietà già venduta
            }

            // Inserisci la relazione proprietà - utente
            \libs\Logger::log("INFO -> INSERITO => PROPRIETAID: " . $idProprieta . " | PARTITAID: "  . $idPartita . " | UTENTEID: " . $idUtente);
            $stmt = $this->conn->prepare("INSERT INTO proprietaAppartengono (id_proprieta, id_partita, id_utente) VALUES (:idProprieta, :idPartita, :idUtente)");
            return $stmt->execute(['idProprieta' => $idProprieta, 'idPartita' => $idPartita, 'idUtente' => $idUtente]);


        } catch (PDOException $e) {
            // gestisci errore o logga
            return false;
        }
    }
}