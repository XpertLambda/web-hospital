<?php 
  $content = '<div class="row">
                <div class="col-md-12">
                  <div class="box box-primary">
                    <div class="box-header with-border">
                      <h3 class="box-title">Add Nurse</h3>
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
                          <input type="password" class="form-control" id="password" placeholder="Password">
                        </div>
                        <div class="form-group">
                          <label>Phone</label>
                          <input type="text" class="form-control" id="phone" placeholder="Enter Phone">
                        </div>
                      </div>
                      <div class="box-footer">
                        <input type="button" class="btn btn-primary" onClick="AddNurse()" value="Submit">
                      </div>
                    </form>
                  </div>
                </div>
              </div>';
  include('../master.php');
?>
<script>
  function AddNurse(){
    $.ajax({
        type: "POST",
        url: "../api/nurse/create.php",
        dataType: "json",
        data: {
            name: $("#name").val(),
            email: $("#email").val(),        
            password: $("#password").val(),
            phone: $("#phone").val()
        },
        error: function (result) {
            alert(result.responseText);
        },
        success: function (result) {
            if (result["status"] == true) {
                alert("Successfully Added New Nurse!");
                window.location.href = "/nurse";
            }
            else {
                alert(result["message"]);
            }
        }
    });
  }
</script>
