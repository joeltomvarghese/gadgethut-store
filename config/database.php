<?php
class Database {
    private $host = "localhost";
    private $db_name = "gadgethut_store";
    private $username = "root";
    private $password = "";  // Empty for XAMPP
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            error_log("Database connection failed: " . $exception->getMessage());
            echo json_encode(["error" => "Database connection failed"]);
            exit;
        }
        return $this->conn;
    }
}
?>