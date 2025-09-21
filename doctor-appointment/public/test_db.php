<?php
include('../includes/header.php');
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "doctor_app";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully<br>";
$result = $conn->query("SELECT * FROM users");
if ($result->num_rows > 0) {
    echo "<h3>Users:</h3><ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>" . $row["id"] . " - " . $row["name"] . " (" . $row["email"] . ")</li>";
    }
    echo "</ul>";
}
$conn->close();
?>