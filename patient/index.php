<?php
// Set required permission
$requiredPermission = 'view_patients';
$pageTitle = "Patients";
$pageSubTitle = "Manage patients";

// Add authentication check
require_once '../api/middleware/auth_middleware.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is authenticated
$user = Auth::isAuthenticated();
if (!$user) {
    header("Location: /auth/login.php");
    exit;
}

// Check permissions - admins, doctors with their patients, and patients viewing themselves
$allowAccess = false;
if ($user->role === 'admin' || Auth::hasPermission('view_patients')) {
    $allowAccess = true;
} elseif ($user->role === 'doctor' || $user->role === 'nurse' || $user->role === 'patient') {
    $allowAccess = true; // They will see filtered data based on their role
}

if (!$allowAccess) {
    header("Location: /auth/access-denied.php");
    exit;
}

// Define the content
$content = '<div class="row">
              <div class="col-xs-12">
                <div class="box">
                  <div class="box-header">
                    <h3 class="box-title">Patients</h3>';

// Only admins can create new patients
if ($user->role === 'admin') {
    $content .= '<div class="pull-right">
                  <a href="/patient/create.php" class="btn btn-primary">Add New Patient</a>
                </div>';
}

$content .= '    </div>
                  <div class="box-body">
                    <table id="patients" class="table table-bordered table-striped">
                      <thead>
                      <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Gender</th>
                        <th>Health Condition</th>
                        <th>Doctor</th>
                        <th>Nurse</th>
                        <th>Action</th>
                      </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>';

include('../master.php');
?>
<script>
  $(document).ready(function () {
    $.ajax({
        type: "GET",
        url: "../api/patient/read.php",
        dataType: 'json',
        success: function (data) {
            var response = "";
            for (var patient in data) {
                response += "<tr>"+
                "<td>"+data[patient].name+"</td>"+
                "<td>"+data[patient].phone+"</td>"+
                "<td>"+(data[patient].gender == 0 ? 'Female' : 'Male')+"</td>"+
                "<td>"+data[patient].health_condition+"</td>"+
                "<td>"+data[patient].doctor_name+"</td>"+
                "<td>"+data[patient].nurse_name+"</td>"+
                "<td>";
                
                // Action buttons based on role
                var userRole = "<?php echo $user->role; ?>";
                if (userRole === "admin") {
                    response += "<a href='/patient/update.php?id="+data[patient].id+"'>Edit</a> | "+
                                "<a href='#' onClick=Remove('"+data[patient].id+"')>Remove</a>";
                } else if (userRole === "doctor" || userRole === "nurse") {
                    response += "<a href='/patient/view.php?id="+data[patient].id+"'>View</a> | "+
                                "<a href='/medical-records/create.php?patient_id="+data[patient].id+"'>Add Record</a>";
                } else {
                    response += "<a href='/patient/view.php?id="+data[patient].id+"'>View</a>";
                }
                
                response += "</td></tr>";
            }
            $(response).appendTo($("#patients tbody"));
        },
        error: function(xhr, status, error) {
            console.error("Error loading patients:", error);
            $("#patients tbody").html("<tr><td colspan='7' class='text-center'>Error loading patient data</td></tr>");
        }
    });
  });

  function Remove(id){
    // Only admins should be able to remove patients
    var userRole = "<?php echo $user->role; ?>";
    if (userRole !== "admin") {
        alert("You don't have permission to remove patients");
        return;
    }
    
    var result = confirm("Are you sure you want to delete this patient?"); 
    if (result) {
        $.ajax({
            type: "POST",
            url: "../api/patient/delete.php",
            dataType: "json",
            data: {
                id: id
            },
            error: function (result) {
                alert(result.responseText);
            },
            success: function (result) {
                if (result['status'] == true) {
                    alert("Successfully Removed Patient!");
                    window.location.href = '/patient';
                }
                else {
                    alert(result['message']);
                }
            }
        });
    }
  }
</script>
