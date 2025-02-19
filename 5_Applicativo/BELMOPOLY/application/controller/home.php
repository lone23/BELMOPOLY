<?php

class home
{

    public function controlloLogin(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if(!isset($_SESSION["isAuthenticated"])){

            $autenticazione = new autenticazione();

            $autenticazione->login();
            return false;
        }else{
            return true;
        }
    }


    public function index(){

    require_once './application/controller/autenticazione.php';

        if($this->controlloLogin()) {
            require './application/views/templates/header.php';
            require 'application/views/home/index.php';
            require './application/views/templates/footer.php';
        }
    }

    public function prova(){
        require 'application/views/MainPage/index.php';

    }



}