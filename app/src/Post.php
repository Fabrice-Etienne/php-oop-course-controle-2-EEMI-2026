<?php
// app/src/Post.php
require_once 'Database.php';
require_once 'User.php';
require_once 'Comment.php';

class Post {
    private $id;
    private $title;
    private $content;
    private $user; // instance de User
    private $created_at;

    public function __construct(int $id) {
        $stmt = Database::getInstance()->prepare(
            "SELECT * FROM posts WHERE id = :id"
        );
        $stmt->execute(['id' => $id]);
        $post = $stmt->fetch();
        if ($post) {
            $this->id = $post['id'];
            $this->title = $post['title'];
            $this->content = $post['content'];
            $this->user = new User($post['user_id']);
            $this->created_at = $post['created_at'];
        }
    }

    public function getId(): int { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getContent(): string { return $this->content; }
    public function getAuthor(): User { return $this->user; }
    public function getCreatedAt(): string { return $this->created_at; }

    public function getComments(): array {
        return Comment::getByPostId($this->id);
    }

    public static function create(string $title, string $content, int $userId): self {
        $stmt = Database::getInstance()->prepare(
            "INSERT INTO posts (title, content, user_id) VALUES (:title, :content, :user_id)"
        );
        $stmt->execute([
            'title' => $title,
            'content' => $content,
            'user_id' => $userId
        ]);
        $id = Database::getInstance()->lastInsertId();
        return new self($id);
    }
}