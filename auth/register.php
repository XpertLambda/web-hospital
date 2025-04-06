<?php
// Add these lines to the very top of the file
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Create a debug log function
function debug_log($message, $data = null) {
    error_log($message . ($data ? ': ' . print_r($data, true) : ''));
}

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
include_once '../api/utils/validation.php';

// Define page variables
$pageTitle = "Register";
$pageSubTitle = "Create your account";
$errorMsg = "";
$successMsg = "";

// Process form submission
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Get database connection
    $database = new Database();
    $db = $database->getConnection();
    
    // Check if database connection is successful
    if($db === null){
        $errorMsg = "Unable to connect to database. Please try again later or contact support.";
    } else {
        // Validate email format
        if(!Validation::isValidEmail($_POST['email'])) {
            $errorMsg = "Please enter a valid email address.";
        }
        // Validate password strength
        else {
            $passwordCheck = Validation::validatePassword($_POST['password']);
            if(!$passwordCheck['valid']) {
                $errorMsg = $passwordCheck['message'];
            }
            // Check if passwords match
            elseif($_POST['password'] !== $_POST['confirm_password']){
                $errorMsg = "Passwords do not match.";
            }
            else {
                // Instantiate user object
                $user = new User($db);
                
                // Set user properties
                $user->name = Validation::sanitizeInput($_POST['name']);
                $user->email = Validation::sanitizeInput($_POST['email']);
                $user->password = $_POST['password'];
                $user->phone = Validation::sanitizeInput($_POST['phone']);
                
                // Get role ID based on selected role
                $selectedRole = Validation::sanitizeInput($_POST['role']);
                $roleQuery = "SELECT id FROM roles WHERE name = :role_name";
                $stmt = $db->prepare($roleQuery);
                $stmt->bindParam(':role_name', $selectedRole);
                $stmt->execute();
                $role = $stmt->fetch(PDO::FETCH_ASSOC);
                $user->role_id = $role['id'];
                
                // Check if selected role is admin and require admin code
                $isValid = true;
                if ($selectedRole === 'admin' && (!isset($_POST['admin_code']) || $_POST['admin_code'] !== '999!')) {
                    $errorMsg = "Invalid administrator registration code.";
                    $isValid = false;
                } else if ($selectedRole === 'admin') {
                    // Admin code is valid, proceed with admin registration
                    try {
                        // Begin transaction to ensure both tables are updated or none
                        $db->beginTransaction();
                        
                        // First insert into users table
                        $stmt = $db->prepare("INSERT INTO users (name, email, password, phone) VALUES (?, ?, ?, ?)");
                        $hashedPassword = password_hash($user->password, PASSWORD_BCRYPT);
                        $stmt->execute([$user->name, $user->email, $hashedPassword, $user->phone]);
                        $userId = $db->lastInsertId();
                        
                        // Then insert into admins table
                        $stmt = $db->prepare("INSERT INTO admins (user_id, name, email, password, phone, created) VALUES (?, ?, ?, ?, ?, NOW())");
                        $stmt->execute([$userId, $user->name, $user->email, $hashedPassword, $user->phone]);
                        
                        $db->commit();
                        // Redirect to success page or login
                        $successMsg = "Administrator registration successful!";
                        header("Location: /auth/login.php?registered=1");
                        exit;
                    } catch (Exception $e) {
                        $db->rollBack();
                        $errorMsg = "Registration failed: " . $e->getMessage();
                        $isValid = false;
                    }
                }
                
                if ($isValid) {
                    // Set optional properties based on role
                    $user->gender = isset($_POST['gender']) ? $_POST['gender'] : null;
                    
                    if ($selectedRole === 'doctor') {
                        $user->specialist = isset($_POST['specialist']) ? Validation::sanitizeInput($_POST['specialist']) : null;
                        $user->health_condition = null;
                    } else if ($selectedRole === 'patient') {
                        $user->specialist = null;
                        $user->health_condition = isset($_POST['health_condition']) ? Validation::sanitizeInput($_POST['health_condition']) : 'Normal';
                    } else {
                        $user->specialist = null;
                        $user->health_condition = null;
                    }
                    
                    // Create user
                    try {
                        if($user->create()){
                            // Extra step: Add role-specific data to the appropriate table
                            $userId = $db->lastInsertId();
                            
                            try {
                                if ($selectedRole === 'doctor') {
                                    $doctorQuery = "INSERT INTO doctors (user_id, name, email, password, phone, specialist) 
                                                   VALUES (:user_id, :name, :email, :password, :phone, :specialist)";
                                    $doctorStmt = $db->prepare($doctorQuery);
                                    $doctorStmt->bindParam(':user_id', $userId);
                                    $doctorStmt->bindParam(':name', $user->name);
                                    $doctorStmt->bindParam(':email', $user->email);
                                    $passwordHash = password_hash($user->password, PASSWORD_BCRYPT);
                                    $doctorStmt->bindParam(':password', $passwordHash);
                                    $doctorStmt->bindParam(':phone', $user->phone);
                                    $doctorStmt->bindParam(':specialist', $user->specialist);
                                    $doctorStmt->execute();
                                } else if ($selectedRole === 'nurse') {
                                    $nurseQuery = "INSERT INTO nurses (user_id, name, email, password, phone) 
                                                  VALUES (:user_id, :name, :email, :password, :phone)";
                                    $nurseStmt = $db->prepare($nurseQuery);
                                    $nurseStmt->bindParam(':user_id', $userId);
                                    $nurseStmt->bindParam(':name', $user->name);
                                    $nurseStmt->bindParam(':email', $user->email);
                                    $passwordHash = password_hash($user->password, PASSWORD_BCRYPT);
                                    $nurseStmt->bindParam(':password', $passwordHash);
                                    $nurseStmt->bindParam(':phone', $user->phone);
                                    $nurseStmt->execute();
                                } else if ($selectedRole === 'patient') {
                                    $patientQuery = "INSERT INTO patients (user_id, name, phone, gender, health_condition) 
                                                    VALUES (:user_id, :name, :phone, :gender, :health_condition)";
                                    $patientStmt = $db->prepare($patientQuery);
                                    $patientStmt->bindParam(':user_id', $userId);
                                    $patientStmt->bindParam(':name', $user->name);
                                    $patientStmt->bindParam(':phone', $user->phone);
                                    $patientStmt->bindParam(':gender', $user->gender);
                                    $patientStmt->bindParam(':health_condition', $user->health_condition);
                                    $patientStmt->execute();
                                }
                                
                                // Send verification email
                                require_once '../api/utils/email_sender.php';
                                $emailSender = new EmailSender();
                                $verificationLink = "http://" . $_SERVER['HTTP_HOST'] . "/auth/verify-email.php?token=" . $user->verification_token;
                                $emailSent = $emailSender->sendVerificationEmail($user->email, $user->name, $verificationLink);
                                
                                if($emailSent){
                                    $successMsg = "Registration successful! Please check your email to verify your account.";
                                    debug_log("Registration successful, email sent");
                                } else {
                                    $successMsg = "Registration successful but we couldn't send the verification email. Please contact support.";
                                    debug_log("Registration successful, email failed");
                                }
                                
                                // Add explicit redirect to prevent blank page
                                header("Location: /auth/login.php?registered=1");
                                exit;
                            } catch (Exception $e) {
                                // Log the error
                                error_log('Error in role-specific registration: ' . $e->getMessage());
                                $errorMsg = "Registration partially completed. Error details: " . $e->getMessage();
                            }
                        } else {
                            $errorMsg = "Registration failed. Email may already be in use.";
                        }
                    } catch (Exception $e) {
                        // Log the error
                        error_log('Error in user creation: ' . $e->getMessage());
                        $errorMsg = "Registration failed. Error details: " . $e->getMessage();
                    }
                }
            }
        }
    }
}

