<?php


class autenticazione
{


    public function controlloLogin(){


        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if(!isset($_SESSION["isAuthenticated"])){

            $this->login();

            return false;
        }else{
            return true;
        }
    }


    public function logout(){

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();

        $this->login();
    }

    public function login(){
        require 'application/views/login/index.php';
        require  './application/views/templates/footer.php';
    }

    public function verificaLogin(){


        $_SESSION["ControlloLogin"] = "";

        $email = $_POST['email'];
        $password = $_POST['password'];



        if(empty($email) or empty($password)){
            $_SESSION["ControlloLogin"] = "Inserisci tutti i campi";
            $this->login();
        }else{

            $AutenticazioneUtenti = new \models\AutenticazioneUtenti();

            if($AutenticazioneUtenti->verificaLogin($email,$password)){

                require_once './application/controller/Home.php';
                $home = new Home();
                $home->index();
            }else{

                $this->login();
            }

        }

    }

    public function RegistraUtenteView()
    {
        require 'application/views/register/index.php';
        require  './application/views/templates/footer.php';

    }


    public function RegistraUtente()
    {

        $email = $_POST['email'];
        $username = $_POST['username'];
        $password = $_POST['password'];


        if(empty($email) or empty($username) or empty($password)){

            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            $_SESSION["ControlloRegister"] = "Inserisci tutti i campi";

            $this->registraUtenteView();
        }else{
            $AutenticazioneUtenti = new \models\AutenticazioneUtenti();
            if($AutenticazioneUtenti->registraUtente($email,$username,$password)){
                $this->login();
            }else{
                $this->registraUtenteView();
            }
        }



    }

}