<?php
class Patient{
    private $conn;
    private $table_name = "patients";
 
    public $id;
    public $name;
    public $phone;
    public $gender;
    public $health_condition;
    public $doctor_id;
    public $nurse_id;
    public $created;
 
    public function __construct($db){
        $this->conn = $db;
    }

    function read(){
        $query = "SELECT p.*, d.name as doctor_name, n.name as nurse_name 
                FROM " . $this->table_name . " p
                LEFT JOIN doctors d ON p.doctor_id = d.id
                LEFT JOIN nurses n ON p.nurse_id = n.id
                ORDER BY p.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    function read_single(){
        $query = "SELECT p.*, d.name as doctor_name, n.name as nurse_name 
                FROM " . $this->table_name . " p
                LEFT JOIN doctors d ON p.doctor_id = d.id
                LEFT JOIN nurses n ON p.nurse_id = n.id
                WHERE p.id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        return $stmt;
    }

    function create(){
        $query = "INSERT INTO " . $this->table_name . "
                SET name=:name, 
                    phone=:phone,
                    gender=:gender,
                    health_condition=:health_condition,
                    doctor_id=:doctor_id,
                    nurse_id=:nurse_id";

        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":gender", $this->gender);
        $stmt->bindParam(":health_condition", $this->health_condition);
        $stmt->bindParam(":doctor_id", $this->doctor_id);
        $stmt->bindParam(":nurse_id", $this->nurse_id);

        if($stmt->execute()){
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    function update(){
        $query = "UPDATE " . $this->table_name . "
                SET name=:name,
                    phone=:phone,
                    gender=:gender,
                    health_condition=:health_condition,
                    doctor_id=:doctor_id,
                    nurse_id=:nurse_id
                WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':gender', $this->gender);
        $stmt->bindParam(':health_condition', $this->health_condition);
        $stmt->bindParam(':doctor_id', $this->doctor_id);
        $stmt->bindParam(':nurse_id', $this->nurse_id);

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

    // Read patients assigned to a specific doctor
    public function readByDoctor($doctorId) {
        $query = "SELECT p.id, u.name, u.phone, u.gender, p.health_condition, 
                         p.doctor_id, doc.name as doctor_name, 
                         p.nurse_id, n.name as nurse_name 
                  FROM " . $this->table_name . " p
                  JOIN users u ON p.user_id = u.id
                  JOIN users doc ON p.doctor_id = doc.id
                  JOIN users n ON p.nurse_id = n.id
                  WHERE p.doctor_id = :doctor_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':doctor_id', $doctorId);
        $stmt->execute();
        
        return $stmt;
    }

    // Read patients assigned to a specific nurse
    public function readByNurse($nurseId) {
        $query = "SELECT p.id, u.name, u.phone, u.gender, p.health_condition, 
                         p.doctor_id, doc.name as doctor_name, 
                         p.nurse_id, n.name as nurse_name 
                  FROM " . $this->table_name . " p
                  JOIN users u ON p.user_id = u.id
                  JOIN users doc ON p.doctor_id = doc.id
                  JOIN users n ON p.nurse_id = n.id
                  WHERE p.nurse_id = :nurse_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nurse_id', $nurseId);
        $stmt->execute();
        
        return $stmt;
    }

    // Read a single patient (used for patients to see their own info)
    public function readSingle($userId) {
        $query = "SELECT p.id, u.name, u.phone, u.gender, p.health_condition, 
                         p.doctor_id, doc.name as doctor_name, 
                         p.nurse_id, n.name as nurse_name 
                  FROM " . $this->table_name . " p
                  JOIN users u ON p.user_id = u.id
                  JOIN users doc ON p.doctor_id = doc.id
                  JOIN users n ON p.nurse_id = n.id
                  WHERE p.user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return $stmt;
    }
}
