<?php

session_start();

define('PROJECT_ROOT', dirname(__DIR__));

require_once PROJECT_ROOT . '/vendor/autoload.php';

require_once PROJECT_ROOT . '/app/controller.php';

$action = $_GET['action'] ?? 'index';
$controller = new Controller();

switch ($action) {
    case 'index':
        $controller->index();
        break;
    case 'detail':
        $controller->detail();
        break;
    default:
        $controller->index();
        break;
}
