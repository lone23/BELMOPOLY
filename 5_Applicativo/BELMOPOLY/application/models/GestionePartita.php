<?php

namespace models;

class GestionePartita
{
    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $this->conn = \libs\Database::getConnection();
    }

    public function getSaldo(){
        $uniqueKey = $_SESSION['uuid'];
        $username = $_SESSION['username'];

        // Query con JOIN
        $sql = "
        SELECT fa_parte.saldo
        FROM fa_parte
        JOIN partita ON fa_parte.partita_id = partita.id
        JOIN utente ON fa_parte.utente_id = utente.id
        WHERE partita.unique_key = :unique_key AND utente.username = :username
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':unique_key' => $uniqueKey,
            ':username' => $username
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            echo "Saldo: " . $result['saldo'];
        } else {
            echo "Nessun risultato trovato.";
        }
    }
}