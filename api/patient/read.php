<?php
// Required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Include database and object files
include_once '../config/database.php';
include_once '../objects/patient.php';
include_once '../middleware/auth_middleware.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Initialize patient object
$patient = new Patient($db);

// Get authenticated user
$user = Auth::isAuthenticated();

if (!$user) {
    // Not authenticated
    http_response_code(401);
    echo json_encode(array("message" => "Unauthorized"));
    exit;
}

// Check user role to determine what patients they can see
if ($user->role === 'admin') {
    // Admin can see all patients
    $stmt = $patient->read();
} elseif ($user->role === 'doctor') {
    // Doctor can only see their patients
    $stmt = $patient->readByDoctor($user->id);
} elseif ($user->role === 'patient') {
    // Patient can only see themselves
    $stmt = $patient->readSingle($user->id);
} elseif ($user->role === 'nurse') {
    // Nurse might see patients they're assigned to
    $stmt = $patient->readByNurse($user->id);
} else {
    // Default case
    $stmt = $patient->read();
}

$num = $stmt->rowCount();

// Check if more than 0 record found
if ($num > 0) {
    // Patients array
    $patients_arr = array();

    // Retrieve table contents
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $patient_item = array(
            "id" => $id,
            "name" => $name,
            "phone" => $phone,
            "gender" => $gender,
            "health_condition" => $health_condition,
            "doctor_id" => $doctor_id,
            "doctor_name" => $doctor_name,
            "nurse_id" => $nurse_id,
            "nurse_name" => $nurse_name
        );

        array_push($patients_arr, $patient_item);
    }

    // Set response code - 200 OK
    http_response_code(200);

    // Show patients data in json format
    echo json_encode($patients_arr);
} else {
    // Set response code - 404 Not found
    http_response_code(404);

    // Tell the user no patients found
    echo json_encode(array("message" => "No patients found."));
}
?>
