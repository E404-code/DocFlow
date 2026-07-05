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

        // Statistics
        $statsSql = $cte . "
            SELECT
                (SELECT COUNT(*) FROM documents d INNER JOIN doc_by_group g ON d.user_id = g.id WHERE (d.is_delete IS NULL OR d.is_delete = 'false')) AS total_docs,
                (SELECT COUNT(*) FROM documents d INNER JOIN doc_by_group g ON d.user_id = g.id WHERE (d.is_delete IS NULL OR d.is_delete = 'false')) AS department_docs,
                (SELECT COUNT(*) FROM documents d WHERE d.user_id = ? AND (d.is_delete IS NULL OR d.is_delete = 'false')) AS my_docs,
                (SELECT COUNT(*) FROM users u INNER JOIN doc_by_group g ON u.id = g.id WHERE u.active = '1' AND (u.is_delete IS NULL OR u.is_delete = 'false')) AS active_users
        ";
        $statsStmt = $pdo->prepare($statsSql);
        $statsStmt->execute([$uid, $uid]);
        $statistics = $statsStmt->fetch();

        // Status counts
        $statusSql = $cte . "
            SELECT d.status, COUNT(*) AS count
            FROM documents d
            INNER JOIN doc_by_group g ON d.user_id = g.id
            GROUP BY d.status
        ";
        $statusStmt = $pdo->prepare($statusSql);
        $statusStmt->execute([$uid]);
        $statusRows = $statusStmt->fetchAll();

        $statusCounts = [
            'new' => 0,
            'w-resv' => 0,
            'on-resv' => 0,
            'enough' => 0,
            'pending-delivery' => 0,
            'delivered' => 0
        ];
        foreach ($statusRows as $row) {
            $key = $row['status'];
            if (isset($statusCounts[$key])) {
                $statusCounts[$key] = (int)$row['count'];
            }
        }

        // Recent documents
        $docsSql = $cte . "
            SELECT d.id, d.title, d.customer_name, d.visibility, d.created_at, d.passport_image, d.nn_image
            FROM documents d
            INNER JOIN doc_by_group g ON d.user_id = g.id
            WHERE (d.is_delete IS NULL OR d.is_delete = 'false')
            ORDER BY d.created_at DESC
            LIMIT 5
        ";
        $docsStmt = $pdo->prepare($docsSql);
        $docsStmt->execute([$uid]);
        $recentDocs = $docsStmt->fetchAll();

        // Recent activity
        $activitySql = $cte . "
            SELECT a.action, a.description, a.created_at, u.name
            FROM activity_log a
            INNER JOIN users u ON a.user_id = u.id
            INNER JOIN doc_by_group g ON u.id = g.id
            ORDER BY a.created_at DESC
            LIMIT 6
        ";
        $activityStmt = $pdo->prepare($activitySql);
        $activityStmt->execute([$uid]);
        $activity = $activityStmt->fetchAll();

        echo json_encode([
            'status' => 'success',
            'data' => [
                'statistics' => $statistics ?: [
                    'total_docs' => 0,
                    'department_docs' => 0,
                    'my_docs' => 0,
                    'active_users' => 0
                ],
                'status_counts' => $statusCounts,
                'recent_documents' => $recentDocs ?: [],
                'activity' => $activity ?: [],
                'updated_at' => date('c')
            ]
        ]);
        exit();
    } catch (Exception $e) {
        error_log("Dashboard stats error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => getApiMessage('server_error')]);
        exit();
    }
}
?>
