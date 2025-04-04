<?php
class Database{
 
    // specify your own database credentials
    private $host = "localhost";
    private $db_name = "hospital_db";
    private $username = "root";
    private $password = "ghali";
    public $conn;
 
    // get the database connection
    public function getConnection(){
 
        $this->conn = null;
 
        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            return $this->conn; // Move return inside try block
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
            return null; // Return null on error
        }
    }
}