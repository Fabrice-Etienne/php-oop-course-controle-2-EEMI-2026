<?php

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $host = 'php-oop-exercice-db';
        $db = 'blog';
        $user = 'root';
        $password = 'password';
        $dsn = "mysql:host=$host;dbname=$db;charset=UTF8";

        try {
            $this->pdo = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->pdo;
    }
}