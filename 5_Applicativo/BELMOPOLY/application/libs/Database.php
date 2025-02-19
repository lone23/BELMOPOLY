<?php

namespace libs;
class Database{
    
    private static $conn;

    public static function getConnection() {

        if (isset(self::$conn)) {
            return self::$conn;
        } else {
            try{
                self::$conn = new \PDO('mysql: = '.HOST.'; dbname='.DATABASE.'; port='.PORT,
                    USER, PASSWORD);
                self::$conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            }
            catch (\PDOException $e){
                require_once './application/controller/MappaturaErrori.php';

                Logger::log("ERROR -> Database errore di connessione: " . $e->getMessage());

                $MappaturaErrori = new \MappaturaErrori();
                $MappaturaErrori->ConnectionErrorDatabase();
                die();

            }
            return self::$conn;
        }
    }
    





}