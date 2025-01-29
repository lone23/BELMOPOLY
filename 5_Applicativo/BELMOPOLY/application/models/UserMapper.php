<?php
require_once 'User.php';
class UserMapper
{


    private $conn;
    public function __construct()
    {
        $conn = new \mysqli(HOST, USER, PASSWORD, DATABASE);

        if ($conn->connect_error) {
            die("Errore di connessione (" . $conn->connect_errno . ") ". $conn->connect_error);
        }

        $this->conn =  $conn;
    }

    public function fetchAll(){

        $Users = array();

        $query = "SELECT * FROM utenti order by data_creazione";
        $result = $this->conn->query($query);

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $User = new User($row['id'], $row['email'], $row['username'], $row['data_creazione']);
            $Users[] = $User;
        }

        return $Users;


    }

    public function verificaLogin($email,$password){

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $query = "SELECT * FROM utenti";
        $result = $this->conn->query($query);

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            if($row['email'] == $email and $row['password'] == $password){
                $_SESSION['email'] = $row['email'];
                $_SESSION["username"] = $row['username'];
                $_SESSION["isAuthenticated"] = true;

                return true;
            }
            if($row['email'] != $email){
                $_SESSION["ControlloLogin"] = "Email non corretta";
            }else{
                $_SESSION["ControlloLogin"] = "Password non corretta";
            }

        }
        return false;

    }

}