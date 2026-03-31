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
        $this->ensureStudentColumnsExist();
    }

    public function addStudent($name, $email, $phone, $avatar = null, $course = null, $class_name = null, $major = null)
    {
        $stmt = $this->conn->prepare(
            'INSERT INTO students (name, email, phone, avatar, course, class_name, major)
             VALUES (:name, :email, :phone, :avatar, :course, :class_name, :major)'
        );

        $name = $this->sanitizeText($name);
        $email = $this->sanitizeText($email);
        $phone = $this->sanitizeText($phone);
        $course = $this->sanitizeText($course);
        $class_name = $this->sanitizeText($class_name);
        $major = $this->sanitizeText($major);

        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':phone', $phone);
        $stmt->bindValue(':avatar', $avatar);
        $stmt->bindValue(':course', $course);
        $stmt->bindValue(':class_name', $class_name);
        $stmt->bindValue(':major', $major);

        return $stmt->execute();
    }

    public function getAllStudents($keyword = null)
    {
        $sql = 'SELECT * FROM students';

        if ($keyword) {
            $sql .= ' WHERE name LIKE :keyword';
        }

        $sql .= ' ORDER BY id DESC';
        $stmt = $this->conn->prepare($sql);

        if ($keyword) {
            $searchKeyword = "%{$keyword}%";
            $stmt->bindParam(':keyword', $searchKeyword);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStudentById($id)
    {
        $stmt = $this->conn->prepare('SELECT * FROM students WHERE id = :id');
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStudent($id, $name, $email, $phone, $avatar = null, $course = null, $class_name = null, $major = null)
    {
        $stmt = $this->conn->prepare(
            'UPDATE students
             SET name = :name,
                 email = :email,
                 phone = :phone,
                 avatar = :avatar,
                 course = :course,
                 class_name = :class_name,
                 major = :major
             WHERE id = :id'
        );

        $name = $this->sanitizeText($name);
        $email = $this->sanitizeText($email);
        $phone = $this->sanitizeText($phone);
        $course = $this->sanitizeText($course);
        $class_name = $this->sanitizeText($class_name);
        $major = $this->sanitizeText($major);

        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':phone', $phone);
        $stmt->bindValue(':avatar', $avatar);
        $stmt->bindValue(':course', $course);
        $stmt->bindValue(':class_name', $class_name);
        $stmt->bindValue(':major', $major);

        return $stmt->execute();
    }

    public function deleteStudent($id)
    {
        $stmt = $this->conn->prepare('DELETE FROM students WHERE id = :id');
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function getStatistics()
    {
        $sql = "SELECT
                    COUNT(*) AS total_students,
                    SUM(CASE WHEN email LIKE '%@tdu.edu.vn' THEN 1 ELSE 0 END) AS edu_emails,
                    SUM(CASE WHEN phone LIKE '09%' THEN 1 ELSE 0 END) AS sdt_09
                FROM students";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function ensureStudentColumnsExist()
    {
        $this->ensureColumnExists(
            'avatar',
            'ALTER TABLE students ADD COLUMN avatar VARCHAR(255) DEFAULT NULL AFTER phone'
        );
        $this->ensureColumnExists(
            'course',
            'ALTER TABLE students ADD COLUMN course VARCHAR(50) DEFAULT NULL AFTER avatar'
        );
        $this->ensureColumnExists(
            'class_name',
            'ALTER TABLE students ADD COLUMN class_name VARCHAR(100) DEFAULT NULL AFTER course'
        );
        $this->ensureColumnExists(
            'major',
            'ALTER TABLE students ADD COLUMN major VARCHAR(100) DEFAULT NULL AFTER class_name'
        );
    }

    private function ensureColumnExists($columnName, $alterSql)
    {
        $quotedColumnName = $this->conn->quote($columnName);
        $stmt = $this->conn->query("SHOW COLUMNS FROM students LIKE {$quotedColumnName}");

        if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->conn->exec($alterSql);
        }
    }

    private function sanitizeText($value)
    {
        return htmlspecialchars(strip_tags((string) $value));
    }
}
