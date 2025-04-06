<?php
// Start session
session_start();

// If user is already logged in, redirect to index page
if(isset($_SESSION['user_id'])){
    header("Location: /index.php");
    exit;
}

// Include database and user object files
include_once '../api/config/database.php';
include_once '../api/objects/user.php';
include_once '../api/objects/doctor.php';
include_once '../api/objects/nurse.php';
include_once '../api/objects/patient.php';
include_once '../api/utils/validation.php';

// Define page variables
$pageTitle = "Login";
$pageSubTitle = "Enter your credentials to access the system";
$errorMsg = "";
$successMsg = "";

// Check if form submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate email format
    if(!Validation::isValidEmail($_POST['email'])) {
        $errorMsg = "Please enter a valid email address.";
    } else {
        // Get database connection
        $database = new Database();
        $db = $database->getConnection();
        
        // Instantiate user object
        $user = new User($db);
        
        // Set user properties
        $user->email = $_POST['email'];
        $user->password = $_POST['password'];
        
        // Attempt login - this checks users table which includes admins
        $result = $user->login();
        
        switch($result){
            case "success":
                // Set session variables
                $_SESSION['user_id'] = $user->id;
                $_SESSION['user_name'] = $user->name;
                $_SESSION['user_email'] = $user->email;
                $_SESSION['user_role'] = $user->role;
                $_SESSION['role_id'] = $user->role_id;
                
                // Create role-specific object instance based on user role
                if($_SESSION['user_role'] == 'doctor') {
                    $doctor = new Doctor($db);
                    $doctor->id = $user->id;
                    $_SESSION['role_object'] = 'doctor';
                } 
                else if($_SESSION['user_role'] == 'nurse') {
                    $nurse = new Nurse($db);
                    $nurse->id = $user->id;
                    $_SESSION['role_object'] = 'nurse';
                } 
                else if($_SESSION['user_role'] == 'patient') {
                    $patient = new Patient($db);
                    $patient->user_id = $user->id;
                    $_SESSION['role_object'] = 'patient';
                }
                
                // Log activity
                require_once '../api/middleware/auth_middleware.php';
                Auth::logActivity("login");
                
                // Redirect to index page
                header("Location: /index.php");
                exit;
                
            case "mfa_required":
                // Store user ID for MFA verification
                $_SESSION['mfa_pending'] = true;
                
                // Redirect to MFA verification page
                header("Location: /auth/mfa.php");
                exit;
                
            case "unverified":
                $errorMsg = "Please verify your email before logging in.";
                break;
                
            case "invalid":
            default:
                $errorMsg = "Invalid email or password.";
                break;
        }
    }
}

// Define content
$content = "
<div class=\"login-box\">
  <div class=\"login-logo\">
    <a href=\"/index.php\"><b>Medi</b>CENTER</a>
  </div>
  <!-- /.login-logo -->
  <div class=\"login-box-body\">
    <p class=\"login-box-msg\">Sign in to start your session</p>
    
    " . ($errorMsg ? "<div class=\"alert alert-danger\">" . $errorMsg . "</div>" : "") . "
    " . ($successMsg ? "<div class=\"alert alert-success\">" . $successMsg . "</div>" : "") . "

    <form action=\"/auth/login.php\" method=\"post\">
      <div class=\"form-group has-feedback\">
        <input type=\"email\" name=\"email\" class=\"form-control\" placeholder=\"Email\" required>
        <span class=\"glyphicon glyphicon-envelope form-control-feedback\"></span>
      </div>
      <div class=\"form-group has-feedback\">
        <input type=\"password\" name=\"password\" class=\"form-control\" placeholder=\"Password\" required>
        <span class=\"glyphicon glyphicon-lock form-control-feedback\"></span>
      </div>
      <div class=\"row\">
        <div class=\"col-xs-8\">
          <div class=\"checkbox icheck\">
            <label>
              <input type=\"checkbox\"> Remember Me
            </label>
          </div>
        </div>
        <!-- /.col -->
        <div class=\"col-xs-4\">
          <button type=\"submit\" class=\"btn btn-primary btn-block btn-flat\">Sign In</button>
        </div>
        <!-- /.col -->
      </div>
    </form>

    <a href=\"/auth/forgot-password.php\">I forgot my password</a><br>
    <a href=\"/auth/register.php\" class=\"text-center\">Register a new account</a>
  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->";

// Use a special layout for login pages
include('../auth_layout.php');
?>