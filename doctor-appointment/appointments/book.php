

<?php
include('../includes/config.php');

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit;
}

$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);  // In real app, get from session
    $doctor_id = intval($_POST['doctor_id']);
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];

    // Simple conflict check
    $stmt = $conn->prepare("SELECT * FROM appointments WHERE doctor_id=? AND appointment_date=? AND appointment_time=?");
    $stmt->bind_param("iss", $doctor_id, $appointment_date, $appointment_time);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $message = "❌ This slot is already booked. Choose another time.";
    } else {
        $stmt = $conn->prepare("INSERT INTO appointments (user_id, doctor_id, appointment_date, appointment_time) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $user_id, $doctor_id, $appointment_date, $appointment_time);
        if ($stmt->execute()) {
            $message = "✅ Appointment booked successfully!";
        } else {
            $message = "❌ Error booking appointment.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Appointment</title>
</head>
<body>
<h2>Book Appointment</h2>
<?php if($message) echo "<p>$message</p>"; ?>

<form method="POST">
    <label>User:</label>
    <select name="user_id" required>
        <?php
        $users = $conn->query("SELECT id, name FROM users");
        while ($u = $users->fetch_assoc()) {
            echo "<option value='{$u['id']}'>{$u['name']}</option>";
        }
        ?>
    </select><br><br>

    <label>Doctor:</label>
    <select name="doctor_id" required>
        <?php
        $doctors = $conn->query("SELECT id, name, specialization FROM doctors");
        while ($d = $doctors->fetch_assoc()) {
            echo "<option value='{$d['id']}'>{$d['name']} ({$d['specialization']})</option>";
        }
        ?>
    </select><br><br>

    <label>Date:</label>
    <input type="date" name="appointment_date" required><br><br>

    <label>Time:</label>
    <input type="time" name="appointment_time" required><br><br>

    <button type="submit">Book Appointment</button>
</form>

<p><a href="list.php">View Appointments</a></p>
</body>
</html>
