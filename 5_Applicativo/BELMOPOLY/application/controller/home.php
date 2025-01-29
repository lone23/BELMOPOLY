<?php

class home
{
    public function index(){
        if($this->controlloLogin()) {
            require './application/views/templates/header.php';
            require 'application/views/home/index.php';
            require './application/views/templates/footer.php';
        }
    }


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

        require_once './application/models/UserMapper.php';

        $email = $_POST['email'];
        $password = $_POST['password'];



        if(empty($email) or empty($password)){
            $_SESSION["ControlloLogin"] = "Inserisci tutti i campi";
            $this->login();
        }else{

            $userMapper = new UserMapper();

            if($userMapper->verificaLogin($email,$password)){

                $this->index();
            }else{

                $this->login();
            }

        }

    }



    public function lista(){
        if($this->controlloLogin()){

            require_once 'application/models/UserMapper.php';
            $userMapper = new UserMapper();
            $users = $userMapper->fetchAll();

            require './application/views/templates/header.php';
            require 'application/views/lista/index.php';
            require  './application/views/templates/footer.php';
        }
    }


    public function RegistraUtente()
    {

        $email = $_POST['email'];
        $username = $_POST['username'];
        $password = $_POST['password'];


        if(empty($email) or empty($username) or empty($password)){
            $_SESSION["ControlloRegister"] = "Inserisci tutti i campi";
        }




    }
}