<?php
class Board
{
    public function index()
    {
        require_once './application/views/tabellone/index.php';
    }

    public function generaNumeroDati() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $Dado1 = rand(1, 6);
        $Dado2 = rand(1, 6);
        $_SESSION['Dado1'] = $Dado1;
        $_SESSION['Dado2'] = $Dado2;

        header('Content-Type: application/json');
        echo json_encode(['dado1' => $Dado1, 'dado2' => $Dado2]);
    }
}