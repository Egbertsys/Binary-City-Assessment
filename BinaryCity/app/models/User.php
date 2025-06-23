<?php
require_once __DIR__ . '/../../core/Database.php';

class User {
    public static function create($name, $surname, $email) {
    $prefix = strtoupper(substr($name, 0, 3));

    $pdo = Database::connect();

    // Get the current max numeric suffix for this prefix
    $stmt = $pdo->prepare("SELECT MAX(code) FROM users WHERE code LIKE :prefix");
    $stmt->execute(['prefix' => $prefix . '%']);

    $latest = $stmt->fetchColumn();

    // Extract number from previous code, default to 0
    $lastNumber = 0;
    if ($latest) {
        $numberPart = substr($latest, strlen($prefix));
        $lastNumber = (int)$numberPart;
    }

    $newNumber = $lastNumber + 1;
    $code = $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

    $stmt = $pdo->prepare("INSERT INTO users (name, surname, email, code) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $surname, $email, $code]);

    return $pdo->lastInsertId();
}

    public static function getAll() {
        $pdo = Database::connect();
        $stmt = $pdo->query("SELECT * FROM users ORDER BY surname ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function countChildren($userId) {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_links WHERE parent_id = ?");
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public static function link($parent, $child) {
        if ($parent == $child) return false;
        $pdo = Database::connect();
        $stmt = $pdo->prepare("INSERT IGNORE INTO user_links (parent_id, child_id) VALUES (?, ?)");
        return $stmt->execute([$parent, $child]);
        
    }
    

    public static function unlink($parent, $child) {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("DELETE FROM user_links WHERE parent_id = ? AND child_id = ?");
        return $stmt->execute([$parent, $child]);
    }

    public static function getChildren($parent) {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id IN (SELECT child_id FROM user_links WHERE parent_id = ?)");
        $stmt->execute([$parent]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}
