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
$message = "";

// Fetch current appointment details with user and doctor names
$stmt = $conn->prepare("
    SELECT a.*, u.name AS patient_name, d.name AS doctor_name, d.specialization
    FROM appointments a
    JOIN users u ON a.user_id = u.id
    JOIN doctors d ON a.doctor_id = d.id
    WHERE a.id=?
");
$stmt->bind_param("i", $appointment_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("❌ Appointment not found.");
}

$appointment = $result->fetch_assoc();
// Access control: only admin or owner can update
if ($_SESSION['role'] !== 'admin' && $appointment['user_id'] != $_SESSION['user_id']) {
    die("❌ Access denied.");
}


// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];

    // Conflict check
    $stmt = $conn->prepare("
        SELECT * FROM appointments
        WHERE doctor_id=? AND appointment_date=? AND appointment_time=? AND id != ?
    ");
    $stmt->bind_param("issi", $appointment['doctor_id'], $appointment_date, $appointment_time, $appointment_id);
    $stmt->execute();
    $conflict = $stmt->get_result();

    if ($conflict->num_rows > 0) {
        $message = "❌ This slot is already booked. Choose another time.";
    } else {
        $stmt = $conn->prepare("UPDATE appointments SET appointment_date=?, appointment_time=? WHERE id=?");
        $stmt->bind_param("ssi", $appointment_date, $appointment_time, $appointment_id);
        if ($stmt->execute()) {
            $message = "✅ Appointment updated successfully!";
            header("Location: list.php?msg=" . urlencode($message));
            exit;
        } else {
            $message = "❌ Error updating appointment.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Appointment</title>
</head>
<body>
<h2>Update Appointment</h2>
<?php if($message) echo "<p>$message</p>"; ?>

<form method="POST" onsubmit="return confirm('Are you sure you want to update this appointment?');">
    <p><strong>Patient:</strong> <?php echo htmlspecialchars($appointment['patient_name']); ?></p>
    <p><strong>Doctor:</strong> <?php echo htmlspecialchars($appointment['doctor_name'] . " (" . $appointment['specialization'] . ")"); ?></p>

    <label>Date:</label>
    <input type="date" name="appointment_date" value="<?php echo $appointment['appointment_date']; ?>" required><br><br>

    <label>Time:</label>
    <input type="time" name="appointment_time" value="<?php echo $appointment['appointment_time']; ?>" required><br><br>

    <button type="submit">Update Appointment</button>
</form>

<p><a href="list.php">Back to Appointments List</a></p>
<?php include('../includes/footer.php'); ?>
</body>
</html>
