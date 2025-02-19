<?php
class MappaturaErrori
{

    public function ConnectionErrorDatabase(){

        require 'application/views/error/ConnectionDatabase.php';

    }

    public function ErrorPage404()
    {
        require 'application/views/error/404.php';
    }

}