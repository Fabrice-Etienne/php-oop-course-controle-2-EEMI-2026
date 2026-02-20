<?php
// app/src/Comment.php
require_once 'Database.php';
require_once 'User.php';

class Comment {
    private $id;
    private $content;
    private $user;
    private $created_at;

    public function __construct(array $data) {
        $this->id = $data['id'];
        $this->content = $data['content'];
        $this->user = new User($data['user_id']);
        $this->created_at = $data['created_at'];
    }

    public function getContent(): string { return $this->content; }
    public function getUser(): User { return $this->user; }
    public function getCreatedAt(): string { return $this->created_at; }

    public static function getByPostId(int $postId): array {
        $stmt = Database::getInstance()->prepare(
            "SELECT * FROM comments WHERE post_id = :post_id ORDER BY created_at ASC"
        );
        $stmt->execute(['post_id' => $postId]);
        $rows = $stmt->fetchAll();
        $comments = [];
        foreach ($rows as $row) {
            $comments[] = new self($row);
        }
        return $comments;
    }

}