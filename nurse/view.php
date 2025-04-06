<?php
$content = '<div class="row">
              <div class="col-md-12">
                <div class="box box-primary">
                  <div class="box-header with-border">
                    <h3 class="box-title">Nurse Details</h3>
                  </div>
                  <div class="box-body">
                    <table class="table table-bordered">
                      <tr>
                        <th style="width: 150px">Name</th>
                        <td id="name"></td>
                      </tr>
                      <tr>
                        <th>Email</th>
                        <td id="email"></td>
                      </tr>
                      <tr>
                        <th>Phone</th>
                        <td id="phone"></td>
                      </tr>
                    </table>
                  </div>
                  <div class="box-footer">
                    <a href="/nurse" class="btn btn-default">Back to List</a>
                  </div>
                </div>
              </div>
            </div>';
include('../master.php');
?>
<script>
$(document).ready(function(){
    $.get("../api/nurse/read_single.php?id=<?php echo $_GET['id']; ?>", function(data){
        $("#name").text(data.name);
        $("#email").text(data.email);
        $("#phone").text(data.phone);
    });
});
</script>