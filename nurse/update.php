<?php
$content = '<div class="row">
              <div class="col-md-12">
                <div class="box box-primary">
                  <div class="box-header with-border">
                    <h3 class="box-title">Update Nurse</h3>
                  </div>
                  <form role="form">
                    <div class="box-body">
                      <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" id="name" placeholder="Enter Name">
                      </div>
                      <div class="form-group">
                        <label>Email address</label>
                        <input type="email" class="form-control" id="email" placeholder="Enter email">
                      </div>
                      <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="form-control" id="password" placeholder="Leave blank to keep current password">
                      </div>
                      <div class="form-group">
                        <label>Phone</label>
                        <input type="text" class="form-control" id="phone" placeholder="Enter Phone">
                      </div>
                    </div>
                    <div class="box-footer">
                      <button type="button" class="btn btn-primary" onclick="UpdateNurse()">Update</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>';
include('../master.php');
?>
<script>
$(document).ready(function(){
    $.get("../api/nurse/read_single.php?id=<?php echo $_GET['id']; ?>", function(data){
        $("#name").val(data.name);
        $("#email").val(data.email);
        $("#phone").val(data.phone);
    });
});

function UpdateNurse(){
    $.ajax({
        type: "POST",
        url: "../api/nurse/update.php",
        dataType: "json",
        data: {
            id: <?php echo $_GET['id']; ?>,
            name: $("#name").val(),
            email: $("#email").val(),
            password: $("#password").val(),
            phone: $("#phone").val()
        },
        success: function(result){
            if(result.status){
                alert("Nurse updated successfully!");
                window.location.href = "/nurse";
            } else {
                alert(result.message);
            }
        },
        error: function(result){
            alert("Error updating nurse!");
        }
    });
}
</script>
