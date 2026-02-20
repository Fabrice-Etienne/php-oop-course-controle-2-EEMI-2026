<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';

function getDbConnexion(): PDO {
    $host = 'php-oop-exercice-db';
    $db = 'blog';
    $user = 'root';
    $password = 'password';

    $dsn = "mysql:host=$host;dbname=$db;charset=UTF8";

    return new PDO($dsn, $user, $password);
}

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function getPage(): int {
    return $_GET['page'] ?? 1;
}

function getLimit(): int {
    return $_GET['limit'] ?? 10;
}


function getPostsCount(): int {
    $sql = "SELECT COUNT(*) FROM posts";
    $stmt = getDbConnexion()->query($sql);
    $count = $stmt->fetchColumn();

    return $count;
}

function getPagination(): array {
    $postsCount = getPostsCount();
    $postsPerPage = getLimit();
    $pagesCount = ceil($postsCount / $postsPerPage);

    return [
        'pagesCount' => $pagesCount,
        'currentPage' => getPage(),
    ];
}

function getPosts(): array {

    $currentPage = getPage();
    $postsPerPage = getLimit();
    $offset = ($currentPage - 1) * $postsPerPage;

    $sql = "SELECT posts.id, posts.title, posts.created_at, users.name, users.id as user_id
    FROM posts 
    INNER JOIN users ON posts.user_id = users.id
    ORDER BY posts.created_at DESC
    LIMIT 10
    OFFSET $offset;
    ";
    $stmt = getDbConnexion()->query($sql);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $posts;
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

                <div class="flex flex-col w-full items-center justify-start space-y-4">
                    <?php foreach(getPosts() as $post): ?>
                        <div class="flex flex-col w-full items-center justify-start border border-gray-300 p-4">
                            <a href="/blogs/index.php?id=<?= $post['id'] ?>" class="text-2xl"><?= $post['title'] ?></a>
                            <a href="/users.php?id=<?= $post['user_id'] ?>" class="p">By <?= $post['name'] ?></a>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="flex flex-row w-full items-center justify-center space-x-4">
                    <?php for ($i = 1; $i <= getPagination()['pagesCount']; $i++): ?>
                        <?php if($i !== getPage()): ?>
                            <a href="/?page=<?= $i ?>" class="text-xl underline text-gray"><?= $i ?></a>
                        <?php else: ?>
                            <p class="text-2xl font-bold text-black"><?= $i ?></p>
                        <? endif; ?>
                    <?php endfor; ?>
                </div>
            </div>
        </div>        
    </main>
</body>
</html>