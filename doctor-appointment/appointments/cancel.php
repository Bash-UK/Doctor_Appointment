<?php
include('../includes/config.php');
include('../includes/header.php');

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("❌ Appointment ID is required.");
}

$appointment_id = intval($_GET['id']);
// Fetch appointment to check ownership
$stmt = $conn->prepare("SELECT user_id FROM appointments WHERE id=?");
$stmt->bind_param("i", $appointment_id);
$stmt->execute();
$res = $stmt->get_result();
$appointment = $res->fetch_assoc();

// Access control: only admin or owner can cancel
if ($_SESSION['role'] !== 'admin' && $appointment['user_id'] != $_SESSION['user_id']) {
    die("❌ Access denied.");
}


// Update appointment status to cancelled
$stmt = $conn->prepare("UPDATE appointments SET status='cancelled' WHERE id=?");
$stmt->bind_param("i", $appointment_id);

if ($stmt->execute()) {
    $message = "✅ Appointment cancelled successfully.";
} else {
    $message = "❌ Error cancelling appointment.";
}

// Redirect back to list with a message (optional: you can use GET param)
header("Location: list.php?msg=" . urlencode($message));
exit;
?>
