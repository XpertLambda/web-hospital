<?php
// Required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Include database and object files
include_once '../config/database.php';
include_once '../objects/doctor.php';
include_once '../middleware/auth_middleware.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Initialize doctor object
$doctor = new Doctor($db);

// Get authenticated user
$user = Auth::isAuthenticated();

if (!$user) {
    // Not authenticated
    http_response_code(401);
    echo json_encode(array("message" => "Unauthorized"));
    exit;
}

// Check user role to determine what doctors they can see
if ($user->role === 'admin') {
    // Admin can see all doctors
    $stmt = $doctor->read();
} elseif ($user->role === 'patient') {
    // Patient can only see their assigned doctor
    $stmt = $doctor->readPatientDoctor($user->id);
} elseif ($user->role === 'doctor') {
    // Doctors can only see themselves
    $stmt = $doctor->readSingle($user->id);
} else {
    // Other roles (like nurse) might see a subset
    $stmt = $doctor->read(); // Could be restricted further if needed
}

$num = $stmt->rowCount();

if ($num > 0) {
    // Doctors array
    $doctors_arr = array();

    // Retrieve table contents
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Extract row
        extract($row);

        $doctor_item = array(
            "id" => $id,
            "name" => $name,
            "email" => $email,
            "phone" => $phone,
            "gender" => $gender,
            "specialist" => $specialist
        );

        array_push($doctors_arr, $doctor_item);
    }

    // Set response code - 200 OK
    http_response_code(200);

    // Show doctors data
    echo json_encode($doctors_arr);
} else {
    // Set response code - 404 Not found
    http_response_code(404);

    // Tell the user no doctors found
    echo json_encode(array("message" => "No doctors found."));
}
?>