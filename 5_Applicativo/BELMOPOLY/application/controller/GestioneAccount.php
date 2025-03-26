<?php

class GestioneAccount
{

    public function aggiungiAmico(){
        require_once "./application/controller/autenticazione.php";
        require_once "./application/controller/home.php";
        $autenticazione = new autenticazione();

        if($autenticazione->controlloLogin()) {

            $regexUsername = '/^[a-zA-Z0-9]{1,20}$/';
            $usernameAmico = $_POST['username'];

            if(preg_match($regexUsername, $usernameAmico)){
                $GesioneUtenti = new \models\GestioneUtenti();

                $GesioneUtenti->AggiungiAmico($_SESSION['username'], $usernameAmico);


                $home = new home();
                $home->index();
            }else{
                $_SESSION["VerificaAmico"] = "Username non valido";
            }

        }
    }

    public function mostraRichiesteAmicizia(){
        require_once "./application/controller/autenticazione.php";

        $autenticazione = new autenticazione();

        if($autenticazione->controlloLogin()) {

            $GesioneUtenti = new \models\GestioneUtenti();

            $amici = $GesioneUtenti->MostraRichiesteAmicizia($_SESSION['username']);

            require './application/views/templates/header.php';
            require 'application/views/amicihome/index.php';
            require './application/views/templates/footer.php';

        }
    }

    public function accettaRichiestaAmicizia()
    {
        require_once "./application/controller/autenticazione.php";

        $autenticazione = new autenticazione();

        if($autenticazione->controlloLogin()) {
            $regexUsername = '/^[a-zA-Z0-9]{1,20}$/';
            $usernameAmico = $_POST['username'];
            if(preg_match($regexUsername, $usernameAmico)) {

                $GesioneUtenti = new \models\GestioneUtenti();

                $GesioneUtenti->accettaRichiestaAmicizia($_SESSION['username'], $usernameAmico);

                $this->mostraRichiesteAmicizia();

            }else{
                $_SESSION["VerificaAmico"] = "Username non valido";
            }

        }
    }

    public function mostraAmicizie(){
        require_once "./application/controller/autenticazione.php";

        $autenticazione = new autenticazione();

        if($autenticazione->controlloLogin()) {

            $GesioneUtenti = new \models\GestioneUtenti();

            $amici = $GesioneUtenti->MostraAmicizia($_SESSION['username']);

            require './application/views/templates/header.php';
            require 'application/views/Amici/index.php';
            require './application/views/templates/footer.php';

        }
    }

    public function rifiutaRichiestaAmicizia()
    {
        require_once "./application/controller/autenticazione.php";

        $autenticazione = new autenticazione();

        if($autenticazione->controlloLogin()){
            $regexUsername = '/^[a-zA-Z0-9]{1,20}$/';
            $usernameAmico = $_POST['username'];
            if(preg_match($regexUsername, $_POST['username'])){
                $GesioneUtenti = new \models\GestioneUtenti();

                $GesioneUtenti->rifiutaRichiestaAmicizia($_SESSION['username'], $usernameAmico);
                $this->mostraRichiesteAmicizia();
            }else{
                $_SESSION["VerificaAmico"] = "Username non valido";
            }

        }

    }

    public function elliminaAmicizia()
    {
        require_once "./application/controller/autenticazione.php";
        require_once "./application/controller/home.php";

        $autenticazione = new autenticazione();

        if($autenticazione->controlloLogin()) { // Aggiunto controllo login
            $regexUsername = '/^[a-zA-Z0-9]{1,20}$/';
            $usernameAmico = $_POST['username'];

            if(preg_match($regexUsername, $usernameAmico)) { // Aggiunta validazione
                $GesioneUtenti = new \models\GestioneUtenti();
                $GesioneUtenti->eliminaAmicizia($_SESSION['username'], $usernameAmico);
            } else {
                $_SESSION["VerificaAmico"] = "Username non valido";
            }
        }

        $home = new home();
        $home->index();
    }

}