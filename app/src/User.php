<?php
// app/src/User.php
require_once 'Database.php';

class User {
    private $id;
    private $name;
    private $email;

    public function __construct(int $id = null) {
        if ($id) {
            $this->loadById($id);
        }
    }

    private function loadById(int $id) {
        $stmt = Database::getInstance()->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        if ($user) {
            $this->id = $user['id'];
            $this->name = $user['name'];
            $this->email = $user['email'];
        }
    }

    public static function isLoggedIn(): bool {
        return isset($_SESSION['user_id']);
    }

    public static function getCurrentUser(): ?self {
        if (!self::isLoggedIn()) return null;
        return new self($_SESSION['user_id']);
    }

    // Nouvelle méthode statique pour gérer la connexion proprement
    public static function login(string $email, string $password) {
        $stmt = Database::getInstance()->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $data = $stmt->fetch();

        if ($data && password_verify($password, $data['password'])) {
            $_SESSION['user_id'] = $data['id'];
            return $data; // Retourne les infos pour l'API
        }
        return false;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function updateProfile(string $name, string $email, ?string $password = null) {
        $sql = "UPDATE users SET name = :name, email = :email";
        $params = ['name' => $name, 'email' => $email, 'id' => $this->id];

        if ($password) {
            $sql .= ", password = :password";
            $params['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $sql .= " WHERE id = :id";
        $stmt = Database::getInstance()->prepare($sql);
        $stmt->execute($params);

        $this->name = $name;
        $this->email = $email;
    }
}