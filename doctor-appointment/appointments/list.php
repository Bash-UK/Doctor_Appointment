<?php
session_start();
include('../includes/config.php');
include('../includes/header.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Choose query based on role
if ($role === 'admin') {
    $query = "SELECT a.id, u.name AS patient_name, d.name AS doctor_name, d.specialization,
                     a.appointment_date, a.appointment_time, a.status
              FROM appointments a
              JOIN users u ON a.user_id = u.id
              JOIN doctors d ON a.doctor_id = d.id
              ORDER BY a.appointment_date, a.appointment_time";
} else {
    $query = "SELECT a.id, u.name AS patient_name, d.name AS doctor_name, d.specialization,
                     a.appointment_date, a.appointment_time, a.status
              FROM appointments a
              JOIN users u ON a.user_id = u.id
              JOIN doctors d ON a.doctor_id = d.id
              WHERE a.user_id = $user_id
              ORDER BY a.appointment_date, a.appointment_time";
}

$result = $conn->query($query);

if (!$result) {
    die("❌ Query Error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Appointments List</title>
</head>
<body>
<h2>Appointments List</h2>
<p>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?> |
   <a href="logout.php">Logout</a></p>

<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Patient</th>
        <th>Doctor</th>
        <th>Date</th>
        <th>Time</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>

    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<tr>
                    <td>'.$row['id'].'</td>
                    <td>'.$row['patient_name'].'</td>
                    <td>'.$row['doctor_name'].' ('.$row['specialization'].')</td>
                    <td>'.$row['appointment_date'].'</td>
                    <td>'.$row['appointment_time'].'</td>
                    <td>'.$row['status'].'</td>
                    <td>
                        <a href="update.php?id='.$row['id'].'" onclick="return confirm(\'Are you sure you want to update this appointment?\');">Update</a> | 
                        <a href="cancel.php?id='.$row['id'].'" onclick="return confirm(\'Are you sure you want to cancel this appointment?\');">Cancel</a>
                    </td>
                  </tr>';
        }
    } else {
        echo '<tr><td colspan="7">No appointments found.</td></tr>';
    }
    ?>
</table>

<p><a href="book.php">Book New Appointment</a></p>
<?php include('../includes/footer.php'); ?>
</body>
</html>
