<?php
session_start();
include('../includes/config.php');
include('../includes/header.php');

// ✅ Allow only admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("❌ Access denied. Admins only.");
}

$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patient_name = $_POST['patient_name'];
    $doctor_id = $_POST['doctor_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $status = $_POST['status'];

    // Insert into appointments (now including patient_name)
    $stmt = $conn->prepare("INSERT INTO appointments (patient_name, user_id, doctor_id, appointment_date, appointment_time, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("siisss", $patient_name, $_SESSION['user_id'], $doctor_id, $date, $time, $status);

    if ($stmt->execute()) {
        $message = "✅ Appointment created successfully!";
        header("Location: admin_dashboard.php?msg=success");
        exit();
    } else {
        $message = "❌ Error: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch doctors for dropdown
$doctors = $conn->query("SELECT id, name, specialization FROM doctors ORDER BY name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Appointment</title>
</head>
<body>
    <h2>Create New Appointment</h2>

    <?php if ($message) echo "<p>$message</p>"; ?>

    <form method="POST">
        <label>Patient Name:</label>
        <input type="text" name="patient_name" required><br><br>

        <label>Doctor:</label>
        <select name="doctor_id" required>
            <option value="">-- Select Doctor --</option>
            <?php while($doc = $doctors->fetch_assoc()): ?>
                <option value="<?php echo $doc['id']; ?>">
                    <?php echo $doc['name'] . " (" . $doc['specialization'] . ")"; ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <label>Date:</label>
        <input type="date" name="date" required><br><br>

        <label>Time:</label>
        <input type="time" name="time" required><br><br>

        <label>Status:</label>
        <select name="status">
            <option value="pending">Pending</option>
            <option value="confirmed">Confirmed</option>
            <option value="cancelled">Cancelled</option>
        </select><br><br>

        <button type="submit">Create Appointment</button>
    </form>

    <br>
    <a href="admin_dashboard.php">⬅ Back to Dashboard</a>
<?php include('../includes/footer.php'); ?>
</body>
</html>
