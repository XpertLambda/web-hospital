<?php
class Database{
 
    // specify your own database credentials
    private $host = "localhost";
    private $db_name = "hospital_db";
    private $username = "root";
    private $password = "NewPassword";
    public $conn;
 
    // get the database connection
    public function getConnection(){
 
        $this->conn = null;
 
        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        }catch(PDOException $exception){
            // Log the error for system admins
            error_log("Database Connection Error: " . $exception->getMessage());
            // Return null for the application to handle
            return null;
        }
    }
}