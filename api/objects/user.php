<?php
class User {
    // database connection and table name
    private $conn;
    private $table_name = "users";
 
    // object properties
    public $id;
    public $name;
    public $email;
    public $password;
    public $phone;
    public $role;
    public $role_id;
    public $gender;
    public $specialist;
    public $health_condition;
    public $email_verified;
    public $verification_token;
    public $reset_token;
    public $reset_token_expiry;
    public $mfa_enabled;
    public $mfa_secret;
    public $created;
    public $last_login;
 
    // constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // read all users (with pagination)
    function read($from = 0, $limit = 10, $role = null) {
        // select query with role filter if provided
        $query = "SELECT 
                    u.id, u.name, u.email, u.phone, u.role_id, r.name as role, 
                    u.gender, u.specialist, u.health_condition,
                    u.email_verified, u.mfa_enabled, u.created, u.last_login
                FROM 
                    " . $this->table_name . " u
                JOIN
                    roles r ON u.role_id = r.id";
        
        // Add role filter if provided
        if ($role !== null) {
            $query .= " WHERE r.name = :role";
        }
        
        $query .= " ORDER BY u.id DESC LIMIT :from, :limit";
    
        // prepare query statement
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        if ($role !== null) {
            $stmt->bindParam(':role', $role);
        }
        
        $stmt->bindParam(':from', $from, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    
        // execute query
        $stmt->execute();
    
        return $stmt;
    }

    // get single user data
    function readOne() {
        // query to read single record
        $query = "SELECT 
                    u.id, u.name, u.email, u.phone, u.role_id, r.name as role, 
                    u.gender, u.specialist, u.health_condition,
                    u.email_verified, u.mfa_enabled, u.created, u.last_login
                FROM 
                    " . $this->table_name . " u
                JOIN
                    roles r ON u.role_id = r.id
                WHERE
                    u.id = :id";
    
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // bind id of record to get
        $stmt->bindParam(":id", $this->id);
    
        // execute query
        $stmt->execute();
    
        // get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // set values to object properties
        if ($row) {
            $this->name = $row['name'];
            $this->email = $row['email'];
            $this->phone = $row['phone'];
            $this->role_id = $row['role_id'];
            $this->role = $row['role'];
            $this->gender = $row['gender'];
            $this->specialist = $row['specialist'];
            $this->health_condition = $row['health_condition'];
            $this->email_verified = $row['email_verified'];
            $this->mfa_enabled = $row['mfa_enabled'];
            $this->created = $row['created'];
            $this->last_login = $row['last_login'];
            return true;
        }
        
        return false;
    }

    // create user
    function create() {
        // check if email already exists
        if ($this->emailExists()) {
            return false;
        }
        
        // query to insert record
        $query = "INSERT INTO " . $this->table_name . "
                (name, email, password, phone, role_id, gender, specialist, health_condition, verification_token, created)
                VALUES
                (:name, :email, :password, :phone, :role_id, :gender, :specialist, :health_condition, :verification_token, :created)";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        
        // hash the password
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        
        // generate verification token
        $this->verification_token = bin2hex(random_bytes(32));
        
        // get current timestamp
        $this->created = date('Y-m-d H:i:s');
    
        // bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $password_hash);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":role_id", $this->role_id);
        $stmt->bindParam(":gender", $this->gender);
        $stmt->bindParam(":specialist", $this->specialist);
        $stmt->bindParam(":health_condition", $this->health_condition);
        $stmt->bindParam(":verification_token", $this->verification_token);
        $stmt->bindParam(":created", $this->created);
    
        // execute query
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
    
        return false;
    }

    // update user
    function update() {
        // query to update record
        $query = "UPDATE " . $this->table_name . "
                SET
                    name = :name,
                    email = :email,
                    phone = :phone";
        
        // if password was provided
        if (!empty($this->password)) {
            $query .= ", password = :password";
        }
        
        $query .= ", role_id = :role_id";
        
        // Add role-specific fields if provided
        if ($this->role == 'doctor' && !empty($this->specialist)) {
            $query .= ", specialist = :specialist";
        }
        
        if (($this->role == 'doctor' || $this->role == 'patient') && isset($this->gender)) {
            $query .= ", gender = :gender";
        }
        
        if ($this->role == 'patient' && !empty($this->health_condition)) {
            $query .= ", health_condition = :health_condition";
        }
        
        $query .= " WHERE id = :id";
    
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->id = htmlspecialchars(strip_tags($this->id));
    
        // bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":role_id", $this->role_id);
        $stmt->bindParam(":id", $this->id);
    
        // hash the password if it was provided
        if (!empty($this->password)) {
            $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
            $stmt->bindParam(":password", $password_hash);
        }
        
        // Bind role-specific fields if provided
        if ($this->role == 'doctor' && !empty($this->specialist)) {
            $stmt->bindParam(":specialist", $this->specialist);
        }
        
        if (($this->role == 'doctor' || $this->role == 'patient') && isset($this->gender)) {
            $stmt->bindParam(":gender", $this->gender);
        }
        
        if ($this->role == 'patient' && !empty($this->health_condition)) {
            $stmt->bindParam(":health_condition", $this->health_condition);
        }
    
        // execute the query
        if ($stmt->execute()) {
            return true;
        }
    
        return false;
    }

    // delete user
    function delete() {
        // query to delete user
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->id = htmlspecialchars(strip_tags($this->id));
    
        // bind id of record to delete
        $stmt->bindParam(":id", $this->id);
    
        // execute query
        if ($stmt->execute()) {
            return true;
        }
    
        return false;
    }

    // email exists?
    function emailExists() {
        // query to check if email exists
        $query = "SELECT u.id, u.name, u.password, u.role_id, r.name as role
                FROM " . $this->table_name . " u
                JOIN roles r ON u.role_id = r.id
                WHERE u.email = :email
                LIMIT 0,1";
    
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->email = htmlspecialchars(strip_tags($this->email));
    
        // bind email value
        $stmt->bindParam(":email", $this->email);
    
        // execute query
        $stmt->execute();
    
        // get number of rows
        $num = $stmt->rowCount();
    
        // if email exists, assign values to object properties for easy access and use for php sessions
        if ($num > 0) {
            // get record details / values
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
            // assign values to object properties
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->password = $row['password'];
            $this->role_id = $row['role_id'];
            $this->role = $row['role'];
    
            // return true because email exists in the database
            return true;
        }
    
        // return false if email does not exist in the database
        return false;
    }

    // login user
    function login() {
        // Store plaintext password before it gets overwritten
        $plain_password = $this->password;
        
        // if email exists, check if password is correct
        if ($this->emailExists()) {
            // check if user is verified
            $query = "SELECT email_verified FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $this->id);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // verify password - use the plaintext password against stored hash
            if (password_verify($plain_password, $this->password)) {
                // check if MFA is enabled
                $query = "SELECT mfa_enabled, mfa_secret FROM " . $this->table_name . " WHERE id = :id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':id', $this->id);
                $stmt->execute();
                
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($row['mfa_enabled'] == 1) {
                    // store user data in session for MFA verification
                    $_SESSION['mfa_user_id'] = $this->id;
                    $this->mfa_enabled = true;
                    $this->mfa_secret = $row['mfa_secret'];
                    return "mfa_required";
                }
                
                // update last login time
                $query = "UPDATE " . $this->table_name . " SET last_login = :last_login WHERE id = :id";
                $stmt = $this->conn->prepare($query);
                $last_login = date('Y-m-d H:i:s');
                $stmt->bindParam(':last_login', $last_login);
                $stmt->bindParam(':id', $this->id);
                $stmt->execute();
                
                return "success";
            }
        }
        
        return "invalid";
    }

    // Get users by role
    function getUsersByRole($role) {
        // query to select all users with specific role
        $query = "SELECT u.id, u.name, u.email, u.phone, 
                    u.gender, u.specialist, u.created
                FROM " . $this->table_name . " u
                JOIN roles r ON u.role_id = r.id
                WHERE r.name = :role
                ORDER BY u.name ASC";
    
        // prepare query statement
        $stmt = $this->conn->prepare($query);
        
        // bind role value
        $stmt->bindParam(":role", $role);
    
        // execute query
        $stmt->execute();
    
        return $stmt;
    }
    
    // Get doctor details including their patients
    function getDoctorWithPatients() {
        if ($this->role !== 'doctor') {
            return false;
        }
        
        // query to select all patients for a doctor
        $query = "SELECT u.id, u.name, u.phone, u.gender, u.health_condition, 
                    p.created as assigned_date
                FROM patients p
                JOIN users u ON p.user_id = u.id
                WHERE p.doctor_id = :doctor_id
                ORDER BY u.name ASC";
    
        // prepare query statement
        $stmt = $this->conn->prepare($query);
        
        // bind doctor id
        $stmt->bindParam(":doctor_id", $this->id);
    
        // execute query
        $stmt->execute();
    
        return $stmt;
    }
    
    // Get patient details including their doctor and nurse
    function getPatientDetails() {
        if ($this->role !== 'patient') {
            return false;
        }
        
        // query to select patient's doctor and nurse
        $query = "SELECT 
                    d.id as doctor_id, d.name as doctor_name, d.specialist,
                    n.id as nurse_id, n.name as nurse_name
                FROM patients p
                JOIN users d ON p.doctor_id = d.id
                JOIN users n ON p.nurse_id = n.id
                WHERE p.user_id = :patient_id
                LIMIT 1";
    
        // prepare query statement
        $stmt = $this->conn->prepare($query);
        
        // bind patient id
        $stmt->bindParam(":patient_id", $this->id);
    
        // execute query
        $stmt->execute();
    
        return $stmt;
    }
}
?>