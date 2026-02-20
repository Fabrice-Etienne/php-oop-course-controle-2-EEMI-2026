<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';

$isApi = strpos($_SERVER['REQUEST_URI'], '/api/') === 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;

    $user = User::login($email, $password);

    if ($isApi) {
        header('Content-Type: application/json');

        if (!$user) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid credentials'
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'user' => [
                    'id' => $user->getId(),
                    'name' => $user->getName()
                ]
            ]);
        }
        exit;
    }

    if (!$user) {
        $error = true;
    } else {
        $_SESSION['user_id'] = $user->getId();
        header('Location: /profile.php');
        exit;
    }
}