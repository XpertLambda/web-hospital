<?php
$content = '<div class="row">
              <div class="col-xs-12">
                <div class="box">
                  <div class="box-header">
                    <h3 class="box-title">Patients List</h3>
                  </div>
                  <div class="box-body">
                    <table id="patients" class="table table-bordered table-hover">
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
                      <tbody></tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>';
include('../master.php');
?>
<script>
$(document).ready(function(){
    $.ajax({
        type: "GET",
        url: "../api/patient/read.php",
        dataType: "json",
        success: function(data) {
            var response = "";
            data.forEach(function(patient){
                response += "<tr>" +
                    "<td>" + patient.name + "</td>" +
                    "<td>" + patient.phone + "</td>" +
                    "<td>" + (patient.gender == 0 ? "Male" : "Female") + "</td>" +
                    "<td>" + patient.health_condition + "</td>" +
                    "<td>" + patient.doctor_name + "</td>" +
                    "<td>" + patient.nurse_name + "</td>" +
                    "<td><a href='/patient/update.php?id="+patient.id+"'>Edit</a> | " +
                    "<a href='#' onclick='RemovePatient("+patient.id+")'>Delete</a></td>" +
                    "</tr>";
            });
            $("#patients tbody").html(response);
        }
    });
});

function RemovePatient(id){
    if(confirm("Are you sure you want to delete this patient?")){
        $.ajax({
            type: "POST",
            url: "../api/patient/delete.php",
            dataType: "json",
            data: { id: id },
            success: function(result){
                if(result.status){
                    alert("Patient deleted successfully!");
                    location.reload();
                } else {
                    alert(result.message);
                }
            }
        });
    }
}
</script>
