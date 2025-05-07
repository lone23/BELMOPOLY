<?php

use WebSocket\Client;


class home
{



    public function index(){
    require_once "./application/controller/autenticazione.php";
    require_once "./application/controller/GestioneAccount.php";
        $autenticazione = new autenticazione();

        if($autenticazione->controlloLogin()) {

            require_once "./application/views/home/index.php";


        }
    }

    public function creaRoom()
    {



        require_once "./application/controller/autenticazione.php";
        $autenticazione = new autenticazione();

        if($autenticazione->controlloLogin()) {

            $GesioneUtenti = new \models\GestioneUtenti();

            $amici = $GesioneUtenti->MostraAmicizia('%',$_SESSION['username']);

            require_once "./application/views/creazioneRoom/index.php";

            $creaRoom = new \models\GestioneRoom();

            $creaRoom->creaRoom($_SESSION['username']);

            header("Location:" . URL . "home/creaRoomView");

        }
    }

    public function creaRoomView()
    {

        require_once "./application/controller/autenticazione.php";
        $autenticazione = new autenticazione();

        if($autenticazione->controlloLogin()) {

            $GesioneUtenti = new \models\GestioneUtenti();

            $amici = $GesioneUtenti->MostraAmicizia("%",$_SESSION['username']);

            require_once "./application/views/creazioneRoom/index.php";


        }

    }

    public function esciRoom(){

        require_once "./application/controller/autenticazione.php";
        $autenticazione = new autenticazione();

        if($autenticazione->controlloLogin()) {

            $creaRoom = new \models\GestioneRoom();

            $creaRoom->elliminaRoom($_SESSION['username']);

            $this->index();

        }


    }

    public function invitaRoom($username = null)
    {

        require_once "./application/controller/autenticazione.php";
        $autenticazione = new autenticazione();

        if($autenticazione->controlloLogin()) {

            $creaRoom = new \models\GestioneRoom();

            $creaRoom->invitaAmicoRoom($username);

            header("Location:" . URL . "home/creaRoomView");

        }

    }

    public function mostraRichiesteRoom()
    {

        require_once "./application/controller/autenticazione.php";
        $autenticazione = new autenticazione();

        if($autenticazione->controlloLogin()) {

            $creaRoom = new \models\GestioneRoom();

            $users = $creaRoom->getInvitiConUsernameCapo($_SESSION['username']);



            require_once "./application/views/notifhce/invites.php";

        }

    }

    public function accettaRichiesteRoom()
    {

        require_once "./application/controller/autenticazione.php";
        $autenticazione = new autenticazione();

        if($autenticazione->controlloLogin()) {

            $creaRoom = new \models\GestioneRoom();

            $creaRoom->accettaInvito($_SESSION['username']);

            $this->creaRoomView();

        }

    }

    public function elliminaInvitoRoom($capoPartita = null)
    {

        require_once "./application/controller/autenticazione.php";
        $autenticazione = new autenticazione();

        if($autenticazione->controlloLogin()) {

            $creaRoom = new \models\GestioneRoom();

            $creaRoom->eliminaInvitoGiocatore($capoPartita,$_SESSION['username']);

            $this->mostraRichiesteRoom();

        }

    }

    public function isStartGame()
    {

        require_once "./application/controller/autenticazione.php";
        $autenticazione = new autenticazione();

        if($autenticazione->controlloLogin()) {

            header('Content-Type: application/json');
            $gestioneRoom = new \models\GestioneRoom();
            $response = $gestioneRoom->isStartGame($_SESSION['username']);

        }



    }
// startGame.php

    public function startGame()
    {
        require_once "./application/controller/autenticazione.php";
        $autenticazione = new autenticazione();

        if($autenticazione->controlloLogin()) {

            $creaRoom = new \models\GestioneRoom();
            $creaRoom->startGame(); // aggiorna il DB

            // 🔥 Comunica via WebSocket al server Node.js
            $uuid = $_SESSION['uuid'];

            // Creazione della connessione al WebSocket
            $client = new Client("ws://localhost:3000");

            // Invia messaggio per iniziare il gioco nella stanza
            $client->send(json_encode([
                'startGame' => true,
                'room' => $uuid,
                'startingIndex' => 0
            ]));

            $client->close();

            // Redirige l'utente alla pagina del gioco
            header("Location:" . URL . "board/index");
        }
    }






}