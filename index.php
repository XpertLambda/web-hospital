<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get user role
$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];
$name = $_SESSION['name'];

// Define content based on user role
if($role == 'admin') {
    // Admin dashboard
    $content = '<div class="row">
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3 id="doctorsCount">0</h3>
              <p>Doctors</p>
            </div>
            <div class="icon">
              <i class="fa fa-medkit"></i>
            </div>
            <a href="/doctor" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-green">
            <div class="inner">
              <h3 id="nursesCount">0</h3>
              <p>Nurses</p>
            </div>
            <div class="icon">
              <i class="fa fa-user-md"></i>
            </div>
            <a href="/nurse" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3 id="patientsCount">0</h3>
              <p>Patients</p>
            </div>
            <div class="icon">
              <i class="fa fa-wheelchair"></i>
            </div>
            <a href="/patient" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-red">
            <div class="inner">
              <h3 id="appointmentsCount">0</h3>
              <p>Appointments</p>
            </div>
            <div class="icon">
              <i class="fa fa-calendar"></i>
            </div>
            <a href="/appointment" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-8">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Recent Activities</h3>
            </div>
            <div class="box-body">
              <div id="recentActivities"></div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">System Statistics</h3>
            </div>
            <div class="box-body">
              <div id="systemStats"></div>
            </div>
          </div>
        </div>
      </div>';
} elseif($role == 'doctor') {
    // Doctor dashboard
    $content = '<div class="row">
        <div class="col-lg-4 col-xs-6">
          <div class="small-box bg-blue">
            <div class="inner">
              <h3 id="myPatientsCount">0</h3>
              <p>My Patients</p>
            </div>
            <div class="icon">
              <i class="fa fa-users"></i>
            </div>
            <a href="/my-patients" class="small-box-footer">View patients <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <div class="col-lg-4 col-xs-6">
          <div class="small-box bg-green">
            <div class="inner">
              <h3 id="todayAppointments">0</h3>
              <p>Today\'s Appointments</p>
            </div>
            <div class="icon">
              <i class="fa fa-calendar-check-o"></i>
            </div>
            <a href="/my-appointments" class="small-box-footer">View appointments <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <div class="col-lg-4 col-xs-6">
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3 id="pendingRecords">0</h3>
              <p>Pending Records</p>
            </div>
            <div class="icon">
              <i class="fa fa-file-text"></i>
            </div>
            <a href="/pending-records" class="small-box-footer">Update records <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-8">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Upcoming Appointments</h3>
            </div>
            <div class="box-body">
              <div id="upcomingAppointments"></div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Recent Messages</h3>
            </div>
            <div class="box-body">
              <div id="recentMessages"></div>
            </div>
          </div>
        </div>
      </div>';
} elseif($role == 'nurse') {
    // Nurse dashboard
    $content = '<div class="row">
        <div class="col-lg-6 col-xs-6">
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3 id="assignedPatients">0</h3>
              <p>Assigned Patients</p>
            </div>
            <div class="icon">
              <i class="fa fa-user-plus"></i>
            </div>
            <a href="/assigned-patients" class="small-box-footer">View patients <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <div class="col-lg-6 col-xs-6">
          <div class="small-box bg-green">
            <div class="inner">
              <h3 id="todayTasks">0</h3>
              <p>Today\'s Tasks</p>
            </div>
            <div class="icon">
              <i class="fa fa-tasks"></i>
            </div>
            <a href="/my-tasks" class="small-box-footer">View tasks <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Patient Updates</h3>
            </div>
            <div class="box-body">
              <div id="patientUpdates"></div>
            </div>
          </div>
        </div>
      </div>';
} elseif($role == 'patient') {
    // Patient dashboard
    $content = '<div class="row">
        <div class="col-lg-6 col-xs-6">
          <div class="small-box bg-blue">
            <div class="inner">
              <h3 id="myAppointments">0</h3>
              <p>My Appointments</p>
            </div>
            <div class="icon">
              <i class="fa fa-calendar"></i>
            </div>
            <a href="/my-appointments" class="small-box-footer">View details <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <div class="col-lg-6 col-xs-6">
          <div class="small-box bg-green">
            <div class="inner">
              <h3 id="myRecords">0</h3>
              <p>Medical Records</p>
            </div>
            <div class="icon">
              <i class="fa fa-heartbeat"></i>
            </div>
            <a href="/my-records" class="small-box-footer">View records <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">My Doctor</h3>
            </div>
            <div class="box-body">
              <div id="myDoctor"></div>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Next Appointment</h3>
            </div>
            <div class="box-body">
              <div id="nextAppointment"></div>
            </div>
          </div>
        </div>
      </div>';
} else {
    // Default content for other roles or if role is not set
    $content = '<div class="alert alert-warning">
        <h4><i class="icon fa fa-warning"></i> Welcome!</h4>
        Your dashboard is not configured. Please contact the administrator.
      </div>';
}

include('master.php');
?>

