<?php
require_once '../core/Database.php';
require_once '../app/models/User.php';
require_once '../app/controllers/UserController.php';

$controller = new UserController();
$action = $_GET['action'] ?? 'index';

if (method_exists($controller, $action)) {
    $controller->$action();
} else {
    echo "Invalid action.";
}

?>