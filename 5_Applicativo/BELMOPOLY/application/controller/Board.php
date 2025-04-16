<?php

use libs\Database;

class Board
{
    public function index()
    {
        require_once './application/views/tabellone/index.php';
    }

    public function generaNumeroDati()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $Dado1 = rand(1, 1);
        $Dado2 = rand(1, 1);
        $_SESSION['Dado1'] = $Dado1;
        $_SESSION['Dado2'] = $Dado2;

        header('Content-Type: application/json');
        echo json_encode(['dado1' => $Dado1, 'dado2' => $Dado2]);
    }

    public function pescaCarta()
    {
        $tipo = $_GET['tipo'] ?? null;

        if ($tipo !== 'probabilita' && $tipo !== 'imprevisti') {
            echo json_encode(["errore" => "Tipo non valido."]);
            return;
        }

        try {
            $db = Database::getConnection();
            $id = rand(4,4);

            $stmt = $db->prepare("SELECT descrizione FROM $tipo WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $carta = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($carta) {
                echo json_encode([
                    "id" => $id,
                    "descrizione" => $carta['descrizione']
                ]);
            } else {
                echo json_encode(["errore" => "Carta non trovata."]);
            }
        } catch (PDOException $e) {
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

}