<script>
$(document).ready(function(){
    // Get user role
    var userRole = "<?php echo $role; ?>";
    var userId = <?php echo $user_id; ?>;
    
    if(userRole == 'admin') {
        // Admin dashboard data
        $.get("api/doctor/read.php", function(data){
            $("#doctorsCount").text(data.length);
        });
        
        $.get("api/nurse/read.php", function(data){
            $("#nursesCount").text(data.length);
        });
        
        $.get("api/patient/read.php", function(data){
            $("#patientsCount").text(data.length);
        });
        
        $.get("api/appointment/read.php", function(data){
            $("#appointmentsCount").text(data.length);
        });
        
        // Load recent activities
        $.get("api/audit_log/recent.php", function(data){
            var html = '<ul class="timeline">';
            $.each(data, function(key, log){
                html += '<li><i class="fa fa-clock-o"></i><div class="timeline-item">' +
                       '<h3 class="timeline-header">' + log.action + '</h3>' +
                       '<div class="timeline-body">' + log.details + '</div>' +
                       '<div class="timeline-footer"><small class="text-muted">' + log.created + '</small></div>' +
                       '</div></li>';
            });
            html += '</ul>';
            $("#recentActivities").html(html);
        });
        
        // Load system statistics
        // This would be a custom API endpoint with system stats
        
    } else if(userRole == 'doctor') {
        // Doctor dashboard data
        $.get("api/patient/read_by_doctor.php?doctor_id=" + userId, function(data){
            $("#myPatientsCount").text(data.length);
        });
        
        $.get("api/appointment/read_by_doctor.php?doctor_id=" + userId + "&date=today", function(data){
            $("#todayAppointments").text(data.length);
            
            var html = '<table class="table table-bordered"><thead><tr>' +
                     '<th>Time</th><th>Patient</th><th>Reason</th><th>Action</th>' +
                     '</tr></thead><tbody>';
            $.each(data, function(key, appointment){
                html += '<tr><td>' + appointment.appointment_time + '</td>' +
                       '<td>' + appointment.patient_name + '</td>' +
                       '<td>' + appointment.reason + '</td>' +
                       '<td><a href="/appointment/view.php?id=' + appointment.id + '" class="btn btn-xs btn-info">View</a></td>' +
                       '</tr>';
            });
            html += '</tbody></table>';
            $("#upcomingAppointments").html(html);
        });
        
        $.get("api/medical_record/pending.php?doctor_id=" + userId, function(data){
            $("#pendingRecords").text(data.length);
        });
        
        // Load recent messages
        $.get("api/message/inbox.php?user_id=" + userId + "&unread=1", function(data){
            var html = '<ul class="list-unstyled">';
            $.each(data, function(key, message){
                html += '<li><a href="/messages/view.php?id=' + message.id + '">' +
                       '<strong>' + message.sender_name + '</strong>: ' +
                       message.subject + ' <small class="pull-right text-muted">' + message.created + '</small>' +
                       '</a></li>';
            });
            html += '</ul>';
            $("#recentMessages").html(html);
        });
        
    } else if(userRole == 'nurse') {
        // Nurse dashboard data
        $.get("api/patient/read_by_nurse.php?nurse_id=" + userId, function(data){
            $("#assignedPatients").text(data.length);
        });
        
        // Other nurse-specific data
        
    } else if(userRole == 'patient') {
        // Patient dashboard data
        $.get("api/appointment/read_by_patient.php?patient_id=" + userId, function(data){
            $("#myAppointments").text(data.length);
        });
        
        $.get("api/medical_record/read_by_patient.php?patient_id=" + userId, function(data){
            $("#myRecords").text(data.length);
        });
        
        // Get patient's doctor
        $.get("api/patient/get_doctor.php?patient_id=" + userId, function(data){
            if(data) {
                var html = '<div class="box-profile">' +
                         '<h3 class="profile-username text-center">' + data.name + '</h3>' +
                         '<p class="text-muted text-center">' + data.specialist + '</p>' +
                         '<ul class="list-group list-group-unbordered">' +
                         '<li class="list-group-item"><b>Email</b> <a class="pull-right">' + data.email + '</a></li>' +
                         '<li class="list-group-item"><b>Phone</b> <a class="pull-right">' + data.phone + '</a></li>' +
                         '</ul>' +
                         '<a href="/message/compose.php?to=' + data.id + '" class="btn btn-primary btn-block">Send Message</a>' +
                         '</div>';
                $("#myDoctor").html(html);
            }
        });
        
        // Get next appointment
        $.get("api/appointment/next.php?patient_id=" + userId, function(data){
            if(data) {
                var html = '<div class="info-box">' +
                         '<span class="info-box-icon bg-aqua"><i class="fa fa-calendar-check-o"></i></span>' +
                         '<div class="info-box-content">' +
                         '<span class="info-box-text">Date: ' + data.appointment_date + '</span>' +
                         '<span class="info-box-number">Doctor: ' + data.doctor_name + '</span>' +
                         '<span class="info-box-text">Reason: ' + data.reason + '</span>' +
                         '</div></div>' +
                         '<a href="/appointment/view.php?id=' + data.id + '" class="btn btn-info btn-block">View Details</a>';
                $("#nextAppointment").html(html);
            } else {
                $("#nextAppointment").html('<div class="text-center"><p>No upcoming appointments.</p>' +
                                        '<a href="/appointment/book.php" class="btn btn-success">Book Appointment</a></div>');
            }
        });
    }
});
</script>
