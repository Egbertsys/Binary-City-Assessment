<?php
require_once __DIR__ . '/../models/User.php';

class UserController {
    public function index() {
        $users = User::getAll();
        include __DIR__ . '/../views/users/index.php';
    }

    public function createAjax() {
        header('Content-Type: application/json');

        $name = trim($_POST['name'] ?? '');
        $surname = trim($_POST['surname'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if (!$name || !$surname || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(["status" => "error", "message" => "Invalid input"]);
            return;
        }

        try {
            $id = User::create($name, $surname, $email);
            $users = User::getAll();
            ob_start();
            include __DIR__ . '/../views/users/tabs.php';
            $html = ob_get_clean();
            echo json_encode(["status" => "success", "html" => $html]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => "Duplicate or server error"]);
        }
    }


    
    public function linkAjax() {
        header('Content-Type: application/json');
        $parent = $_POST['parent_id'] ?? null;
        $child = $_POST['child_id'] ?? null;
    
        if (!$parent || !$child || $parent == $child) {
            echo json_encode(["status" => "error", "message" => "Invalid link"]);
            return;
        }
    
        $success = User::link($parent, $child);
        echo json_encode(["status" => $success ? "success" : "error", "message" => $success ? "" : "DB error"]);
    }
    
    

    public function unlinkAjax() {
        header('Content-Type: application/json');
        $parent = $_POST['parent_id'] ?? null;
        $child = $_POST['child_id'] ?? null;
    
        if ($parent && $child) {
            User::unlink($parent, $child);
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Missing data"]);
        }
    }
    

    public function getTabsAjax() {
        header('Content-Type: application/json');
        $users = User::getAll();
        ob_start();
        include __DIR__ . '/../views/users/tabs.php';
        $html = ob_get_clean();
        echo json_encode([
            "status" => "success",
            "html" => $html
        ]);
    }
    

    
}




?>