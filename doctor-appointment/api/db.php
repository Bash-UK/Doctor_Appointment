<?php
// Simple DB connection file. Update credentials below.
$DB_HOST = '127.0.0.1';
$DB_NAME = 'doctor_app';
$DB_USER = 'root';
$DB_PASS = ''; // <-- set your MySQL root password

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}
?>