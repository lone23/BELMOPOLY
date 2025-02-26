<?php
class home
{



    public function index(){
    require_once "./application/controller/autenticazione.php";
    require_once "./application/controller/GestioneAccount.php";
        $autenticazione = new autenticazione();

        if($autenticazione->controlloLogin()) {

            $GesionteAccount = new GestioneAccount();
            $GesionteAccount->mostraRichiesteAmicizia();

        }
    }




}