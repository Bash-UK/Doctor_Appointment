<?php
header('Content-Type: application/json');
session_start();
require 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $action = $_GET['action'] ?? '';
    $data = json_decode(file_get_contents('php://input'), true);

    if ($action === 'register') {
        $name = $data['name'] ?? '';
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        $role = $data['role'] ?? 'patient';

        if (!$name || !$email || !$password) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing fields']);
            exit;
        }

        // Note: For demo, passwords stored as plain text. Replace with password_hash in production.
        $stmt = $pdo->prepare('INSERT INTO users (name,email,password,role) VALUES (?,?,?,?)');
        try {
            $stmt->execute([$name, $email, $password, $role]);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            http_response_code(400);
            echo json_encode(['error' => 'Could not register: ' . $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'login') {
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        if (!$email || !$password) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing fields']);
            exit;
        }
        $stmt = $pdo->prepare('SELECT id,name,email,role,password FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && $password === $user['password']) {
            // store minimal info in session
            $_SESSION['user'] = ['id'=>$user['id'],'name'=>$user['name'],'email'=>$user['email'],'role'=>$user['role']];
            echo json_encode(['success'=>true,'user'=>$_SESSION['user']]);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials']);
        }
        exit;
    }
}

// GET for logout / session
if ($method === 'GET') {
    $action = $_GET['action'] ?? '';
    if ($action === 'logout') {
        session_destroy();
        echo json_encode(['success'=>true]);
        exit;
    }
    if ($action === 'me') {
        echo json_encode(['user'=>$_SESSION['user'] ?? null]);
        exit;
    }
}

http_response_code(405);
echo json_encode(['error'=>'Method not allowed']);
?>