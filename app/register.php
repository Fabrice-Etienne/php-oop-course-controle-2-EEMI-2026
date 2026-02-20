<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? null;
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;

    if ($username && $email && $password) {
        $user = User::register($username, $email, $password);
        if ($user) {
            header('Location: /login.php');
            exit;
        } else {
            $error = 'User already exists';
        }
    } else {
        $error = 'Please fill all fields';
    }
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
                    <?php if (User::isLoggedIn()): ?>
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
                    <?php if (isset($error)): ?>
                        <p class="text-red-500 font-semibold"><?= htmlspecialchars($error) ?></p>
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
