<?php

namespace Tinhl\Bai01QuanlySv\models;

use PDO;
use Tinhl\Bai01QuanlySv\Database;

class StudentModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
        $this->ensureAvatarColumnExists();
    }

    public function addStudent($name, $email, $phone, $avatar = null)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO students (name, email, phone, avatar) VALUES (:name, :email, :phone, :avatar)"
        );

        $name = htmlspecialchars(strip_tags($name));
        $email = htmlspecialchars(strip_tags($email));
        $phone = htmlspecialchars(strip_tags($phone));

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindValue(':avatar', $avatar);

        return $stmt->execute();
    }

    public function getAllStudents($keyword = null)
    {
        $sql = "SELECT * FROM students";

        if ($keyword) {
            $sql .= " WHERE name LIKE :keyword";
        }

        $sql .= " ORDER BY id DESC";
        $stmt = $this->conn->prepare($sql);

        if ($keyword) {
            $searchKeyword = "%{$keyword}%";
            $stmt->bindParam(':keyword', $searchKeyword);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function ensureAvatarColumnExists()
    {
        $stmt = $this->conn->query("SHOW COLUMNS FROM students LIKE 'avatar'");

        if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->conn->exec("ALTER TABLE students ADD COLUMN avatar VARCHAR(255) DEFAULT NULL AFTER phone");
        }
    }

    /**
     * Lấy các số liệu thống kê về sinh viên
     */
    public function getStatistics()
    {
        $sql = " SELECT COUNT(*) AS total_students, SUM(CASE WHEN email LIKE '%@tdu.edu.vn' THEN 1 ELSE 0 END) AS edu_emails, SUM(CASE WHEN phone LIKE '09%' THEN 1 ELSE 0 END) AS sdt_09
                FROM students";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
