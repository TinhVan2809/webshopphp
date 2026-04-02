<?php

namespace Tinhl\Bai01QuanlySv\Models;

use PDO;
use Tinhl\Bai01QuanlySv\Database;

class UserModel
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
        $this->ensureUserColumnsExist();
    }

    public function findUserByUsername(string $username)
    {
        $stmt = $this->conn->prepare('SELECT * FROM users WHERE username = :username LIMIT 1');
        $stmt->bindValue(':username', trim($username));
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findUserByEmail(string $email)
    {
        $stmt = $this->conn->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->bindValue(':email', trim($email));
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createUser(string $name, string $username, string $password, string $email): bool
    {
        if ($this->findUserByUsername($username) || $this->findUserByEmail($email)) {
            return false;
        }

        $stmt = $this->conn->prepare(
            'INSERT INTO users (name, username, password, email)
             VALUES (:name, :username, :password, :email)'
        );

        $stmt->bindValue(':name', $this->sanitizeText($name));
        $stmt->bindValue(':username', $this->sanitizeText($username));
        $stmt->bindValue(':password', password_hash($password, PASSWORD_DEFAULT));
        $stmt->bindValue(':email', trim($email));

        return $stmt->execute();
    }

    private function ensureUserColumnsExist(): void
    {
        $this->ensureColumnExists(
            'email',
            'ALTER TABLE users ADD COLUMN email VARCHAR(100) DEFAULT NULL AFTER username'
        );
    }

    private function ensureColumnExists(string $columnName, string $alterSql): void
    {
        $quotedColumnName = $this->conn->quote($columnName);
        $stmt = $this->conn->query("SHOW COLUMNS FROM users LIKE {$quotedColumnName}");

        if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->conn->exec($alterSql);
        }
    }

    private function sanitizeText(string $value): string
    {
        return trim(strip_tags($value));
    }
}
