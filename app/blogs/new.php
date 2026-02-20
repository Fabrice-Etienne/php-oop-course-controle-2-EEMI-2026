<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

if (!User::isLoggedIn()) {
    header('Location: /login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = $_POST['title'] ?? null;
    $content = $_POST['content'] ?? null;

    if ($title && $content) {
        $post = Post::create(
            $title,
            $content,
            $_SESSION['user_id']
        );

        header('Location: /blogs/index.php?id=' . $post->getId());
        exit;
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
                <h1 class="text-4xl">New post</h1>
                <form action="/blogs/new.php" method="post" class="flex flex-col w-1/2 space-y-4">
                    <input type="text" name="title" placeholder="Title" class="p-2 border
                    border-gray-300 rounded">
                    <textarea name="content" placeholder="Content" class="p-2 border border-gray-300 rounded"></textarea>
                    <button type="submit" class="p-2 bg-blue-500 text-white rounded">Create</button>
                </form>
            </div>
        </div>        
    </main>
</body>
</html>