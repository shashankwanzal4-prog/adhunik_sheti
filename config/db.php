<?php
/**
 * Database Configuration for Adhunik Krushi Bhandar
 * Establishes connection to MySQL database
 */

class Database {
    private $host = 'localhost';
    private $username = 'root';
    private $password = '';
    private $database = 'krushi_bhandar';
    public $conn;

    public function __construct() {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);
        
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
        
        // Set charset to utf8mb4 for proper encoding
        $this->conn->set_charset("utf8mb4");
    }

    public function query($sql) {
        return $this->conn->query($sql);
    }

    public function escape($string) {
        return $this->conn->real_escape_string($string);
    }

    public function insert_id() {
        return $this->conn->insert_id;
    }

    public function prepare($sql) {
        return $this->conn->prepare($sql);
    }

    public function close() {
        $this->conn->close();
    }
}

// Initialize database connection
$db = new Database();
?>
