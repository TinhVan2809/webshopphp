<?php

session_start();

define('PROJECT_ROOT', dirname(__DIR__));

require_once PROJECT_ROOT . '/vendor/autoload.php';

use Tinhl\Bai01QuanlySv\Controllers\PageController;
use Tinhl\Bai01QuanlySv\Controllers\StudentController;
use Tinhl\Bai01QuanlySv\Controllers\UserController;

$action = $_GET['action'] ?? 'index';

$public_actions = [
    'login',
    'register',
    'do_login',
    'do_register',
    'contact',
    'submit_contact',
];

$protected_actions = [
    'index',
    'edit',
    'update',
    'delete',
    'add',
    'dashboard',
    'detail',
];

if (in_array($action, $protected_actions, true) && !isset($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit();
}

if (!in_array($action, $public_actions, true) && !isset($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit();
}

if (in_array($action, ['login', 'register', 'do_login', 'do_register', 'logout'], true)) {
    $controller = new UserController();
} elseif (in_array($action, ['contact', 'submit_contact'], true)) {
    $controller = new PageController();
} else {
    $controller = new StudentController();
}

switch ($action) {
    case 'index':
        $controller->index();
        break;
    case 'edit':
        $controller->edit();
        break;
    case 'update':
        $controller->update();
        break;
    case 'delete':
        $controller->delete();
        break;
    case 'dashboard':
        $controller->dashboard();
        break;
    case 'add':
        $controller->add();
        break;
    case 'login':
        $controller->showLoginForm();
        break;
    case 'do_login':
        $controller->login();
        break;
    case 'register':
        $controller->showRegisterForm();
        break;
    case 'do_register':
        $controller->register();
        break;
    case 'logout':
        $controller->logout();
        break;
    case 'contact':
        $controller->showContactForm();
        break;
    case 'submit_contact':
        $controller->submitContact();
        break;
    case 'detail':
        $controller->detail();
        break;
    default:
        $controller->index();
        break;
}
