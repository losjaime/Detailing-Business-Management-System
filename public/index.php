<?php
session_start();

require_once __DIR__ . '/../src/controllers/LoginController.php';
require_once __DIR__ . '/../src/controllers/Controller.php';

// Handle login/logout before anything else
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginController = new LoginController();

    if (isset($_POST['login'])) {
        $loginController->handleLoginRequest();
    } elseif (isset($_POST['logout'])) {
        $loginController->handleLogoutRequest();
    }
}

// Include header
include(__DIR__ . '/../src/partials/header.php');

// Continue with the rest of the MVC routing
$controller = new Controller();
$action = $_GET['action'] ?? 'index';
$params = array_merge($_GET, $_POST);
$result = $controller->handleRequest($action, $params);

$view = $result['view'] ?? 'index';
$data = $result['data'] ?? [];

$view_file = __DIR__ . '/../public/assets/pages/' . $view . '.php';

if (file_exists($view_file)) {
    include $view_file;
} else {
    include __DIR__ . '/assets/pages/index.php';
}

// Include footer
include(__DIR__ . '/../src/partials/footer.php');
