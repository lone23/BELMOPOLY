<?php

namespace models;

class GestioneUtenti
{
    private $conn;
    public function __construct()
    {
        $this->conn = \libs\Database::getConnection();
    }

    public function AggiungiAmico($usernameUtente, $UsernameAmico)
    {
        $sth = $this->conn->prepare('SELECT id, username FROM utente WHERE username IN (:utente, :amico)');
        $sth->bindValue(':utente', $usernameUtente);
        $sth->bindValue(':amico', $UsernameAmico);
        $sth->execute();

        $ids = [];
        while ($row = $sth->fetch( \PDO::FETCH_ASSOC)) {
            $ids[$row['username']] = $row['id'];
        }

        if (!isset($ids[$usernameUtente]) || !isset($ids[$UsernameAmico])) {
            \libs\Logger::log("WARN -> Tentativo di amicizia fallito: uno o entrambi gli username non esistono ({$usernameUtente}, {$UsernameAmico})");
        }


        $idUtente = $ids[$usernameUtente];
        $idAmico = $ids[$UsernameAmico];

        $sth = $this->conn->prepare('INSERT INTO amico (mandante, ricevente,richiesta) VALUES (:mandante, :ricevente,:richiesta)');
        $sth->bindValue(':mandante', $idUtente);
        $sth->bindValue(':ricevente', $idAmico);
        $sth->bindValue(':richiesta', false);
        $sth->execute();

        \libs\Logger::log("INFO -> Richiesta di amicizia inviata correttamente da {$usernameUtente} a {$UsernameAmico}");

    }


    public function MostraRichiesteAmicizia($username)
    {
        $amici = array();

        try{
            $sth = $this->conn->prepare('SELECT u1.username 
                             FROM amico a
                             JOIN utente u1 ON u1.id = a.mandante
                             JOIN utente u2 ON u2.id = a.ricevente
                             WHERE u2.username = :username AND a.richiesta = false;');

            $sth->bindValue(':username', $username);
            $sth->execute();
        }catch (\PDOException $e){
            \libs\Logger::log("ERROR -> Tentativo di mostrare le richieste di amicizia fallito: {$e->getMessage()}");
            die();
        }

        while ($row = $sth->fetch()) {
            $utente = new Utente();
            $utente->setUsername($row['username']);
            $amici[] = $utente;
        }

        return $amici;

    }

    public function accettaRichiestaAmicizia($usernameUtente, $usernameAmico)
    {
        try{
            $sth = $this->conn->prepare('UPDATE amico a
                                 JOIN utente u1 ON u1.id = a.mandante
                                 JOIN utente u2 ON u2.id = a.ricevente
                                 SET a.richiesta = TRUE
                                 WHERE u1.username = :usernameAmico
                                 AND u2.username = :usernameUtente');

            $sth->bindValue(':usernameUtente', $usernameUtente);
            $sth->bindValue(':usernameAmico', $usernameAmico);
            $sth->execute();
            \libs\Logger::log("INFO -> Amicizia accettata correttamente da {$usernameUtente} a {$usernameAmico}");
        }catch (\PDOException $e){
            \libs\Logger::log("WARN -> Tentativo di accettare l'amicizia fallito: {$e->getMessage()}");
        }
    }

    public function rifiutaRichiestaAmicizia($usernameUtente, $usernameAmico)
    {
        try{
            $sth = $this->conn->prepare('DELETE a
                             FROM amico a
                             JOIN utente u1 ON u1.id = a.mandante
                             JOIN utente u2 ON u2.id = a.ricevente
                             WHERE u1.username = :usernameAmico
                             AND u2.username = :usernameUtente
                             AND a.richiesta = FALSE');

            $sth->bindValue(':usernameUtente', $usernameUtente);
            $sth->bindValue(':usernameAmico', $usernameAmico);
            $sth->execute();
            \libs\Logger::log("INFO -> Rifiuto di amicizia inviata correttamente da {$usernameUtente} a {$usernameAmico}");
        }catch (\PDOException $e){
            \libs\Logger::log("WARN -> Tentativo di rifiuto amicizia fallito: {$e->getMessage()}");
        }

    }

    public function MostraAmicizia($username)
    {
        $amici = array();

        try{
            $sth = $this->conn->prepare('SELECT u1.username 
                             FROM amico a
                             JOIN utente u1 ON u1.id = a.mandante
                             JOIN utente u2 ON u2.id = a.ricevente
                             WHERE u2.username = :username;');

            $sth->bindValue(':username', $username);
            $sth->execute();
        }catch (\PDOException $e){
            \libs\Logger::log("ERROR -> Tentativo di mostrare le amicizie fallito: {$e->getMessage()}");
            die();
        }

        while ($row = $sth->fetch()) {
            $utente = new Utente();
            $utente->setUsername($row['username']);
            $amici[] = $utente;
        }

        return $amici;

    }



}