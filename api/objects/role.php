<?php
class Role {
    // Database connection and table name
    private $conn;
    private $table_name = "roles";
    private $permissions_table = "permissions";
    private $role_permissions_table = "role_permissions";
 
    // Object properties
    public $id;
    public $name;
    public $description;
 
    // Constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Read all roles
    public function read() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    // Read single role
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->name = $row['name'];
        $this->description = $row['description'];
    }
    
    // Get role by name
    public function getByName() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE name = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->name);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->id = $row['id'];
            $this->description = $row['description'];
            return true;
        }
        return false;
    }
    
    // Get permissions for a role
    public function getPermissions() {
        $query = "SELECT p.* 
                FROM " . $this->permissions_table . " p
                JOIN " . $this->role_permissions_table . " rp ON p.id = rp.permission_id
                WHERE rp.role_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Check if role has a specific permission
    public function hasPermission($permission_name) {
        $query = "SELECT 1 
                FROM " . $this->permissions_table . " p
                JOIN " . $this->role_permissions_table . " rp ON p.id = rp.permission_id
                WHERE rp.role_id = ? AND p.name = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->bindParam(2, $permission_name);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
}
?>