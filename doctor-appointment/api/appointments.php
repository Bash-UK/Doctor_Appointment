<?php
header('Content-Type: application/json');
session_start();
require 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // list appointments (filter by role)
    $user = $_SESSION['user'] ?? null;
    if (!$user) { http_response_code(401); echo json_encode(['error'=>'Unauthorized']); exit; }

    if ($user['role'] === 'patient') {
        $stmt = $pdo->prepare('SELECT a.*, d.specialty, u.name as doctor_name FROM appointments a JOIN doctors d ON a.doctor_id=d.id JOIN users u ON d.user_id=u.id WHERE a.patient_id = ? ORDER BY a.appointment_date,a.appointment_time');
        $stmt->execute([$user['id']]);
    } elseif ($user['role'] === 'doctor') {
        // find doctor's id
        $stmtDoc = $pdo->prepare('SELECT id FROM doctors WHERE user_id = ? LIMIT 1');
        $stmtDoc->execute([$user['id']]);
        $doc = $stmtDoc->fetch(PDO::FETCH_ASSOC);
        $docId = $doc ? $doc['id'] : 0;
        $stmt = $pdo->prepare('SELECT a.*, p.name as patient_name FROM appointments a JOIN users p ON a.patient_id=p.id WHERE a.doctor_id = ? ORDER BY a.appointment_date,a.appointment_time');
        $stmt->execute([$docId]);
    } else {
        // admin: list all
        $stmt = $pdo->query('SELECT a.*, p.name as patient_name, u.name as doctor_name FROM appointments a JOIN users p ON a.patient_id=p.id JOIN doctors d ON a.doctor_id=d.id JOIN users u ON d.user_id=u.id ORDER BY a.appointment_date,a.appointment_time');
    }
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['appointments'=>$rows]);
    exit;
}

if ($method === 'POST') {
    // create appointment (patient)
    $user = $_SESSION['user'] ?? null;
    if (!$user || $user['role'] !== 'patient') { http_response_code(401); echo json_encode(['error'=>'Only patients can book']); exit; }

    $data = json_decode(file_get_contents('php://input'), true);
    $doctor_id = $data['doctor_id'] ?? null;
    $date = $data['date'] ?? null;
    $time = $data['time'] ?? null;
    if (!$doctor_id || !$date || !$time) { http_response_code(400); echo json_encode(['error'=>'Missing fields']); exit; }

    $stmt = $pdo->prepare('INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time) VALUES (?,?,?,?)');
    try {
        $stmt->execute([$user['id'], $doctor_id, $date, $time]);
        echo json_encode(['success'=>true,'id'=>$pdo->lastInsertId()]);
    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode(['error'=>$e->getMessage()]);
    }
    exit;
}

if ($method === 'PUT') {
    // update status (doctors/admin)
    parse_str(file_get_contents('php://input'), $data);
    $user = $_SESSION['user'] ?? null;
    if (!$user) { http_response_code(401); echo json_encode(['error'=>'Unauthorized']); exit; }
    $id = $data['id'] ?? null;
    $status = $data['status'] ?? null;
    if (!$id || !$status) { http_response_code(400); echo json_encode(['error'=>'Missing fields']); exit; }

    // basic permission: doctor of appointment or admin can change
    if ($user['role'] === 'doctor') {
        // check appointment belongs to doctor
        $stmtCheck = $pdo->prepare('SELECT a.id FROM appointments a JOIN doctors d ON a.doctor_id=d.id WHERE a.id=? AND d.user_id=?');
        $stmtCheck->execute([$id,$user['id']]);
        if (!$stmtCheck->fetch()) { http_response_code(403); echo json_encode(['error'=>'Forbidden']); exit; }
    }

    $stmt = $pdo->prepare('UPDATE appointments SET status=? WHERE id=?');
    $stmt->execute([$status,$id]);
    echo json_encode(['success'=>true]);
    exit;
}

http_response_code(405);
echo json_encode(['error'=>'Method not allowed']);
?>