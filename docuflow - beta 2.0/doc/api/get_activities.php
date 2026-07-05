<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/doc/config/database.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/doc/api/api_translations.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/doc/app/authentication.php';

response_ccode('POST', 404, 'Location: /doc/public/error/pages/404.html');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $uid = htmlspecialchars(trim($_SESSION['uid'])) ?? '';
    if (empty($uid)) {
        echo json_encode(['status' => 'error', 'message' => getApiMessage('unauthorized')]);
        exit();
    }

    try {
        $cte = "
            WITH RECURSIVE doc_by_group AS(
                SELECT id, name, group_id FROM users WHERE id = ?
                UNION ALL
                SELECT u.id, u.name, u.group_id FROM users u
                INNER JOIN doc_by_group g ON u.group_id = g.id
            )
        ";

        $activitiesSql = $cte . "
            SELECT a.id, a.action, a.description, a.created_at, u.name
            FROM activity_log a
            INNER JOIN users u ON a.user_id = u.id
            INNER JOIN doc_by_group g ON u.id = g.id
            ORDER BY a.created_at DESC
            LIMIT 200
        ";
        $stmt = $pdo->prepare($activitiesSql);
        $stmt->execute([$uid]);
        $activities = $stmt->fetchAll();

        $statsSql = $cte . "
            SELECT
                (SELECT COUNT(*) FROM activity_log a INNER JOIN doc_by_group g ON a.user_id = g.id) AS total_activities,
                (SELECT COUNT(*) FROM activity_log a INNER JOIN doc_by_group g ON a.user_id = g.id WHERE DATE(a.created_at) = CURDATE()) AS today_activities,
                (SELECT COUNT(*) FROM users u INNER JOIN doc_by_group g ON u.id = g.id WHERE u.active = '1' AND (u.is_delete IS NULL OR u.is_delete = 'false')) AS active_users
        ";
        $statsStmt = $pdo->prepare($statsSql);
        $statsStmt->execute([$uid]);
        $stats = $statsStmt->fetch();

        echo json_encode([
            'status' => 'success',
            'data' => [
                'activities' => $activities ?: [],
                'stats' => $stats ?: [
                    'total_activities' => 0,
                    'today_activities' => 0,
                    'active_users' => 0
                ]
            ]
        ]);
        exit();
    } catch (Exception $e) {
        error_log("Failed to fetch activities: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => getApiMessage('server_error')]);
        exit();
    }
}
?>
