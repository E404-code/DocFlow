<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/doc/config/database.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/doc/api/api_translations.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/doc/app/authentication.php';

response_ccode('POST', 404, 'Location: /doc/public/error/pages/404.html');

function getGroupColumn($pdo) {
    $stmt = $pdo->prepare("
        SELECT COLUMN_NAME
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'users'
          AND COLUMN_NAME IN ('group_name', 'group_id')
        ORDER BY FIELD(COLUMN_NAME, 'group_name', 'group_id')
        LIMIT 1
    ");
    $stmt->execute();
    $row = $stmt->fetch();
    return $row['COLUMN_NAME'] ?? 'group_name';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $uid = $_SESSION['uid'] ?? '';
    $role = $_SESSION['role'] ?? '';

    if (!in_array($role, ['admin', 'group'], true)) {
        echo json_encode(['status' => 'error', 'message' => getApiMessage('forbidden')]);
        exit();
    }

    try {
        $groupCol = getGroupColumn($pdo);
        $sql = "
            WITH RECURSIVE users_tree AS(
                SELECT id, name, {$groupCol} FROM users WHERE id = ?
                UNION ALL
                SELECT u.id, u.name, u.{$groupCol} FROM users u
                INNER JOIN users_tree t ON u.{$groupCol} = t.id
            )
            SELECT u.id, u.name, u.email, u.role, u.active, u.created_at
            FROM users u
            INNER JOIN users_tree t ON u.id = t.id
            WHERE u.id <> ?
              AND (u.is_delete IS NULL OR u.is_delete = 'false')
            ORDER BY u.created_at DESC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$uid, $uid]);
        $users = $stmt->fetchAll();

        echo json_encode(['status' => 'success', 'users' => $users ?: []]);
        exit();
    } catch (Exception $e) {
        error_log("Failed to load managed users: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => getApiMessage('server_error')]);
        exit();
    }
}
?>
