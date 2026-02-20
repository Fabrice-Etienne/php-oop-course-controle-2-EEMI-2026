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

        public static function create(int $postId, int $userId, string $content): self {
        $stmt = Database::getInstance()->prepare(
            "INSERT INTO comments (content, post_id, user_id) VALUES (:content, :post_id, :user_id)"
        );
        $stmt->execute([
            'content' => $content,
            'post_id' => $postId,
            'user_id' => $userId
        ]);
        $id = Database::getInstance()->lastInsertId();
        $stmt = Database::getInstance()->prepare("SELECT * FROM comments WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return new self($stmt->fetch());
    }


}