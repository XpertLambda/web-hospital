<?php
// Required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

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

if (!$user) {
    // Not authenticated
    http_response_code(401);
    echo json_encode(array("message" => "Unauthorized"));
    exit;
}

// Check user role to determine what nurses they can see
if ($user->role === 'admin') {
    // Admin can see all nurses
    $stmt = $nurse->read();
} elseif ($user->role === 'patient') {
    // Patient can only see their assigned nurse
    $stmt = $nurse->readPatientNurse($user->id);
} elseif ($user->role === 'doctor') {
    // Doctor can see nurses assigned to their patients
    $stmt = $nurse->readByDoctor($user->id);
} elseif ($user->role === 'nurse') {
    // Nurses can only see themselves
    $stmt = $nurse->readSingle($user->id);
} else {
    // Default case
    $stmt = $nurse->read();
}

$num = $stmt->rowCount();

// Check if more than 0 record found
if ($num > 0) {
    // Nurses array
    $nurses_arr = array();

    // Retrieve table contents
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $nurse_item = array(
            "id" => $id,
            "name" => $name,
            "email" => $email,
            "phone" => $phone
        );

        array_push($nurses_arr, $nurse_item);
    }

    // Set response code - 200 OK
    http_response_code(200);

    // Show nurses data in json format
    echo json_encode($nurses_arr);
} else {
    // Set response code - 404 Not found
    http_response_code(404);

    // Tell the user no nurses found
    echo json_encode(array("message" => "No nurses found."));
}
?>
