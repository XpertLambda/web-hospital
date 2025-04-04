<?php
  $content = '<div class="row">
                <div class="col-xs-12">
                  <div class="box">
                    <div class="box-header">
                      <h3 class="box-title">Nurses List</h3>
                    </div>
                    <div class="box-body">
                      <table id="nurses" class="table table-bordered table-hover">
                        <thead>
                          <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>';
  include("../master.php");
?>
<script>
  $(document).ready(function(){
    $.ajax({
        type: "GET",
        url: "../api/nurse/read.php",
        dataType: "json",
        success: function(data) {
            var response="";
            for(var nurse in data){
                response += "<tr>"+
                "<td>"+data[nurse].name+"</td>"+
                "<td>"+data[nurse].email+"</td>"+
                "<td>"+data[nurse].phone+"</td>"+
                "<td><a href='/nurse/update.php?id="+data[nurse].id+"'>Edit</a> | <a href='#' onClick=Remove('"+data[nurse].id+"')>Remove</a></td>"+
                "</tr>";
            }
            $(response).appendTo($("#nurses tbody"));
        }
    });
  });

  function Remove(id){
    var result = confirm("Are you sure you want to delete this nurse?"); 
    if (result) {
        $.ajax({
            type: "POST",
            url: "../api/nurse/delete.php",
            dataType: "json",
            data: {
                id: id
            },
            error: function (result) {
                alert(result.responseText);
            },
            success: function (result) {
                if (result["status"] == true) {
                    alert("Successfully Removed Nurse!");
                    window.location.href = "/nurse";
                }
                else {
                    alert(result["message"]);
                }
            }
        });
    }
  }
</script>
