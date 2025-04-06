<?php
// Required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database and object files
include_once '../config/database.php';
include_once '../objects/user.php';
include_once '../objects/role.php';
include_once '../utils/email_sender.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Instantiate objects
$user = new User($db);
$role = new Role($db);

// Get posted data
$data = json_decode(file_get_contents("php://input"));

// Make sure data is not empty
if (
    !empty($data->username) && 
    !empty($data->email) && 
    !empty($data->password) && 
    !empty($data->role)
) {
    
    // Set user property values
    $user->username = $data->username;
    $user->email = $data->email;
    $user->password = $data->password;
    
    // Get role ID from name
    $role->name = $data->role;
    if ($role->getByName()) {
        $user->role_id = $role->id;
    } else {
        // Default to patient role if invalid
        $role->name = "patient";
        $role->getByName();
        $user->role_id = $role->id;
    }
    
    // Create the user
    if ($user->create()) {
        
        // Send verification email
        $verify_url = "http://" . $_SERVER['HTTP_HOST'] . "/auth/verify-email.php?token=" . $user->verification_token;
        $subject = "Please verify your email address";
        $body = "Hello " . $user->username . ",\n\n";
        $body .= "Please click the following link to verify your email address:\n";
        $body .= $verify_url . "\n\n";
        $body .= "If you didn't create an account, please ignore this email.\n\n";
        $body .= "Thank you,\nMedical Center Team";
        
        // Send email (Note: in a real system, you'd have a proper email setup)
        // sendEmail($user->email, $subject, $body);
        
        // Return success response
        http_response_code(201);
        echo json_encode(array(
            "status" => true,
            "message" => "User created successfully. Please check your email to verify your account.",
            "user_id" => $user->id,
            "verification_token" => $user->verification_token  // In production, you wouldn't return this
        ));
    } 
    else {
        // Failed to create user
        http_response_code(503);
        echo json_encode(array(
            "status" => false,
            "message" => "Email address already exists."
        ));
    }
} 
else {
    // Data incomplete
    http_response_code(400);
    echo json_encode(array(
        "status" => false,
        "message" => "Unable to create user. Data is incomplete."
    ));
}
?>