<?php
// Required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database and user object files
include_once '../config/database.php';
include_once '../objects/user.php';
include_once '../utils/jwt_handler.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Instantiate user object
$user = new User($db);

// Get posted data
$data = json_decode(file_get_contents("php://input"));

// Make sure data is not empty
if (!empty($data->email) && !empty($data->password)) {
    
    // Set user property values
    $user->email = $data->email;
    $user->password = $data->password;
    
    // Attempt login
    if ($user->login()) {
        // Check if email is verified
        if (!$user->email_verified) {
            // Email not verified
            http_response_code(401);
            echo json_encode(array(
                "status" => false,
                "message" => "Email not verified. Please check your inbox and verify your email."
            ));
            exit;
        }
        
        // Check if MFA is enabled
        if ($user->mfa_enabled) {
            // Return MFA required response
            http_response_code(200);
            echo json_encode(array(
                "status" => true,
                "message" => "MFA required",
                "require_mfa" => true,
                "user_id" => $user->id
            ));
            exit;
        }
        
        // Create session
        $ip = $_SERVER['REMOTE_ADDR'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $token = $user->createSession($ip, $user_agent);
        
        // Get user role information
        $role_info = array(
            "id" => $user->role_id,
            "name" => $user->role_name
        );
        
        // Login successful
        http_response_code(200);
        echo json_encode(array(
            "status" => true,
            "message" => "Login successful",
            "token" => $token,
            "user" => array(
                "id" => $user->id,
                "username" => $user->username,
                "email" => $user->email,
                "role" => $role_info
            )
        ));
    } 
    else {
        // Login failed
        http_response_code(401);
        echo json_encode(array(
            "status" => false,
            "message" => "Invalid email or password."
        ));
    }
} 
else {
    // Data incomplete
    http_response_code(400);
    echo json_encode(array(
        "status" => false,
        "message" => "Email and password are required."
    ));
}
?>