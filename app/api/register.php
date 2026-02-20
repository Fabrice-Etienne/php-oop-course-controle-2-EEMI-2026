<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$username = $_POST['username'] ?? null;
$email = $_POST['email'] ?? null;
$password = $_POST['password'] ?? null;

if (!$username || !$email || !$password) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

$user = User::register($username, $email, $password);

// je n'ai pas pu terminer...