<?php
include 'config.php';

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);
class Database {
    private $conn;
    
    public function __construct() {
        $servername = "127.0.0.1"; // Use localhost or 127.0.0.1 since it's on the host machine
        $port = 3306; // Use the port number you need
        $username = "admin";
        $password = "admin12345";
        $database = "bmwa";
        try {
            $this->conn = new PDO("mysql:host=$servername;port=$port;dbname=$database", $username, $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function close() {
        $this->conn = null;
    }


    public function query($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function getLastInsertedId() {
        return $this->conn->lastInsertId();
    }

}


