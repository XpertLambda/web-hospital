<?php
// Start session
session_start();

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: /index.php");
    exit;
}

$content = '<div class="login-box">
  <div class="login-logo">
    <a href="/index.php"><b>Medi</b>CENTER</a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">Forgot Password</p>
    <div id="error-message" class="alert alert-danger" style="display:none;"></div>
    <div id="success-message" class="alert alert-success" style="display:none;"></div>
    
    <form id="forgot-password-form">
      <div class="form-group has-feedback">
        <input type="email" class="form-control" id="email" placeholder="Email">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-12">
          <button type="button" id="reset-button" class="btn btn-primary btn-block btn-flat">Request Password Reset</button>
        </div>
      </div>
    </form>

    <a href="/auth/login.php">Back to login</a><br>
    <a href="/auth/register.php" class="text-center">Register a new account</a>

  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->';

// Use a simple template
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Medical Center | Forgot Password</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="../bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="../bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/AdminLTE.min.css">

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition login-page">

<?php echo $content; ?>

<!-- jQuery 3 -->
<script src="../bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script>
  $(function () {
    // Reset password request
    $('#reset-button').click(function() {
      var email = $('#email').val();

      if (!email) {
        $('#error-message').text('Email is required.').show();
        $('#success-message').hide();
        return;
      }

      $.ajax({
        url: '/api/auth/request_reset.php',
        type: 'POST',
        dataType: 'json',
        contentType: 'application/json',
        data: JSON.stringify({
          email: email
        }),
        success: function(response) {
          $('#error-message').hide();
          $('#success-message').text('If the email is registered, password reset instructions have been sent.').show();
          $('#forgot-password-form')[0].reset();
        },
        error: function(xhr) {
          var response = JSON.parse(xhr.responseText);
          $('#error-message').text(response.message).show();
          $('#success-message').hide();
        }
      });
    });
  });
</script>
</body>
</html>