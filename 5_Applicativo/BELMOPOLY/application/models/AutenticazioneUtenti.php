<?php
namespace models;
class AutenticazioneUtenti
{


    private $conn;
    public function __construct()
    {
        $this->conn = \libs\Database::getConnection();
    }

    public function verificaLogin($email, $password)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        try {
            $sth = $this->conn->prepare('SELECT id, password, username FROM utente WHERE email = :email');
            $sth->bindValue(':email', $email);
            $sth->execute();

            $row = $sth->fetch(\PDO::FETCH_ASSOC);
            if (!$row) {
                $_SESSION["ControlloLogin"] = "Email o password errata";
                \libs\Logger::log("Tentativo di login fallito per email: $email");
                return false;
            }

            $username = $row["username"];
            $passwordHash = $row["password"];
            $_SESSION["id"] = $row["id"];
            setcookie('id', $row["id"], time() + 10000, '/');

            if (password_verify($password, $passwordHash)) {
                \libs\Logger::log("INFO -> Utente $email autenticato con successo");

                $_SESSION['email'] = $email;
                $_SESSION['username'] = $username;
                $_SESSION["isAuthenticated"] = true;
                return true;
            } else {
                $_SESSION["ControlloLogin"] = "Email o password errata";
                \libs\Logger::log("Tentativo di login fallito per email: $email");
                return false;
            }
        } catch (\Exception $e) {
            \libs\Logger::log("ERROR -> Errore verificaLogin: " . $e->getMessage());
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


            $sth = $this->conn->prepare("INSERT INTO utente (email, username, password) VALUES (:email, :username, :password)");

            $sth->bindValue(':email', $email);
            $sth->bindValue(':username', $username);
            $sth->bindValue(':password', $password);

            $sth->execute();

            \libs\Logger::log("INFO -> registrazione utente ".$email." effettuata con successo");
            return true;
        }catch(\PDOException $e){


            \libs\Logger::log("ERROR -> Errore registrazione utente: ".$e->getMessage());
            $_SESSION["ControlloRegister"] = "Registrazione utente non riuscita contatta un amministratore";
            return false;

        }

    }



    private function controllaUniicitaUsername($username){

        $controllo = true;

        $sth = $this->conn->prepare("SELECT username from utente");
        $sth->execute();
        while ($row = $sth->fetch(\PDO::FETCH_ASSOC)) {
            if($row["username"] == $username){
                $controllo = false;
            }
        }

        return $controllo;
    }

}