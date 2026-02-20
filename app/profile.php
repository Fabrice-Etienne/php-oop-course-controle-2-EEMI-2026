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

function getUser(): array {
    $sql = "SELECT * FROM users WHERE id = :id";
    $stmt = getDbConnexion()->prepare($sql);
    $stmt->execute(['id' => $_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    return $user;
}

function getPosts(): array {
    $sql = "SELECT posts.id, posts.title, posts.created_at
    FROM posts 
    INNER JOIN users ON posts.user_id = users.id
    WHERE posts.user_id = :id
    ORDER BY posts.created_at DESC;
    ";

    $stmt = getDbConnexion()->prepare($sql);
    $stmt->execute(['id' => $_SESSION['user_id']]);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $posts;
}

function updateProfile(string $name, string $email, string $password) {
    $userId = $_SESSION['user_id'];
    $pdo = getDbConnexion();

    $sql = "UPDATE users SET name = :name, email = :email";
    $params = ['name' => $name, 'email' => $email, 'id' => $userId];

    if($password) {
        $sql .= ", password = :password";
        $params['password'] = password_hash($password, PASSWORD_BCRYPT);
    }

    $sql .= " WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    header('Location: /profile.php');
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    return updateProfile($name, $email, $password);
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
                <h1 class="text-4xl">Hello <?= getUser()['name'] ?></h1>

                <h2 class="text-2xl">Update your profile</h2>

                <form action="/profile.php" method="post" class="flex flex-col w-1/2 space-y-4">

                
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" value="<?= getUser()['name'] ?>" class="p-2 border
                    border-gray-300 rounded">
                
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="<?= getUser()['email'] ?>" class="p-2 border
                    border-gray-300 rounded">
                
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="p-2 border border-gray-300 rounded">
                
                    <button type="submit" class="p-2 bg-blue-500 text-white rounded">Update</button>
                </form>

                <h2 class="text-2xl">Your posts</h2>

                <a href="/blogs/new.php" class="text-blue-500">Create a new post</a>

                <ul class="w-1/2">
                    <?php foreach (getPosts() as $post): ?>
                        <li class="border-b border-gray-300 p-2">
                            <a href="/blogs/show.php?id=<?= $post['id'] ?>" class="text-blue-500"><?= $post['title'] ?></a>
                            <p class="text-gray-500"><?= $post['created_at'] ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>

            </div>
        </div>        
    </main>
</body>
</html>