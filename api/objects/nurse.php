<?php
class Nurse{
    private $conn;
    private $table_name = "nurses";
 
    public $id;
    public $name;
    public $email;
    public $password;
    public $phone;
    public $created;
 
    public function __construct($db){
        $this->conn = $db;
    }

    function read(){
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    function read_single(){
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        return $stmt;
    }

    function create(){
        if($this->isAlreadyExist()){
            return false;
        }
        
        $query = "INSERT INTO " . $this->table_name . " 
                SET name=:name, email=:email, password=:password, phone=:phone";

        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":phone", $this->phone);

        if($stmt->execute()){
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    function update(){
        $query = "UPDATE " . $this->table_name . "
                SET name = :name,
                    email = :email,
                    password = :password,
                    phone = :phone 
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':phone', $this->phone);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    function delete(){
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        
        if($stmt->execute()){
            return true;
        }
        return false;
    }

    function isAlreadyExist(){
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Read a single nurse (used for nurses to see their own info)
    public function readSingle($id) {
        $query = "SELECT n.id, n.name, n.email, n.phone 
                  FROM " . $this->table_name . " n 
                  WHERE n.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt;
    }

    // Read nurse assigned to a specific patient
    public function readPatientNurse($patientId) {
        $query = "SELECT n.id, n.name, n.email, n.phone 
                  FROM " . $this->table_name . " n 
                  JOIN patients p ON p.nurse_id = n.id 
                  WHERE p.user_id = :patient_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patient_id', $patientId);
        $stmt->execute();
        
        return $stmt;
    }

    // Read nurses working with a specific doctor's patients
    public function readByDoctor($doctorId) {
        $query = "SELECT DISTINCT n.id, n.name, n.email, n.phone 
                  FROM " . $this->table_name . " n 
                  JOIN patients p ON p.nurse_id = n.id 
                  WHERE p.doctor_id = :doctor_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':doctor_id', $doctorId);
        $stmt->execute();
        
        return $stmt;
    }
}
