<?php
require_once 'Utenti.php';
class AutenticazioneUtenti
{


    private $conn;
    public function __construct()
    {
        require_once './application/libs/database.php';
        $this->conn = Database::getConnection();
    }

    public function verificaLogin($email, $password)
    {
        require_once './application/libs/logger.php';
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        try {
            $sth = $this->conn->prepare('SELECT password FROM utenti WHERE email = :email');
            $sth->bindValue(':email', $email);
            $sth->execute();

            $passwordHash = $sth->fetchColumn();

            if ($passwordHash && password_verify($password, $passwordHash)) {
                Logger::log("INFO -> utente ".$email." autenticato con successo");

                $_SESSION['email'] = $email;
                $_SESSION["isAuthenticated"] = true;
                return true;
            } else {
                $_SESSION["ControlloLogin"] = "Email o password errata";
                Logger::log("Tentativo di login fallito per email: $email");
                return false;
            }
        } catch (Exception $e) {

            Logger::log("ERROR -> Errore verificaLogin: " . $e->getMessage());
            return false;
        }
    }


    public function registraUtente($email, $username, $password) {
        require_once './application/libs/logger.php';

        $regexEmail = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
        $regexPassword = '/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';
        $regexUsername = '/^[a-zA-Z0-9]{1,20}$/';


        if (!$this->controllaUniicitaUsername($username)) {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            $_SESSION["ControlloRegister"] = "Username è già presente";
            return false;
        }


        if (!preg_match($regexEmail, $email)) {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            $_SESSION["ControlloRegister"] = "Email non è valida";
            return false;
        }


        if (!preg_match($regexUsername, $username)) {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            $_SESSION["ControlloRegister"] = '
        <div>
            username non rispecchia i requisiti:
            <ul style="margin-top: 5px;">
                <li>Massimo 20 caratteri</li>
                <li>Nessun carattere speciale</li>
            </ul>
        </div>';
            return false;
        }


        if (!preg_match($regexPassword, $password)) {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            $_SESSION["ControlloRegister"] = '
        <div>
            La password non è abbastanza sicura:
            <ul style="margin-top: 5px;">
                <li>Minimo 8 caratteri</li>
                <li>Almeno una maiuscola</li>
                <li>Almeno un numero</li>
                <li>Almeno un carattere speciale</li>
            </ul>
        </div>';
            return false;
        }

        try{
            $password = password_hash($password, PASSWORD_BCRYPT);


            $sth = $this->conn->prepare("INSERT INTO utenti (email, username, password) VALUES (:email, :username, :password)");

            $sth->bindValue(':email', $email);
            $sth->bindValue(':username', $username);
            $sth->bindValue(':password', $password);

            $sth->execute();

            Logger::log("INFO -> registrazione utente ".$email." effettuata con successo");
            return true;
        }catch(PDOException $e){


            Logger::log("ERROR -> Errore registrazione utente: ".$e->getMessage());
            $_SESSION["ControlloRegister"] = "Registrazione utente non riuscita contatta un amministratore";
            return false;

        }

    }



    private function controllaUniicitaUsername($username){

        $controllo = true;

        $sth = $this->conn->prepare("SELECT username from utenti");
        $sth->execute();
        while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
            if($row["username"] == $username){
                $controllo = false;
            }
        }

        return $controllo;
    }

}