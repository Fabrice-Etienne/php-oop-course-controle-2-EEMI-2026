<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$email = $_POST['email'] ?? null;
$password = $_POST['password'] ?? null;

if (!$email || !$password) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email and password are required']);
    exit;
}

$user = User::login($email, $password);

if (!$user) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
    exit;
}

// Connexion réussie
$_SESSION['user_id'] = $user->getId();
echo json_encode([
    'success' => true,
    'user' => [
        'id' => $user->getId(),
        'name' => $user->getName(),
        'email' => $user->getEmail()
    ]
]);