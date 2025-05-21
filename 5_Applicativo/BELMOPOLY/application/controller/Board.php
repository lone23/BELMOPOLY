<?php

use libs\Database;
use models\GestioneRoom;

class Board
{
    public function index()
    {
        require_once "./application/controller/autenticazione.php";
        $autenticazione = new autenticazione();
        if($autenticazione->controlloLogin()) {

            require_once './application/views/tabellone/index.php';
        }
        echo $_SESSION['username'];
    }

    public function generaNumeroDati()
    {
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

    public function pescaCarta()
    {
        $tipo = $_GET['tipo'] ?? null;

        if ($tipo !== 'probabilita' && $tipo !== 'imprevisti') {
            http_response_code(400);
            echo json_encode(["errore" => "Tipo non valido."]);
            return;
        }

        try {
            $db = Database::getConnection();
            $id = rand(1,8);

            $stmt = $db->prepare("SELECT * FROM $tipo WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $carta = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($carta) {
                header('Content-Type: application/json');
                echo json_encode($carta);
            } else {
                http_response_code(404);
                echo json_encode(["errore" => "Carta non trovata."]);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["errore" => "Errore di connessione al database."]);
        }
    }

    public function pescaCartaSpeciale() {
        $id = $_GET['id'] ?? null;

        if (!is_numeric($id)) {
            http_response_code(400);
            echo json_encode(["errore" => "ID non valido."]);
            return;
        }

        try {
            $db = Database::getConnection();

            $stmt = $db->prepare("SELECT id, nome, prezzo, affitto1, affitto2, affitto3, affitto4 FROM proprietaSpeciali WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $carta = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($carta) {
                header('Content-Type: application/json');
                echo json_encode($carta);
            } else {
                http_response_code(404);
                echo json_encode(["errore" => "Carta speciale non trovata."]);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["errore" => "Errore di connessione al database."]);
        }
    }

    public function pescaCartaNormale() {
        $id = $_GET['id'] ?? null;

        if (!is_numeric($id)) {
            http_response_code(400);
            echo json_encode(["errore" => "ID non valido."]);
            return;
        }

        try {
            $db = Database::getConnection();

            $stmt = $db->prepare(" SELECT id, nome, prezzo, affitto, affittoCompleto, affittoCasa1, affittoCasa2, affittoCasa3, affittoCasa4, affittoAlbergo, costoCasa, costoAlbergo FROM proprietaNormali WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $carta = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($carta) {
                header('Content-Type: application/json');
                echo json_encode($carta);  // Restituisci i dettagli della carta in formato JSON
            } else {
                http_response_code(404);
                echo json_encode(["errore" => "Carta normale non trovata."]);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["errore" => "Errore di connessione al database."]);
        }
    }

    public function aggiornaSaldo(){
        $GestionePartita = new \models\GestionePartita();
        $giocatori = $GestionePartita->getSaldo();
        header('Content-Type: application/json');
        echo json_encode($giocatori);
    }

    public function salvaSaldo(){
        $input = json_decode(file_get_contents('php://input'), true);

        if (isset($input['price'])) {
            $price = $input['price'];

            $GestionePartita = new \models\GestionePartita();
            $response = $GestionePartita->setSaldo($price);

            header('Content-Type: application/json');
            echo json_encode(['successo' => $response]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['successo' => false, 'errore' => 'Valore mancante']);
        }
    }

    public function compraProprieta() {
        $input = json_decode(file_get_contents('php://input'), true);

        \libs\Logger::log("INFO -> mandato input " . $input['price'] . " " . $input['idProprieta']);

        if (!isset($input['price']) || !isset($input['idProprieta'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Valori mancanti: price e/o idProprieta'
            ]);
            return;
        }

        $price = intval($input['price']);
        $idProprieta = intval($input['idProprieta']);

        $GestionePartita = new \models\GestionePartita();

        // 1. Prova ad acquistare la proprietà
        $acquisto = $GestionePartita->acquistaProprieta($idProprieta);

        if ($acquisto === false) {
            echo json_encode([
                'success' => false,
                'error' => 'Acquisto proprietà fallito'
            ]);
            exit;
        }
        // Se la proprietà è già posseduta
        if (isset($acquisto['gia_posseduta']) && $acquisto['gia_posseduta'] === true) {
            echo json_encode([
                'success' => false,
                'error' => 'Proprietà già acquistata da un altro giocatore',
                'id_utente' => $acquisto['id_utente'],
                'posseduta' => true,
                'pagamento' => isset($acquisto['pagamento']) ? $acquisto['pagamento'] : null
            ]);
            \libs\Logger::log("INFO -> PROPRIETA GIA POSSEDUTA CONTROLLER");
            exit;
        }

        // Se l'acquisto non ha avuto successo per altri motivi
        if (!$acquisto['success']) {
            echo json_encode([
                'success' => false,
                'error' => 'Acquisto proprietà fallito per motivi sconosciuti'
            ]);
            exit;
        }

        // 2. Se acquisto OK, scala il saldo
        $saldoAggiornato = $GestionePartita->setSaldo($price);

        if (!$saldoAggiornato) {
            echo json_encode([
                'success' => false,
                'error' => 'Saldo non aggiornato'
            ]);
            return;
        }

        // Tutto OK
        echo json_encode(['success' => true]);
    }
    public function numeroGiocatori() {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $uuid = $data["uuid"];
        $GestioneRoom = new GestioneRoom();
        $numeroGiocatori = $GestioneRoom->numeroGiocatori($uuid);
        $response = $numeroGiocatori;
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function salvaPosizionePedina()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $posizione = $data["posizione"];
        $GestioneRoom = new GestioneRoom();
        $GestioneRoom->salvaPosizionePedina($posizione);
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    }


    public function prendiPosizionePedina()
    {
        $GestioneRoom = new GestioneRoom();
        $posizioni = $GestioneRoom->prendiPosizionePedina();

        header('Content-Type: application/json');
        echo json_encode($posizioni);
    }
}