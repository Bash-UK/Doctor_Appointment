<?php
// Shared header - includes Bootstrap 5 and Select2 CSS + jQuery
?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Doctor Appointment</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
/* small adjustments */
.container-main { padding: 20px; }
.navbar-brand { font-weight: 600; }
.select2-container { min-width: 200px; }
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="/doctor-appointment/public/index.php">Doctor Appointment</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navmenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navmenu">
      <ul class="navbar-nav ms-auto">
        <?php if(session_status() != PHP_SESSION_ACTIVE) session_start(); ?>
        <?php if(isset($_SESSION['user_name'])): ?>
            <li class="nav-item"><a class="nav-link" href="/doctor-appointment/public/index.php">Home</a></li>
            <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <li class="nav-item"><a class="nav-link" href="/doctor-appointment/appointments/admin_dashboard.php">Admin Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="/doctor-appointment/appointments/create_appointment.php">Create Appointment</a></li>
            <?php elseif(isset($_SESSION['role']) && $_SESSION['role'] === 'doctor'): ?>
                <li class="nav-item"><a class="nav-link" href="/doctor-appointment/appointments/doctor_dashboard.php">Doctor Dashboard</a></li>
            <?php else: ?>
                <li class="nav-item"><a class="nav-link" href="/doctor-appointment/appointments/list.php">My Appointments</a></li>
            <?php endif; ?>
            <li class="nav-item"><a class="nav-link" href="/doctor-appointment/public/logout.php">Logout (<?php echo htmlspecialchars($_SESSION['user_name']); ?>)</a></li>
        <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="/doctor-appointment/public/login.php">Login</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<div class="container container-main">
