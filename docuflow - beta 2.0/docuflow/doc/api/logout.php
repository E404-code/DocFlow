<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/doc/config/database.php';
session_start();

if (isset($_SESSION['uid']) && !empty($_SESSION['uid'])) {
    try {
        $stmt = $pdo->prepare('UPDATE users SET active = ? WHERE id = ?');
        $stmt->execute(['0', $_SESSION['uid']]);
        $logStmt = $pdo->prepare('INSERT INTO activity_log (user_id, action, description) VALUES (?, ?, ?)');
        $logStmt->execute([$_SESSION['uid'], 'logout', 'User logged out']);
    } catch (Exception $e) {
        error_log("Failed to update active status on logout: " . $e->getMessage());
    }
}

session_unset();
session_destroy();
exit();
