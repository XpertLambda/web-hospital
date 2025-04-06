<?php
$content = '<div class="row">
              <div class="col-lg-4 col-xs-6">
                <div class="small-box bg-aqua">
                  <div class="inner">
                    <h3 id="patientCount">0</h3>
                    <p>My Patients</p>
                  </div>
                  <div class="icon">
                    <i class="fa fa-user-injured"></i>
                  </div>
                  <a href="/doctor/patients.php" class="small-box-footer">View My Patients <i class="fa fa-arrow-circle-right"></i></a>
                </div>
              </div>
              <div class="col-lg-4 col-xs-6">
                <div class="small-box bg-green">
                  <div class="inner">
                    <h3 id="appointmentCount">0</h3>
                    <p>Today\'s Appointments</p>
                  </div>
                  <div class="icon">
                    <i class="fa fa-calendar-check"></i>
                  </div>
                  <a href="/doctor/appointments.php" class="small-box-footer">View Appointments <i class="fa fa-arrow-circle-right"></i></a>
                </div>
              </div>
              <div class="col-lg-4 col-xs-6">
                <div class="small-box bg-yellow">
                  <div class="inner">
                    <h3>Profile</h3>
                    <p>My Account</p>
                  </div>
                  <div class="icon">
                    <i class="fa fa-user-md"></i>
                  </div>
                  <a href="/profile.php" class="small-box-footer">Update Profile <i class="fa fa-arrow-circle-right"></i></a>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-8">
                <div class="box box-primary">
                  <div class="box-header with-border">
                    <h3 class="box-title">My Patients</h3>
                  </div>
                  <div class="box-body">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>Name</th>
                          <th>Health Condition</th>
                          <th>Assigned Nurse</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody id="myPatients"></tbody>
                    </table>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="box box-info">
                  <div class="box-header with-border">
                    <h3 class="box-title">Quick Notes</h3>
                  </div>
                  <div class="box-body">
                    <div class="form-group">
                      <textarea class="form-control" rows="8" id="doctorNotes" placeholder="Write your notes here..."></textarea>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="saveNotes()">Save Notes</button>
                  </div>
                </div>
              </div>
            </div>';
include('../master.php');
?>
<script>
$(document).ready(function(){
    // Load doctor dashboard data
    $.get("../api/dashboard/doctor_stats.php", function(data){
        $("#patientCount").text(data.patientCount);
        $("#appointmentCount").text(data.appointmentCount);
        
        // Load saved notes if any
        if(localStorage.getItem("doctorNotes")){
            $("#doctorNotes").val(localStorage.getItem("doctorNotes"));
        }
        
        // My patients
        var patientsHtml = "";
        data.patients.forEach(function(patient){
            patientsHtml += "<tr>";
            patientsHtml += "<td>" + patient.name + "</td>";
            patientsHtml += "<td>" + patient.health_condition + "</td>";
            patientsHtml += "<td>" + patient.nurse_name + "</td>";
            patientsHtml += "<td>";
            patientsHtml += "<a href='/patient/view.php?id=" + patient.id + "' class='btn btn-xs btn-info'><i class='fa fa-eye'></i></a> ";
            patientsHtml += "<a href='/doctor/update_patient.php?id=" + patient.id + "' class='btn btn-xs btn-primary'><i class='fa fa-edit'></i></a>";
            patientsHtml += "</td>";
            patientsHtml += "</tr>";
        });
        $("#myPatients").html(patientsHtml);
    });
});

function saveNotes(){
    localStorage.setItem("doctorNotes", $("#doctorNotes").val());
    alert("Notes saved successfully!");
}
</script>