<?php
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

            $amici = $GesioneUtenti->MostraAmicizia($_SESSION['username']);

            require_once "./application/views/creazioneRoom/index.php";

            $creaRoom = new \models\GestioneRoom();

            $creaRoom->creaRoom($_SESSION['username']);

            header("Location: ". URL  ."home/creaRoomView");
            exit();


        }
    }

    public function creaRoomView()
    {


        require_once "./application/controller/autenticazione.php";
        $autenticazione = new autenticazione();

        if($autenticazione->controlloLogin()) {

            $GesioneUtenti = new \models\GestioneUtenti();

            $amici = $GesioneUtenti->MostraAmicizia($_SESSION['username']);

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

    public function invitaRoom($username)
    {

        require_once "./application/controller/autenticazione.php";
        $autenticazione = new autenticazione();

        if ($autenticazione->controlloLogin()) {

            $creaRoom = new \models\GestioneRoom();

            $creaRoom->invitaAmicoRoom($username);

            header("Location: " . URL . "home/creaRoomView");
            exit();

        }


    }
    public function accettaInvitoRoom($username,$UUID){

        require_once "./application/controller/autenticazione.php";
        $autenticazione = new autenticazione();

        if ($autenticazione->controlloLogin()) {

            $creaRoom = new \models\GestioneRoom();


            header("Location: " . URL . "home/creaRoomView");
            exit();

        }

    }






}