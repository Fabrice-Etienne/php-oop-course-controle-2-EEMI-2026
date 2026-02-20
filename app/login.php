<?php
session_start();

// Importation de la classe Database
require_once __DIR__ . '/src/Database.php';

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

// Supprimé : getDbConnexion() n'est plus nécessaire ici car on utilise la classe Database

function login(string $email, string $password) {
    $sql = "SELECT * FROM users WHERE email = :email";
    
    // Utilisation de la nouvelle classe Database
    $stmt = Database::getInstance()->prepare($sql);
    
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(); // Le mode FETCH_ASSOC est déjà géré dans la classe Database

    if (!$user || !password_verify($password, $user['password'])) {
        return false;
    }
    $_SESSION['user_id'] = $user['id'];
    return $user;
}

function jsonResponse($data, int $status = 200) {
    header('Content-Type: application/json');
    http_response_code($status);
    echo json_encode($data);
    exit;
}

$isApi = str_starts_with($_SERVER['REQUEST_URI'], '/api/');
$success = null;

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
    $success = login($email, $password);

    if($isApi) {
        if($success === false) {
            jsonResponse(['success' => false, 'message' => 'Invalid credentials'], 401);
        } else {
            jsonResponse([
                'success' => true, 
                'user' => [
                    'id' => $success['id'], 
                    'name' => $success['name'], 
                    'email' => $success['email']
                ]
            ], 200);
        }
    } else {
        if($success === false) {
            $error = true;
        } else {
            header('Location: /profile.php');
            exit;
        }
    }
}
?>