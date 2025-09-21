<?php
header('Content-Type: application/json');
require 'db.php';
$stmt = $pdo->query('SELECT d.id, d.specialty, d.bio, u.name FROM doctors d JOIN users u ON d.user_id = u.id');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['doctors'=>$rows]);
?>
