<?php
// Set required permission
$requiredPermission = 'view_nurses';
$pageTitle = "Nurses";
$pageSubTitle = "Manage nurses";

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

// Check permissions - only certain roles can view nurses
$allowAccess = false;
if ($user->role === 'admin' || Auth::hasPermission('view_nurses')) {
    $allowAccess = true;
} elseif (in_array($user->role, ['doctor', 'patient', 'nurse'])) {
    $allowAccess = true; // They will see filtered data
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
                    <h3 class="box-title">Nurses</h3>';

// Only admins can add new nurses
if ($user->role === 'admin') {
    $content .= '<div class="pull-right">
                  <a href="/nurse/create.php" class="btn btn-primary">Add New Nurse</a>
                </div>';
}

$content .= '    </div>
                  <div class="box-body">
                    <table id="nurses" class="table table-bordered table-striped">
                      <thead>
                      <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
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
        url: "../api/nurse/read.php",
        dataType: 'json',
        success: function (data) {
            var response = "";
            for (var nurse in data) {
                response += "<tr>"+
                "<td>"+data[nurse].name+"</td>"+
                "<td>"+data[nurse].email+"</td>"+
                "<td>"+data[nurse].phone+"</td>"+
                "<td>";
                
                // Action buttons based on role
                var userRole = "<?php echo $user->role; ?>";
                if (userRole === "admin") {
                    response += "<a href='/nurse/update.php?id="+data[nurse].id+"'>Edit</a> | "+
                                "<a href='#' onClick=Remove('"+data[nurse].id+"')>Remove</a>";
                } else {
                    response += "<a href='/nurse/view.php?id="+data[nurse].id+"'>View</a>";
                }
                
                response += "</td></tr>";
            }
            $(response).appendTo($("#nurses tbody"));
        },
        error: function(xhr, status, error) {
            console.error("Error loading nurses:", error);
            $("#nurses tbody").html("<tr><td colspan='4' class='text-center'>Error loading nurse data</td></tr>");
        }
    });
  });

  function Remove(id){
    // Only admins should be able to remove nurses
    var userRole = "<?php echo $user->role; ?>";
    if (userRole !== "admin") {
        alert("You don't have permission to remove nurses");
        return;
    }
    
    var result = confirm("Are you sure you want to delete this nurse?"); 
    if (result) {
        $.ajax({
            type: "POST",
            url: "../api/nurse/delete.php",
            dataType: "json",
            data: {
                id: id
            },
            error: function (result) {
                alert(result.responseText);
            },
            success: function (result) {
                if (result["status"] == true) {
                    alert("Successfully Removed Nurse!");
                    window.location.href = "/nurse";
                }
                else {
                    alert(result["message"]);
                }
            }
        });
    }
  }
</script>
