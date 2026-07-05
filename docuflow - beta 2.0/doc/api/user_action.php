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

    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || empty($data['id']) || empty($data['action'])) {
        echo json_encode(['status' => 'error', 'message' => getApiMessage('invalid_request')]);
        exit();
    }

    $targetId = (int)$data['id'];
    $action = trim($data['action']);

    if ($targetId === (int)$uid) {
        echo json_encode(['status' => 'error', 'message' => getApiMessage('cannot_modify_self')]);
        exit();
    }

    try {
        $groupCol = getGroupColumn($pdo);
        $checkSql = "
            WITH RECURSIVE users_tree AS(
                SELECT id, name, {$groupCol} FROM users WHERE id = ?
                UNION ALL
                SELECT u.id, u.name, u.{$groupCol} FROM users u
                INNER JOIN users_tree t ON u.{$groupCol} = t.id
            )
            SELECT u.id, u.active, u.is_delete
            FROM users u
            INNER JOIN users_tree t ON u.id = t.id
            WHERE u.id = ?
              AND (u.is_delete IS NULL OR u.is_delete = 'false')
        ";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([$uid, $targetId]);
        $targetUser = $checkStmt->fetch();

        if (!$targetUser) {
            echo json_encode(['status' => 'error', 'message' => getApiMessage('not_found')]);
            exit();
        }

        if ($action === 'disable') {
            $stmt = $pdo->prepare('UPDATE users SET active = ? WHERE id = ?');
            $stmt->execute(['3', $targetId]);
            echo json_encode(['status' => 'success', 'message' => getApiMessage('user_disabled_success')]);
            exit();
        } elseif ($action === 'enable') {
            $stmt = $pdo->prepare('UPDATE users SET active = ? WHERE id = ?');
            $stmt->execute(['0', $targetId]);
            echo json_encode(['status' => 'success', 'message' => getApiMessage('user_enabled_success')]);
            exit();
        } elseif ($action === 'delete') {
            $stmt = $pdo->prepare('UPDATE users SET is_delete = ? WHERE id = ?');
            $stmt->execute(['true', $targetId]);
            echo json_encode(['status' => 'success', 'message' => getApiMessage('user_deleted_success')]);
            exit();
        } else if ($action !== 'enable' && $action !== 'disable' && $action !== 'delete') {
            echo json_encode(['status' => 'error', 'message' => getApiMessage('invalid_request')]);
            exit();
        }

        echo json_encode(['status' => 'success', 'message' => getApiMessage('user_action_success')]);
        exit();
    } catch (Exception $e) {
        error_log("Failed to manage user: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => getApiMessage('user_action_failed')]);
        exit();
    }
}
?>
