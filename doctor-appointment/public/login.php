<?php
session_start();
include('../includes/config.php');
include('../includes/header.php');

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password']; // plain-text for now

    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if ($password === $user['password']) { // plain-text check
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role'] = $user['role'];   // important!
            // After successful login
            if ($user['role'] === 'admin') {
            header("Location: ../appointments/admin_dashboard.php");
            } elseif ($user['role'] === 'doctor') {
                header("Location: ../appointments/doctor_dashboard.php");
                exit;
            } else {
            header("Location: ../appointments/list.php");
            }
            exit;

        } else {
            $message = "❌ Incorrect password";
        }
    } else {
        $message = "❌ Email not found";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
<h2>Login</h2>
<?php if($message) echo "<p style='color:red;'>$message</p>"; ?>
<form method="POST">
    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>
    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>
    <button type="submit">Login</button>
</form>
<?php include('../includes/footer.php'); ?>
</body>
</html>
