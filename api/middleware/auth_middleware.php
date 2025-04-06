<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../objects/user.php';

class Auth {
    /**
     * Check if user is authenticated
     * @return mixed User object if authenticated, false otherwise
     */
    public static function isAuthenticated() {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        // Get database connection
        $database = new Database();
        $db = $database->getConnection();
        
        // Create user object
        $user = new User($db);
        $user->id = $_SESSION['user_id'];
        
        // Get user details
        if($user->readOne()) {
            return $user;
        }
        
        // If user doesn't exist, destroy session
        self::logout();
        return false;
    }
    
    /**
     * Check if user has specific role
     * @param string $roleName Role name
     * @return bool True if user has the role, false otherwise
     */
    public static function hasRole($roleName) {
        $user = self::isAuthenticated();
        if (!$user) {
            return false;
        }
        
        return $user->role === $roleName;
    }
    
    /**
     * Check if user has specific permission
     * @param string $permissionName Permission name
     * @return bool True if user has the permission, false otherwise
     */
    public static function hasPermission($permissionName) {
        $user = self::isAuthenticated();
        if (!$user) {
            return false;
        }
        
        // Admin has all permissions
        if ($user->role === 'admin') {
            return true;
        }
        
        // Get database connection
        $database = new Database();
        $db = $database->getConnection();
        
        // Check if user has permission
        $query = "SELECT COUNT(*) as count 
                 FROM permissions p 
                 JOIN role_permissions rp ON p.id = rp.permission_id
                 JOIN roles r ON rp.role_id = r.id
                 JOIN users u ON u.role_id = r.id
                 WHERE u.id = :user_id AND p.name = :permission_name";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $user->id);
        $stmt->bindParam(':permission_name', $permissionName);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] > 0;
    }
    
    /**
     * Log user out
     */
    public static function logout() {
        // Clear all session variables
        $_SESSION = array();
        
        // Destroy the session
        session_destroy();
        
        // Delete session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
    }
    
    /**
     * Generate CSRF token
     * @return string CSRF token
     */
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verify CSRF token
     * @param string $token CSRF token
     * @return bool True if token is valid, false otherwise
     */
    public static function verifyCSRFToken($token) {
        if (!isset($_SESSION['csrf_token']) || $_SESSION['csrf_token'] !== $token) {
            return false;
        }
        return true;
    }
    
    /**
     * Log activity
     * @param string $action Action performed
     * @param string $entity Entity type
     * @param int $entityId Entity ID
     * @param string $details Additional details
     */
    public static function logActivity($action, $entity = null, $entityId = null, $details = null) {
        $user = self::isAuthenticated();
        $userId = $user ? $user->id : null;
        
        $database = new Database();
        $db = $database->getConnection();
        
        $query = "INSERT INTO audit_logs 
                  (user_id, action, entity, entity_id, details, ip_address) 
                  VALUES 
                  (:user_id, :action, :entity, :entity_id, :details, :ip_address)";
                  
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':action', $action);
        $stmt->bindParam(':entity', $entity);
        $stmt->bindParam(':entity_id', $entityId);
        $stmt->bindParam(':details', $details);
        
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $stmt->bindParam(':ip_address', $ipAddress);
        
        //$stmt->execute();
    }
}
?>