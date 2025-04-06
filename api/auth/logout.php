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

// Get authorization header
$headers = getallheaders();
$token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : "";

if (!empty($token)) {
    // Delete session
    if (User::deleteSession($db, $token)) {
        // Success
        http_response_code(200);
        echo json_encode(array(
            "status" => true,
            "message" => "Logged out successfully."
        ));
    } else {
        // Error deleting session
        http_response_code(500);
        echo json_encode(array(
            "status" => false,
            "message" => "Failed to logout."
        ));
    }
} else {
    // Token missing
    http_response_code(400);
    echo json_encode(array(
        "status" => false,
        "message" => "Authorization token is required."
    ));
}
?>
