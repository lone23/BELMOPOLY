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

        if (!isset($input['idProprieta'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'ID proprietà mancante']);
            return;
        }
        \libs\Logger::log("INFO -> DATI MANDATI: " . $input['idProprieta']);
        $idProprieta = intval($input['idProprieta']);

        $GestionePartita = new \models\GestionePartita();

        // Controlla se l'utente può comprare la proprietà (es. ha soldi, proprietà libera, ecc)
        // Qui puoi aggiungere ulteriori controlli a piacere

        $acquisto = $GestionePartita->acquistaProprieta($idProprieta);

        if ($acquisto) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Acquisto fallito']);
        }
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

    public function controlloProprieta()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $GestioneParita = new \models\GestionePartita();
        $infoCarta = $GestioneParita->proprietaPosseduta($data["idNumerico"]);
        $response = $infoCarta;
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function pagaAffitto()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        $uuid = $data['uuid'];
        $playerId = $data['playerId'];
        $idProprieta = $data['idProprieta'];

        $GestionePartita = new \models\GestionePartita();

        $affitto = $GestionePartita->getAffittoBase($idProprieta);

        $proprietarioId = $GestionePartita->getGiocatore($uuid, $idProprieta);


        $GestionePartita->aggiornaSaldoAffitto($uuid, $playerId ,$proprietarioId, $affitto);

        echo json_encode(["controllo" => "success"]);
    }


}