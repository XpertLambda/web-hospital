<?php
class Doctor{
 
    // database connection and table name
    private $conn;
    private $table_name = "doctors";
 
    // object properties
    public $id;
    public $name;
    public $email;
    public $password;
    public $phone;
    public $gender;
    public $specialist;
    public $created;
 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    // read all doctors
    function read(){
    
        // select all query
        $query = "SELECT
                    `id`, `name`, `email`, `password`, `phone`, `gender`, `specialist`, `created`
                FROM
                    " . $this->table_name . " 
                ORDER BY
                    id DESC";
    
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // execute query
        $stmt->execute();
    
        return $stmt;
    }

    // get single doctor data
    function read_single(){
    
        // select all query
        $query = "SELECT
                    `id`, `name`, `email`, `password`, `phone`, `gender`, `specialist`, `created`
                FROM
                    " . $this->table_name . " 
                WHERE
                    id= '".$this->id."'";
    
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // execute query
        $stmt->execute();
        return $stmt;
    }

    // create doctor
    function create(){
    
        if($this->isAlreadyExist()){
            return false;
        }
        
        // query to insert record
        $query = "INSERT INTO  ". $this->table_name ." 
                        (`name`, `email`, `password`, `phone`, `gender`, `specialist`, `created`)
                  VALUES
                        ('".$this->name."', '".$this->email."', '".$this->password."', '".$this->phone."', '".$this->gender."', '".$this->specialist."', '".$this->created."')";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
    
        // execute query
        if($stmt->execute()){
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    // update doctor 
    function update(){
    
        // query to insert record
        $query = "UPDATE
                    " . $this->table_name . "
                SET
                    name='".$this->name."', email='".$this->email."', password='".$this->password."', phone='".$this->phone."', gender='".$this->gender."', specialist='".$this->specialist."'
                WHERE
                    id='".$this->id."'";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
        // execute query
        if($stmt->execute()){
            return true;
        }
        return false;
    }

    // delete doctor
    function delete(){
        
        // query to insert record
        $query = "DELETE FROM
                    " . $this->table_name . "
                WHERE
                    id= '".$this->id."'";
        
        // prepare query
        $stmt = $this->conn->prepare($query);
        
        // execute query
        if($stmt->execute()){
            return true;
        }
        return false;
    }

    function isAlreadyExist(){
        $query = "SELECT *
            FROM
                " . $this->table_name . " 
            WHERE
                email='".$this->email."'";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // execute query
        $stmt->execute();

        if($stmt->rowCount() > 0){
            return true;
        }
        else{
            return false;
        }
    }

    // Read a single doctor (used for doctors to see their own info)
    public function readSingle($id) {
        $query = "SELECT d.id, d.name, d.email, d.phone, d.gender, d.specialist 
                  FROM " . $this->table_name . " d 
                  WHERE d.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt;
    }

    // Read doctor assigned to a specific patient
    public function readPatientDoctor($patientId) {
        $query = "SELECT d.id, d.name, d.email, d.phone, d.gender, d.specialist 
                  FROM " . $this->table_name . " d 
                  JOIN patients p ON p.doctor_id = d.id 
                  WHERE p.user_id = :patient_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patient_id', $patientId);
        $stmt->execute();
        
        return $stmt;
    }
}