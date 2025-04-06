<?php
// Required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database and object files
include_once '../config/database.php';
include_once '../objects/nurse.php';
include_once '../middleware/auth_middleware.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Initialize nurse object
$nurse = new Nurse($db);

// Get authenticated user
$user = Auth::isAuthenticated();

if (!$user || $user->role !== 'admin') {
    // Only admin can delete nurses
    http_response_code(403);
    echo json_encode(array("message" => "You don't have permission to delete nurses"));
    exit;
}

// Set nurse id to be deleted
$nurse->id = isset($_POST["id"]) ? $_POST["id"] : die();

// Delete the nurse
if($nurse->delete()){
    // Set response code - 200 ok
    http_response_code(200);
    
    // Tell the user
    echo json_encode(array(
        "status" => true,
        "message" => "Nurse was deleted."
    ));
}
else{
    // Set response code - 503 service unavailable
    http_response_code(503);
    
    // Tell the user
    echo json_encode(array(
        "status" => false,
        "message" => "Unable to delete nurse. Nurse may be assigned to patients."
    ));
}