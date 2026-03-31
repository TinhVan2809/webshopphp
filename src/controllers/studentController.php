<?php

namespace Tinhl\Bai01QuanlySv\Controllers;

use Tinhl\Bai01QuanlySv\Core\FlashMessage;
use Tinhl\Bai01QuanlySv\models\StudentModel;

class StudentController
{
    private const STUDENTS_PER_PAGE = 5;

    private $studentModel;

    public function __construct()
    {
        $this->studentModel = new StudentModel();
    }

    public function index()
    {
        $keyword = $this->getKeywordFromQuery();
        $requestedPage = $this->getPageFromQuery();
        $sortby = $this->sanitizeSortColumn($_GET['sortby'] ?? 'id');
        $order = $this->sanitizeSortOrder($_GET['order'] ?? 'desc');
        $nextOrder = $order === 'asc' ? 'desc' : 'asc';
        $listData = $this->getStudentListData($keyword, $requestedPage, $sortby, $order);

        $students = $listData['students'];
        $currentPage = $listData['currentPage'];
        $perPage = $listData['perPage'];
        $totalStudents = $listData['totalStudents'];
        $totalPages = $listData['totalPages'];
        $listStart = $listData['listStart'];
        $listEnd = $listData['listEnd'];
        $editingStudent = null;

        require_once __DIR__ . '/../../views/studentList.php';
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $course = $_POST['course'] ?? '';
            $class_name = $_POST['class_name'] ?? '';
            $major = $_POST['major'] ?? '';

            if (!empty($name) && !empty($email) && !empty($phone)) {
                $uploadResult = $this->handleAvatarUpload($_FILES['avatar'] ?? null);

                if (!$uploadResult['success']) {
                    FlashMessage::set('student_action', $uploadResult['message'], 'error');
                    header('Location: index.php');
                    exit();
                }

                $isAdded = $this->studentModel->addStudent(
                    $name,
                    $email,
                    $phone,
                    $uploadResult['filename'],
                    $course,
                    $class_name,
                    $major,
                );

                if ($isAdded) {
                    FlashMessage::set('student_action', 'Them sinh vien thanh cong!', 'success');
                } else {
                    $this->deleteAvatarFile($uploadResult['filename']);
                    FlashMessage::set('student_action', 'Them sinh vien that bai!', 'error');
                }
            } else {
                FlashMessage::set('student_action', 'Vui long nhap day du thong tin sinh vien.', 'error');
            }
        }

