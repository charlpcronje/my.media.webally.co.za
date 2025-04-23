<?php
// Database utility class
class Database {
    private $conn;
    public function __construct($host, $user, $pass, $name) {
        $this->conn = new mysqli($host, $user, $pass, $name);
        if ($this->conn->connect_error) {
            $this->logError($this->conn->connect_error);
            die('Database connection failed');
        }
    }
    public function query($sql) {
        return $this->conn->query($sql);
    }
    public function prepare($sql) {
        return $this->conn->prepare($sql);
    }
    public function getConnection() {
        return $this->conn;
    }
    private function logError($error) {
        error_log($error, 3, __DIR__.'/../logs/db_errors.log');
    }
}
