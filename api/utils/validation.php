<?php
/**
 * Utility class for input validation
 */
class Validation {
    /**
     * Validate email format
     * 
     * @param string $email Email to validate
     * @return bool True if email is valid, false otherwise
     */
    public static function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Check password strength
     * 
     * @param string $password Password to validate
     * @return array ['valid' => bool, 'message' => string]
     */
    public static function validatePassword($password) {
        $result = ['valid' => true, 'message' => ''];
        
        if (strlen($password) < 8) {
            $result['valid'] = false;
            $result['message'] = 'Password must be at least 8 characters long';
            return $result;
        }
        
        // Check for at least one uppercase letter
        if (!preg_match('/[A-Z]/', $password)) {
            $result['valid'] = false;
            $result['message'] = 'Password must contain at least one uppercase letter';
            return $result;
        }
        
        // Check for at least one lowercase letter
        if (!preg_match('/[a-z]/', $password)) {
            $result['valid'] = false;
            $result['message'] = 'Password must contain at least one lowercase letter';
            return $result;
        }
        
        // Check for at least one number
        if (!preg_match('/[0-9]/', $password)) {
            $result['valid'] = false;
            $result['message'] = 'Password must contain at least one number';
            return $result;
        }
        
        return $result;
    }
    
    /**
     * Clean and sanitize input
     * 
     * @param string $input Input to sanitize
     * @return string Sanitized input
     */
    public static function sanitizeInput($input) {
        $input = trim($input);
        $input = stripslashes($input);
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        return $input;
    }
}
?>