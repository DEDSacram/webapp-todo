<?php
include 'config.php';

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);
class Database {
    private $conn;
    
    public function __construct() {
        try {

            $this->conn = new PDO("mysql:host=" . DB_SERVER . ";port=" . DB_PORT . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
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
    public function beginTransaction() {
        $this->conn->beginTransaction();
    }

    public function commit() {
        $this->conn->commit();
    }

}


