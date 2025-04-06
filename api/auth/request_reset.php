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
include_once '../utils/email_sender.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Prepare user object
$user = new User($db);

// Get posted data
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->email)) {
    // Set email property
    $user->email = $data->email;
    
    // Request password reset
    if ($user->requestPasswordReset()) {
        // Send reset email
        $reset_url = "http://" . $_SERVER['HTTP_HOST'] . "/auth/reset-password.php?token=" . $user->reset_token;
        $subject = "Password Reset Request";
        $body = "Hello,\n\n";
        $body .= "You requested to reset your password. Please click the following link to reset your password:\n";
        $body .= $reset_url . "\n\n";
        $body .= "This link will expire in 24 hours.\n\n";
        $body .= "If you didn't request a password reset, please ignore this email.\n\n";
        $body .= "Thank you,\nMedical Center Team";
        
        // Send email (Note: in a real system, you'd have a proper email setup)
        // sendEmail($user->email, $subject, $body);
        
        // Return success response
        http_response_code(200);
        echo json_encode(array(
            "status" => true,
            "message" => "Password reset instructions have been sent to your email.",
            "reset_token" => $user->reset_token  // In production, you wouldn't return this
        ));
    } else {
        // No user found or error
        // Still return success for security reasons (don't reveal if email exists)
        http_response_code(200);
        echo json_encode(array(
            "status" => true,
            "message" => "If your email exists in our system, password reset instructions will be sent."
        ));
    }
} else {
    // Email missing
    http_response_code(400);
    echo json_encode(array(
        "status" => false,
        "message" => "Email address is required."
    ));
}
?>