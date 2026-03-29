<?php

namespace Tinhl\Bai01QuanlySv\Controllers;

use Tinhl\Bai01QuanlySv\models\StudentModel;
use Tinhl\Bai01QuanlySv\Core\FlashMessage;

class StudentController
{
    private $studentModel;
    public function __construct()
    {
        $this->studentModel = new StudentModel();
    }
    // Hiển thị danh sách sinh viên
    public function index()
    {
        $keyword = $_GET['keyword'] ?? null;
        $students = $this->studentModel->getAllStudents($keyword);
        require_once __DIR__ . '/../../views/studentList.php';
    }
    // Xử lý thêm sinh viên
    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $phone = $_POST['phone'] ?? '';
            if (
                !empty($name) && !empty($email) &&

                !empty($phone)
            ) {

                $isAdded = $this->studentModel->addStudent(
                    $name,
                    $email,
                    $phone
                );

                if ($isAdded) {
                    FlashMessage::set('student_action', 'Thêm sinh viên thành công!', 'success');
                } else {
                    FlashMessage::set('student_action', 'Thêm sinh viên thất bại!', 'error');
                }
            } else {
                FlashMessage::set('student_action', 'Thêm sinh viên thất bại!', 'error');
            }
        }
        // Sau khi thêm, chuyển hướng về trang danh sách
        header('Location: index.php');
        exit();
    }
}
