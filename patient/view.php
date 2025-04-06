<?php
$content = '<div class="row">
              <div class="col-md-12">
                <div class="box box-primary">
                  <div class="box-header with-border">
                    <h3 class="box-title">Patient Details</h3>
                  </div>
                  <div class="box-body">
                    <table class="table table-bordered">
                      <tr>
                        <th style="width: 150px">Name</th>
                        <td id="name"></td>
                      </tr>
                      <tr>
                        <th>Phone</th>
                        <td id="phone"></td>
                      </tr>
                      <tr>
                        <th>Gender</th>
                        <td id="gender"></td>
                      </tr>
                      <tr>
                        <th>Health Condition</th>
                        <td id="health_condition"></td>
                      </tr>
                      <tr>
                        <th>Assigned Doctor</th>
                        <td id="doctor_name"></td>
                      </tr>
                      <tr>
                        <th>Assigned Nurse</th>
                        <td id="nurse_name"></td>
                      </tr>
                    </table>
                  </div>
                  <div class="box-footer">
                    <a href="/patient" class="btn btn-default">Back to List</a>
                  </div>
                </div>
              </div>
            </div>';
include('../master.php');
?>
<script>
$(document).ready(function(){
    $.get("../api/patient/read_single.php?id=<?php echo $_GET['id']; ?>", function(data){
        $("#name").text(data.name);
        $("#phone").text(data.phone);
        $("#gender").text(data.gender == 0 ? "Male" : "Female");
        $("#health_condition").text(data.health_condition);
        $("#doctor_name").text(data.doctor_name);
        $("#nurse_name").text(data.nurse_name);
    });
});
</script>