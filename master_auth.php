<?php
// Start session
session_start();

// Include authentication middleware
require_once(__DIR__ . '/api/middleware/auth_middleware.php');

// Check if user is authenticated
$user = Auth::isAuthenticated();
$isLoggedIn = $user !== false;

// Handle page that requires authentication
if (isset($requireAuth) && $requireAuth && !$isLoggedIn) {
    header("Location: /auth/login.php");
    exit;
}

// Handle page that requires specific permission
if (isset($requiredPermission) && !Auth::hasPermission($requiredPermission)) {
    header("Location: /auth/access-denied.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Medical Center</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="../bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="../bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="../dist/css/skins/skin-blue.min.css">
  <!-- Google Font -->
  <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
  <!-- Main Header -->
  <header class="main-header">
    <!-- Logo -->
    <a href="/index.php" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>M</b>C</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>Medi</b>CENTER</span>
    </a>
    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- User Account Menu -->
          <li class="dropdown user user-menu">
            <!-- Menu Toggle Button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <!-- The user image in the navbar-->
              <img src="../dist/img/user2-160x160.jpg" class="user-image" alt="User Image">
              <!-- hidden-xs hides the username on small devices so only the image appears. -->
              <span class="hidden-xs"><?php echo $isLoggedIn ? $user->name : 'Guest'; ?></span>
            </a>
            <ul class="dropdown-menu">
              <!-- The user image in the menu -->
              <li class="user-header">
                <img src="../dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
                <p>
                  <?php if($isLoggedIn): ?>
                    <?php echo $user->name; ?> - <?php echo ucfirst($user->role); ?>
                    <small>Member since <?php echo date('M. Y', strtotime($user->created)); ?></small>
                  <?php else: ?>
                    Guest
                  <?php endif; ?>
                </p>
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                <?php if($isLoggedIn): ?>
                  <div class="pull-left">
                    <a href="/auth/profile.php" class="btn btn-default btn-flat">Profile</a>
                  </div>
                  <div class="pull-right">
                    <a href="/auth/logout.php" class="btn btn-default btn-flat">Sign out</a>
                  </div>
                <?php else: ?>
                  <div class="pull-left">
                    <a href="/auth/login.php" class="btn btn-default btn-flat">Login</a>
                  </div>
                  <div class="pull-right">
                    <a href="/auth/register.php" class="btn btn-default btn-flat">Register</a>
                  </div>
                <?php endif; ?>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>
  
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="../dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p><?php echo $isLoggedIn ? $user->name : 'Guest'; ?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> <?php echo $isLoggedIn ? 'Online' : 'Offline'; ?></a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MAIN NAVIGATION</li>
        <li><a href="/index.php"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
        
        <?php if($isLoggedIn): ?>
          <?php if(Auth::hasPermission('view_doctors') || Auth::hasRole('admin')): ?>
          <li><a href="/doctor"><i class="fa fa-medkit"></i> <span>Doctors</span></a></li>
          <?php endif; ?>
          
          <?php if(Auth::hasPermission('view_nurses') || Auth::hasRole('admin')): ?>
          <li><a href="/nurse"><i class="fa fa-user-md"></i> <span>Nurses</span></a></li>
          <?php endif; ?>
          
          <?php if(Auth::hasPermission('view_patients') || Auth::hasRole('admin') || Auth::hasRole('doctor')): ?>
          <li><a href="/patient"><i class="fa fa-wheelchair"></i> <span>Patients</span></a></li>
          <?php endif; ?>
          
          <?php if(Auth::hasPermission('view_appointments') || Auth::hasRole('admin') || Auth::hasRole('doctor') || Auth::hasRole('patient')): ?>
          <li><a href="/appointment"><i class="fa fa-calendar"></i> <span>Appointments</span></a></li>
          <?php endif; ?>
          
          <?php if(Auth::hasRole('patient')): ?>
          <li><a href="/medical-records"><i class="fa fa-file-text"></i> <span>Medical Records</span></a></li>
          <li><a href="/messages"><i class="fa fa-envelope"></i> <span>Messages</span></a></li>
          <?php endif; ?>
          
          <?php if(Auth::hasRole('admin')): ?>
          <li class="treeview">
            <a href="#"><i class="fa fa-cog"></i> <span>Administration</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li><a href="/admin/users"><i class="fa fa-users"></i> User Management</a></li>
              <li><a href="/admin/roles"><i class="fa fa-key"></i> Role Management</a></li>
              <li><a href="/admin/system-logs"><i class="fa fa-list"></i> System Logs</a></li>
            </ul>
          </li>
          <?php endif; ?>
          
        <?php endif; ?>
      </ul>
      <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?php echo isset($pageTitle) ? $pageTitle : 'Medical Center'; ?>
        <small><?php echo isset($pageSubTitle) ? $pageSubTitle : ''; ?></small>
      </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <?php echo isset($content) ? $content : ''; ?>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 1.0.0
    </div>
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">Medical Center</a>.</strong> All rights reserved.
  </footer>
  
  <!-- Add the sidebar's background. This div must be placed
  immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->
<!-- jQuery 3 -->
<script src="../bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- DataTables -->
<script src="../bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="../bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>
</body>
</html>