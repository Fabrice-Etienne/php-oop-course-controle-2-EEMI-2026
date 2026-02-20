<?php
session_start();

require_once __DIR__ . '/vendor/autoload.php';

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}
function getDbConnexion(): PDO {
    $host = 'php-oop-exercice-db';
    $db = 'blog';
    $user = 'root';
    $password = 'password';

    $dsn = "mysql:host=$host;dbname=$db;charset=UTF8";

    return new PDO($dsn, $user, $password);
}

function register(string $username, string $email, string $password) {
    $sql = "SELECT * FROM users WHERE email = :email OR name = :username";
    $stmt = getDbConnexion()->prepare($sql);
    $stmt->execute(['email' => $email, 'username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!empty($user)) {
        return false;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password);";
    $stmt = getDbConnexion()->prepare($sql);
    $stmt->execute(['name' => $username, 'email' => $email, 'password' => $hashedPassword]);
    
    header('Location: /login.php');
    exit;
}

$success = null;

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    $success = register($username, $email, $password);
}

?>

<!doctype html>
<html class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body  class="min-h-full">
    <main class="min-h-full">
        <div class="flex flex-col min-h-full w-full items-center justify-start">
            <div class="flex flex-row w-full h-24 bg-gray-900 items-center justify-center">
                <div class="w-11/12 flex flex-row items-center justify-end space-x-4">
                    <a href="/" class="text-white">Homepage</a>
                    <?php if (isLoggedIn()): ?>
                        <a href="/blogs/new.php" class="text-white">Create post</a>
                        <a href="/profile.php" class="text-white">Profile</a>
                        <a href="/logout.php" class="text-white">Logout</a>
                    <?php else: ?>
                        <a href="/login.php"  class="text-white">Login</a>
                        <a href="/register.php"  class="text-white">Register</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="flex flex-col w-11/12 items-center justify-start">
                <h1 class="text-4xl">Wonderful blog</h1>
                <form action="/register.php" method="post" class="flex flex-col w-1/2 space-y-4">
                    <?php if ($success === false): ?>
                        <p class="text-red-500">Invalid credentials</p>
                    <?php endif; ?>

                    <input type="text" name="username" placeholder="Username" class="p-2 border border-gray-300 rounded">
                    <input type="email" name="email" placeholder="Email" class="p-2 border border-gray-300 rounded">
                    <input type="password" name="password" placeholder="Password" class="p-2 border border-gray-300 rounded">
                    <button type="submit" class="p-2 bg-blue-500 text-white rounded">Register</button>
                </form>
            </div>
        </div>        
    </main>
</body>
</html>