        header('Location: index.php');
        exit();
    }

    public function edit()
    {
        $studentId = (int) ($_GET['id'] ?? 0);
        $keyword = $this->getKeywordFromQuery();
        $requestedPage = $this->getPageFromQuery();
        $sortby = $this->sanitizeSortColumn($_GET['sortby'] ?? 'id');
        $order = $this->sanitizeSortOrder($_GET['order'] ?? 'desc');
        $nextOrder = $order === 'asc' ? 'desc' : 'asc';
        $redirectUrl = $this->buildListUrl($requestedPage, $keyword, $sortby, $order);

        if ($studentId <= 0) {
            FlashMessage::set('student_action', 'Khong tim thay sinh vien can sua.', 'error');
            header('Location: ' . $redirectUrl);
            exit();
        }

        $editingStudent = $this->studentModel->getStudentById($studentId);

        if (!$editingStudent) {
            FlashMessage::set('student_action', 'Sinh vien khong ton tai.', 'error');
            header('Location: ' . $redirectUrl);
            exit();
        }

        $listData = $this->getStudentListData($keyword, $requestedPage, $sortby, $order);
        $students = $listData['students'];
        $currentPage = $listData['currentPage'];
        $perPage = $listData['perPage'];
        $totalStudents = $listData['totalStudents'];
        $totalPages = $listData['totalPages'];
        $listStart = $listData['listStart'];
        $listEnd = $listData['listEnd'];

        require_once __DIR__ . '/../../views/studentList.php';
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php');
            exit();
        }

        $studentId = (int) ($_POST['id'] ?? 0);
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $course = $_POST['course'] ?? '';
        $class_name = $_POST['class_name'] ?? '';
        $major = $_POST['major'] ?? '';
        $keyword = trim((string) ($_POST['keyword'] ?? ''));
        $currentPage = $this->getPositivePage($_POST['page'] ?? 1);
        $sortby = $this->sanitizeSortColumn($_POST['sortby'] ?? 'id');
        $order = $this->sanitizeSortOrder($_POST['order'] ?? 'desc');

        if ($studentId <= 0) {
            FlashMessage::set('student_action', 'Khong tim thay sinh vien can cap nhat.', 'error');
            header('Location: ' . $this->buildListUrl($currentPage, $keyword, $sortby, $order));
            exit();
        }

        $currentStudent = $this->studentModel->getStudentById($studentId);

        if (!$currentStudent) {
            FlashMessage::set('student_action', 'Sinh vien khong ton tai.', 'error');
            header('Location: ' . $this->buildListUrl($currentPage, $keyword, $sortby, $order));
            exit();
        }

        if (empty($name) || empty($email) || empty($phone)) {
            FlashMessage::set('student_action', 'Vui long nhap day du thong tin sinh vien.', 'error');
            header('Location: ' . $this->buildEditUrl($studentId, $currentPage, $keyword, $sortby, $order));
            exit();
        }

        $uploadResult = $this->handleAvatarUpload($_FILES['avatar'] ?? null);

        if (!$uploadResult['success']) {
            FlashMessage::set('student_action', $uploadResult['message'], 'error');
            header('Location: ' . $this->buildEditUrl($studentId, $currentPage, $keyword, $sortby, $order));
            exit();
        }

        $avatarFile = $currentStudent['avatar'] ?? null;
        $oldAvatarFile = $avatarFile;

        if (!empty($uploadResult['filename'])) {
            $avatarFile = $uploadResult['filename'];
        }

        $isUpdated = $this->studentModel->updateStudent(
            $studentId,
            $name,
            $email,
            $phone,
            $avatarFile,
            $course,
            $class_name,
            $major,
        );

        if ($isUpdated) {
            if (!empty($uploadResult['filename']) && !empty($oldAvatarFile) && $oldAvatarFile !== $avatarFile) {
                $this->deleteAvatarFile($oldAvatarFile);
            }

            FlashMessage::set('student_action', 'Cap nhat sinh vien thanh cong!', 'success');
            header('Location: ' . $this->buildListUrl($currentPage, $keyword, $sortby, $order));
            exit();
        }

        if (!empty($uploadResult['filename'])) {
            $this->deleteAvatarFile($uploadResult['filename']);
        }

        FlashMessage::set('student_action', 'Cap nhat sinh vien that bai!', 'error');
        header('Location: ' . $this->buildEditUrl($studentId, $currentPage, $keyword, $sortby, $order));
        exit();
    }

    public function delete()
    {
        $studentId = (int) ($_GET['id'] ?? 0);
        $keyword = $this->getKeywordFromQuery();
        $currentPage = $this->getPageFromQuery();
        $sortby = $this->sanitizeSortColumn($_GET['sortby'] ?? 'id');
        $order = $this->sanitizeSortOrder($_GET['order'] ?? 'desc');
        $redirectUrl = $this->buildListUrl($currentPage, $keyword, $sortby, $order);

        if ($studentId <= 0) {
            FlashMessage::set('student_action', 'Khong tim thay sinh vien can xoa.', 'error');
            header('Location: ' . $redirectUrl);
            exit();
        }

        $student = $this->studentModel->getStudentById($studentId);

        if (!$student) {
            FlashMessage::set('student_action', 'Sinh vien khong ton tai.', 'error');
            header('Location: ' . $redirectUrl);
            exit();
        }

        if ($this->studentModel->deleteStudent($studentId)) {
            $this->deleteAvatarFile($student['avatar'] ?? null);
            FlashMessage::set('student_action', 'Xoa sinh vien thanh cong!', 'success');
        } else {
            FlashMessage::set('student_action', 'Xoa sinh vien that bai!', 'error');
        }

        header('Location: ' . $redirectUrl);
        exit();
    }

    public function dashboard()
    {
        $stats = $this->studentModel->getStatistics();

        require_once __DIR__ . '/../../views/dashboard.php';
    }

    public function detail()
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            FlashMessage::set('student_action', 'ID sinh vien khong hop le.', 'error');
            header('Location: index.php');
            exit();
        }

        $student = $this->studentModel->getStudentById($id);

        if (!$student) {
            FlashMessage::set('student_action', 'Khong tim thay sinh vien.', 'error');
            header('Location: index.php');
            exit();
        }

        require_once PROJECT_ROOT . '/views/detail.php';
    }

    private function getStudentListData(string $keyword, int $requestedPage, string $sortby, string $order): array
    {
        $perPage = self::STUDENTS_PER_PAGE;
        $totalStudents = $this->studentModel->countStudents($keyword);
        $totalPages = max(1, (int) ceil($totalStudents / $perPage));
        $currentPage = min(max(1, $requestedPage), $totalPages);
        $offset = ($currentPage - 1) * $perPage;
        $students = $this->studentModel->getAllStudents($keyword, $perPage, $offset, $sortby, $order);
        $listStart = $totalStudents === 0 ? 0 : $offset + 1;
        $listEnd = $totalStudents === 0 ? 0 : $offset + count($students);

        return [
            'students' => $students,
            'currentPage' => $currentPage,
            'perPage' => $perPage,
            'totalStudents' => $totalStudents,
            'totalPages' => $totalPages,
            'listStart' => $listStart,
            'listEnd' => $listEnd,
        ];
    }

    private function getKeywordFromQuery(): string
    {
        return trim((string) ($_GET['keyword'] ?? ''));
    }

    private function getPageFromQuery(): int
    {
        return $this->getPositivePage($_GET['page'] ?? 1);
    }

    private function getPositivePage($value): int
    {
        $page = (int) $value;

        return $page > 0 ? $page : 1;
    }

    private function sanitizeSortColumn($value): string
    {
        $allowedSortCols = ['id', 'name', 'email', 'phone'];
        $sortby = (string) $value;

        return in_array($sortby, $allowedSortCols, true) ? $sortby : 'id';
    }

    private function sanitizeSortOrder($value): string
    {
        return strtolower((string) $value) === 'asc' ? 'asc' : 'desc';
    }

    private function buildListUrl(int $page = 1, string $keyword = '', string $sortby = 'id', string $order = 'desc'): string
    {
        $params = [];

        if ($page > 1) {
            $params['page'] = $page;
        }

        if ($keyword !== '') {
            $params['keyword'] = $keyword;
        }

        if ($sortby !== 'id' || $order !== 'desc') {
            $params['sortby'] = $sortby;
            $params['order'] = $order;
        }

        return 'index.php' . (!empty($params) ? '?' . http_build_query($params) : '');
    }

    private function buildEditUrl(int $studentId, int $page = 1, string $keyword = '', string $sortby = 'id', string $order = 'desc'): string
    {
        $params = [
            'action' => 'edit',
            'id' => $studentId,
        ];

        if ($page > 1) {
            $params['page'] = $page;
        }

        if ($keyword !== '') {
            $params['keyword'] = $keyword;
        }

        if ($sortby !== 'id' || $order !== 'desc') {
            $params['sortby'] = $sortby;
            $params['order'] = $order;
        }

        return 'index.php?' . http_build_query($params);
    }

    private function handleAvatarUpload($file)
    {
        if (!$file || !isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return ['success' => true, 'filename' => null, 'message' => ''];
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'filename' => null, 'message' => 'Tai anh dai dien that bai.'];
        }

        if (($file['size'] ?? 0) > 2 * 1024 * 1024) {
            return ['success' => false, 'filename' => null, 'message' => 'Anh dai dien khong duoc vuot qua 2MB.'];
        }

        $tmpName = $file['tmp_name'] ?? '';
        $imageInfo = @getimagesize($tmpName);
        $allowedTypes = [
            IMAGETYPE_JPEG => 'jpg',
            IMAGETYPE_PNG => 'png',
            IMAGETYPE_GIF => 'gif',
            IMAGETYPE_WEBP => 'webp',
        ];

        if ($imageInfo === false || !isset($allowedTypes[$imageInfo[2]])) {
            return ['success' => false, 'filename' => null, 'message' => 'Anh dai dien phai la file JPG, PNG, GIF hoac WEBP.'];
        }

        $uploadDir = $this->getAvatarUploadDirectory();

        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
            return ['success' => false, 'filename' => null, 'message' => 'Khong the tao thu muc luu anh dai dien.'];
        }

        $fileName = uniqid('avatar_', true) . '.' . $allowedTypes[$imageInfo[2]];
        $destination = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

        if (!move_uploaded_file($tmpName, $destination)) {
            return ['success' => false, 'filename' => null, 'message' => 'Khong the luu file anh dai dien.'];
        }

        return ['success' => true, 'filename' => $fileName, 'message' => ''];
    }

    private function deleteAvatarFile($fileName)
    {
        if (empty($fileName) || $fileName === 'default-avatar.png') {
            return;
        }

        $avatarPath = $this->getAvatarUploadDirectory() . DIRECTORY_SEPARATOR . $fileName;

        if (is_file($avatarPath)) {
            unlink($avatarPath);
        }
    }

    private function getAvatarUploadDirectory()
    {
        if (defined('PROJECT_ROOT')) {
            return PROJECT_ROOT . '/public/uploads/avatars';
        }

        return dirname(__DIR__, 2) . '/public/uploads/avatars';
    }
}
