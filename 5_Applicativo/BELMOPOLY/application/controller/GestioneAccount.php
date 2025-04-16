<?php

class GestioneAccount
{

    public function aggiungiAmico($username = null){
        require_once "./application/controller/Autenticazione.php";
        require_once "./application/controller/home.php";
        $autenticazione = new autenticazione();

        if($autenticazione->controlloLogin()) {

            $regexUsername = '/^[a-zA-Z0-9]{1,20}$/';
            $usernameAmico = $username;

            if(preg_match($regexUsername, $usernameAmico)){
                $GesioneUtenti = new \models\GestioneUtenti();

                $GesioneUtenti->AggiungiAmico($_SESSION['username'], $usernameAmico);


                $this->mostraUtenti();
            }else{
                $_SESSION["VerificaAmico"] = "Username non valido";
            }

        }
    }

    public function mostraRichiesteAmicizia(){
        require_once "./application/controller/Autenticazione.php";

        $autenticazione = new autenticazione();

        if($autenticazione->controlloLogin()) {

            $GesioneUtenti = new \models\GestioneUtenti();

            $amici = $GesioneUtenti->MostraRichiesteAmicizia($_SESSION['username']);


            require 'application/views/notifhce/requests.php';


        }
    }

    public function accettaRichiestaAmicizia($username = null)
    {
        require_once "./application/controller/Autenticazione.php";

        $autenticazione = new autenticazione();

        if($autenticazione->controlloLogin()) {
            $regexUsername = '/^[a-zA-Z0-9]{1,20}$/';
            $usernameAmico = $username;
            if(preg_match($regexUsername, $usernameAmico)) {

                $GesioneUtenti = new \models\GestioneUtenti();

                $GesioneUtenti->accettaRichiestaAmicizia($_SESSION['username'], $usernameAmico);

                $this->mostraRichiesteAmicizia();

            }else{
                $_SESSION["VerificaAmico"] = "Username non valido";
            }

        }
    }

    public function mostraAmicizie($param = '%'){
        require_once "./application/controller/Autenticazione.php";

        $autenticazione = new autenticazione();

        if($autenticazione->controlloLogin()) {

            $GesioneUtenti = new \models\GestioneUtenti();


            $amici = $GesioneUtenti->MostraAmicizia($param, $_SESSION['username']);

            require 'application/views/amici/friends.php';

        }
    }

    public function rifiutaRichiestaAmicizia($username)
    {
        require_once "./application/controller/Autenticazione.php";

        $autenticazione = new autenticazione();

        if($autenticazione->controlloLogin()){
            $regexUsername = '/^[a-zA-Z0-9]{1,20}$/';
            $usernameAmico = $username;
            if(preg_match($regexUsername,$usernameAmico)){
                $GesioneUtenti = new \models\GestioneUtenti();

                $GesioneUtenti->rifiutaRichiestaAmicizia($_SESSION['username'], $usernameAmico);
                $this->mostraRichiesteAmicizia();
            }else{
                $_SESSION["VerificaAmico"] = "Username non valido";
            }

        }

    }

    public function elliminaAmicizia($username = null)
    {
        require_once "./application/controller/Autenticazione.php";
        require_once "./application/controller/home.php";

        $autenticazione = new autenticazione();

        if($autenticazione->controlloLogin()) { // Aggiunto controllo login
            $regexUsername = '/^[a-zA-Z0-9]{1,20}$/';
            $usernameAmico = $username;


            if(preg_match($regexUsername, $usernameAmico)) { // Aggiunta validazione
                $GesioneUtenti = new \models\GestioneUtenti();
                $GesioneUtenti->eliminaAmicizia($_SESSION['username'], $usernameAmico);
            } else {
                $_SESSION["VerificaAmico"] = "Username non valido";
            }
        }

        $this->mostraAmicizie();
    }

    public function mostraUtenti($param = null)
    {
        require_once "./application/controller/Autenticazione.php";
        require_once "./application/controller/home.php";

        $autenticazione = new autenticazione();

        if($autenticazione->controlloLogin()) {

            $GesioneUtenti = new \models\GestioneUtenti();
            $utenti = $GesioneUtenti->mostraUtenti($param, $_SESSION['username']);


            require_once "./application/views/amici/users.php";

        }else{
            $home = new home();
            $home->index();
        }


    }


}