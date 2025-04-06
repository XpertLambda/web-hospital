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

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Prepare user object
$user = new User($db);

// Get posted data
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->token) && !empty($data->password)) {
    // Set properties
    $user->reset_token = $data->token;
    $user->password = $data->password;
    
    // Reset password
    if ($user->resetPassword()) {
        // Success
        http_response_code(200);
        echo json_encode(array(
            "status" => true,
            "message" => "Password has been reset successfully. You can now login with your new password."
        ));
    } else {
        // Invalid or expired token
        http_response_code(400);
        echo json_encode(array(
            "status" => false,
            "message" => "Invalid or expired password reset token."
        ));
    }
} else {
    // Data incomplete
    http_response_code(400);
    echo json_encode(array(
        "status" => false,
        "message" => "Token and new password are required."
    ));
}
?>