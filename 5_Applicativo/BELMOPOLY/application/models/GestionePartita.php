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

        return $stmt->execute([
            'deltaSaldo' => $value,
            'utente_id' => $_SESSION['id'],
            'partita_id' => $partita['id']
        ]);

        \libs\Logger::log("INFO -> Nella partita: " . $partita['id'] . " Il giocatore: " . $_SESSION['id'] . " Ha speso: " . $value);
    }
}