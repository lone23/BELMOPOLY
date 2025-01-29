<?php

class home
{
    public function index(){
        require './application/views/templates/header.php';
        require 'application/views/home/index.php';
        require  './application/views/templates/footer.php';
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
        require_once './application/models/UserMapper.php';

        $email = $_POST['email'];
        $password = $_POST['password'];

        $personMapper = new UserMapper();

        if($personMapper->verificaLogin($email,$password)){

            $this->index();
        }else{

            $this->login();
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
}