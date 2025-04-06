<?php
// Required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET");
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

// Get token from query string or posted data
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $token = isset($_GET['token']) ? $_GET['token'] : "";
} else {
    $data = json_decode(file_get_contents("php://input"));
    $token = isset($data->token) ? $data->token : "";
}

if (!empty($token)) {
    // Set token property
    $user->verification_token = $token;
    
    // Verify email
    if ($user->verifyEmail()) {
        // Success
        http_response_code(200);
        echo json_encode(array(
            "status" => true,
            "message" => "Email verified successfully. You can now login."
        ));
    } else {
        // Invalid token
        http_response_code(400);
        echo json_encode(array(
            "status" => false,
            "message" => "Invalid or expired verification token."
        ));
    }
} else {
    // Token missing
    http_response_code(400);
    echo json_encode(array(
        "status" => false,
        "message" => "Verification token is required."
    ));
}
?>