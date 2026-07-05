<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/doc/config/database.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/doc/api/api_translations.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/doc/app/authentication.php';

response_ccode('POST', 404, 'Location: /doc/public/error/pages/404.html');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid = htmlspecialchars(trim($_SESSION['uid'])) ?? '';
    try {
        $sql =
            "
            WITH RECURSIVE doc_by_group AS(
            SELECT id, name, group_id FROM users WHERE id = ?
    
            UNION ALL
            SELECT u.id, u.name, u.group_id FROM users u
            INNER JOIN doc_by_group g ON u.group_id = g.id
             )
            SELECT d.id,
                   d.title,
                   d.customer_name,
                   d.national_number,
                   d.phone,
                   d.passport,
                   d.contact,
                   d.status,
                   d.iban,
                   d.notes,
                   d.visibility,
                   d.created_at
            FROM documents
            d INNER JOIN doc_by_group g ON d.user_id = g.id WHERE (d.is_delete IS NULL OR d.is_delete = 'false')
            ORDER BY created_at DESC;
            ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$uid]);
        $docs = $stmt->fetchAll();
        if (!$docs) {
            $docs = [];
            echo json_encode(["status" => "success", 'document' => $docs]);
            exit();
        }

        echo json_encode(["status" => "success", 'document' => $docs]);

    } catch (Exception $e) {
        error_log("Error fetching documents: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Failed to fetch documents']);
    }
}
?>