// Define content
$content = '
<div class="register-box">
  <div class="register-logo">
    <a href="/index.php"><b>Medi</b>CENTER</a>
  </div>

  <div class="register-box-body">
    <p class="login-box-msg">Register a new account</p>
    
    ' . ($errorMsg ? '<div class="alert alert-danger">' . $errorMsg . '</div>' : '') . '
    ' . ($successMsg ? '<div class="alert alert-success">' . $successMsg . '</div>' : '') . '

    <form action="/auth/register.php" method="post">
      <div class="form-group has-feedback">
        <input type="text" name="name" class="form-control" placeholder="Full name" required>
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="email" name="email" class="form-control" placeholder="Email" required>
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" name="password" class="form-control" placeholder="Password" required>
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" name="confirm_password" class="form-control" placeholder="Confirm password" required>
        <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="text" name="phone" class="form-control" placeholder="Phone number" required>
        <span class="glyphicon glyphicon-phone form-control-feedback"></span>
      </div>
      
      <!-- Add role selection dropdown -->
      <div class="form-group">
        <label>Register as</label>
        <select class="form-control" name="role" id="role-select">
          <option value="patient">Patient</option>
          <option value="doctor">Doctor</option>
          <option value="nurse">Nurse</option>
          <option value="admin">Administrator</option>
        </select>
      </div>
      
      <!-- Doctor-specific fields (hidden by default) -->
      <div class="form-group doctor-field" style="display: none;">
        <label>Specialization</label>
        <input type="text" name="specialist" class="form-control" placeholder="Your specialization">
      </div>
      
      <!-- Patient-specific fields -->
      <div class="form-group patient-field">
        <label>Health Condition (Optional)</label>
        <textarea name="health_condition" class="form-control" rows="2" placeholder="Please describe your current health condition"></textarea>
      </div>
      
      <!-- Admin-specific fields -->
      <div class="form-group admin-field" style="display: none;">
        <label>Administrator Code</label>
        <input type="text" name="admin_code" class="form-control" placeholder="Enter administrator code">
      </div>
      
      <div class="form-group">
        <label>Gender</label>
        <select class="form-control" name="gender">
          <option value="1">Male</option>
          <option value="0">Female</option>
        </select>
      </div>
      
      <div class="row">
        <div class="col-xs-8">
          <div class="checkbox icheck">
            <label>
              <input type="checkbox" required> I agree to the <a href="#">terms</a>
            </label>
          </div>
        </div>
        <!-- /.col -->
        <div class="col-xs-4">
          <button type="submit" class="btn btn-primary btn-block btn-flat">Register</button>
        </div>
        <!-- /.col -->
      </div>
    </form>

    <a href="/auth/login.php" class="text-center">I already have an account</a>
  </div>
  <!-- /.form-box -->
</div>
<!-- /.register-box -->';

// Use auth layout for register page
include('../auth_layout.php');
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role-select');
    
    roleSelect.addEventListener('change', function() {
        // Hide all role-specific fields first
        document.querySelectorAll('.doctor-field, .patient-field, .admin-field').forEach(el => {
            el.style.display = 'none';
        });
        
        // Show fields based on selected role
        const selectedRole = this.value;
        if (selectedRole === 'doctor') {
            document.querySelectorAll('.doctor-field').forEach(el => {
                el.style.display = 'block';
            });
        } else if (selectedRole === 'patient') {
            document.querySelectorAll('.patient-field').forEach(el => {
                el.style.display = 'block';
            });
        } else if (selectedRole === 'admin') {
            document.querySelectorAll('.admin-field').forEach(el => {
                el.style.display = 'block';
            });
        }
    });
    
    // Initialize on page load
    roleSelect.dispatchEvent(new Event('change'));
});
</script>