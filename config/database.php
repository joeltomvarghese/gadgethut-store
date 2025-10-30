<?php
class Database {
    private $host = "localhost";
    private $db_name = "gadgethut_store";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name, 
                $this->username, 
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conn->exec("set names utf8mb4");
            
        } catch(PDOException $exception) {
            error_log("Database connection failed: " . $exception->getMessage());
            echo json_encode([
                "error" => "Database connection failed", 
                "message" => $exception->getMessage()
            ]);
            exit;
        }
        return $this->conn;
    }
}
?>