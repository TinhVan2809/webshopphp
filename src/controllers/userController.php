<?php

namespace Tinhl\Bai01QuanlySv\Controllers;

use Tinhl\Bai01QuanlySv\Core\FlashMessage;
use Tinhl\Bai01QuanlySv\Core\Mailer;
use Tinhl\Bai01QuanlySv\Models\UserModel;

class UserController
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function showRegisterForm(): void
    {
        require_once PROJECT_ROOT . '/views/register.php';
    }

    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=register');
            exit();
        }

        $name = trim((string) ($_POST['name'] ?? ''));
        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $email = trim((string) ($_POST['email'] ?? ''));

        if ($name === '' || $username === '' || $password === '' || $email === '') {
            $error = 'Vui lòng điền đầy đủ thông tin.';
            require_once PROJECT_ROOT . '/views/register.php';
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Email không đúng định dạng.';
            require_once PROJECT_ROOT . '/views/register.php';
            return;
        }

        if ($this->userModel->findUserByUsername($username)) {
            $error = 'Tên đăng nhập đã tồn tại. Vui lòng chọn tên khác.';
            require_once PROJECT_ROOT . '/views/register.php';
            return;
        }

        if ($this->userModel->findUserByEmail($email)) {
            $error = 'Email đã được sử dụng. Vui lòng chọn email khác.';
            require_once PROJECT_ROOT . '/views/register.php';
            return;
        }

        $isRegistered = $this->userModel->createUser($name, $username, $password, $email);

        if (!$isRegistered) {
            $error = 'Không thể tạo tài khoản vào lúc này. Vui lòng thử lại.';
            require_once PROJECT_ROOT . '/views/register.php';
            return;
        }

        $subject = 'Chào mừng bạn đến với ứng dụng Quản lý Sinh viên';
        $body = $this->buildWelcomeEmailBody($name, $username);

        if (Mailer::send($email, $name, $subject, $body)) {
            FlashMessage::set(
                'login_form',
                'Đăng ký thành công! Email chào mừng đã được gửi đến hộp thư của bạn.',
                'success'
            );
        } else {
            FlashMessage::set(
                'login_form',
                'Đăng ký thành công, nhưng chưa thể gửi email chào mừng lúc này.',
                'error'
            );
        }

        header('Location: index.php?action=login');
        exit();
    }

    public function showLoginForm(): void
    {
        require_once PROJECT_ROOT . '/views/login.php';
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=login');
            exit();
        }

        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($username === '' || $password === '') {
            $error = 'Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu.';
            require_once PROJECT_ROOT . '/views/login.php';
            return;
        }

        $user = $this->userModel->findUserByUsername($username);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            header('Location: index.php');
            exit();
        }

        $error = 'Tên đăng nhập hoặc mật khẩu không chính xác.';
        require_once PROJECT_ROOT . '/views/login.php';
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        header('Location: index.php?action=login');
        exit();
    }

    private function buildWelcomeEmailBody(string $name, string $username): string
    {
        $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $safeUsername = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');

        return <<<HTML
<h2>Chào mừng, {$safeName}!</h2>
<p>Tài khoản của bạn đã được tạo thành công trên hệ thống Quản lý Sinh viên.</p>
<p>Tên đăng nhập của bạn là: <strong>{$safeUsername}</strong></p>
<p>Bạn có thể đăng nhập ngay để bắt đầu sử dụng hệ thống.</p>
<p>Trân trọng,<br>Ban quản trị</p>
HTML;
    }
}
