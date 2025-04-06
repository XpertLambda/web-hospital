<?php
$content = '<div class="row">
              <div class="col-md-12">
                <div class="box box-primary">
                  <div class="box-header with-border">
                    <h3 class="box-title">Update Patient</h3>
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
                          <label><input type="radio" name="gender" id="gender0" value="0">Male</label>
                        </div>
                        <div class="radio">
                          <label><input type="radio" name="gender" id="gender1" value="1">Female</label>
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
                      <button type="button" class="btn btn-primary" onclick="UpdatePatient()">Update</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>';
include('../master.php');
?>
<script>
$(document).ready(function(){
    // Load patient data
    $.get("../api/patient/read_single.php?id=<?php echo $_GET['id']; ?>", function(data){
        $("#name").val(data.name);
        $("#phone").val(data.phone);
        $("#gender" + data.gender).prop("checked", true);
        $("#health_condition").val(data.health_condition);
        
        // Load doctors and select current doctor
        $.get("../api/doctor/read.php", function(doctors){
            var options = "";
            doctors.forEach(function(doctor){
                var selected = (doctor.id == data.doctor_id) ? "selected" : "";
                options += "<option value='"+doctor.id+"' "+selected+">"+doctor.name+"</option>";
            });
            $("#doctor_id").html(options);
        });
        
        // Load nurses and select current nurse
        $.get("../api/nurse/read.php", function(nurses){
            var options = "";
            nurses.forEach(function(nurse){
                var selected = (nurse.id == data.nurse_id) ? "selected" : "";
                options += "<option value='"+nurse.id+"' "+selected+">"+nurse.name+"</option>";
            });
            $("#nurse_id").html(options);
        });
    });
});

function UpdatePatient(){
    $.ajax({
        type: "POST",
        url: "../api/patient/update.php",
        dataType: "json",
        data: {
            id: <?php echo $_GET['id']; ?>,
            name: $("#name").val(),
            phone: $("#phone").val(),
            gender: $("input[name='gender']:checked").val(),
            health_condition: $("#health_condition").val(),
            doctor_id: $("#doctor_id").val(),
            nurse_id: $("#nurse_id").val()
        },
        success: function(result){
            if(result.status){
                alert("Patient updated successfully!");
                window.location.href = "/patient";
            } else {
                alert(result.message);
            }
        },
        error: function(result){
            alert("Error updating patient!");
        }
    });
}
</script>