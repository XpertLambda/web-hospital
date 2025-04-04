<?php
$content = '<div class="row">
              <div class="col-md-12">
                <div class="box box-primary">
                  <div class="box-header with-border">
                    <h3 class="box-title">Add Patient</h3>
                  </div>
                  <form role="form">
                    <div class="box-body">
                      <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" id="name" placeholder="Enter Name">
                      </div>
                      <div class="form-group">
                        <label>Phone</label>
                        <input type="text" class="form-control" id="phone" placeholder="Enter Phone">
                      </div>
                      <div class="form-group">
                        <label>Gender</label>
                        <div class="radio">
                          <label><input type="radio" name="gender" value="0" checked>Male</label>
                        </div>
                        <div class="radio">
                          <label><input type="radio" name="gender" value="1">Female</label>
                        </div>
                      </div>
                      <div class="form-group">
                        <label>Health Condition</label>
                        <textarea class="form-control" id="health_condition" rows="3"></textarea>
                      </div>
                      <div class="form-group">
                        <label>Select Doctor</label>
                        <select class="form-control" id="doctor_id"></select>
                      </div>
                      <div class="form-group">
                        <label>Select Nurse</label>
                        <select class="form-control" id="nurse_id"></select>
                      </div>
                    </div>
                    <div class="box-footer">
                      <button type="button" class="btn btn-primary" onclick="AddPatient()">Submit</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>';
include('../master.php');
?>
<script>
$(document).ready(function(){
    // Load doctors
    $.get("../api/doctor/read.php", function(data){
        var options = "";
        data.forEach(function(doctor){
            options += "<option value='"+doctor.id+"'>"+doctor.name+"</option>";
        });
        $("#doctor_id").html(options);
    });
    
    // Load nurses
    $.get("../api/nurse/read.php", function(data){
        var options = "";
        data.forEach(function(nurse){
            options += "<option value='"+nurse.id+"'>"+nurse.name+"</option>";
        });
        $("#nurse_id").html(options);
    });
});

function AddPatient(){
    $.ajax({
        type: "POST",
        url: "../api/patient/create.php",
        dataType: "json",
        data: {
            name: $("#name").val(),
            phone: $("#phone").val(),
            gender: $("input[name='gender']:checked").val(),
            health_condition: $("#health_condition").val(),
            doctor_id: $("#doctor_id").val(),
            nurse_id: $("#nurse_id").val()
        },
        success: function(result){
            if(result.status){
                alert("Patient added successfully!");
                window.location.href = "/patient";
            } else {
                alert(result.message);
            }
        },
        error: function(result){
            alert("Error adding patient!");
        }
    });
}
</script>
