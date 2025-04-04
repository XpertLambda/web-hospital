<?php
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
      </div>';

include('master.php');
?>

<script>
$(document).ready(function(){
    // Get doctors count
    $.get("api/doctor/read.php", function(data){
        $("#doctorsCount").text(data.length);
    });
    
    // Get nurses count
    $.get("api/nurse/read.php", function(data){
        $("#nursesCount").text(data.length);
    });
    
    // Get patients count
    $.get("api/patient/read.php", function(data){
        $("#patientsCount").text(data.length);
    });
});
</script>
