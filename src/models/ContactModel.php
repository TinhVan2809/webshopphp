<?php
// src/Models/ContactModel.php
namespace Tinhl\Bai01QuanlySv\Models;

use Tinhl\Bai01QuanlySv\Database;
use PDO;

class ContactModel
{
    private $conn;
    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }
    /**
     * Lưu một liên hệ mới vào CSDL
     */
    public function saveContact($name, $email, $message)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO contacts (name, email, message) VALUES

(:name, :email, :message)"

        );
        $cleanName = htmlspecialchars(strip_tags($name));
        $cleanEmail = htmlspecialchars(strip_tags($email));
        $cleanMessage = htmlspecialchars(strip_tags($message));

        $stmt->bindParam(
            ':name',
            $cleanName
        );
        $stmt->bindParam(
            ':email',
            $cleanEmail
        );
        $stmt->bindParam(
            ':message',
            $cleanMessage
        );

        return $stmt->execute();
    }
}
