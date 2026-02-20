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

}