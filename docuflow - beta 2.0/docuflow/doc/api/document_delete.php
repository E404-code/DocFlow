<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/doc/config/database.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/doc/api/api_translations.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/doc/app/authentication.php';

response_ccode('POST', 404, 'Location: /doc/public/error/pages/404.html');



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    try {
        $uid = htmlspecialchars(trim($_SESSION['uid'])) ?? '';
        if (empty($uid)) {
            echo json_encode(["status" => "error", "message" => "unauthorized"]);
            exit();
        }
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);
        if (!is_array($data) || empty($data)) {
            $data = $_POST ?? [];
        }
        if (empty($data) || !isset($data['id'])) {
            echo json_encode(["status" => "error", "message" => "delete filed try agine"]);
            exit();
        }

        $did = htmlspecialchars(trim((int) ($data['id'] ?? 0)));
        if ($did <= 0) {
            echo json_encode(["status" => "error", "message" => "invalid document id"]);
            exit();
        }
        $role = $_SESSION['role'] ?? '';
        if ($role === 'admin') {
            $stmt = $pdo->prepare("SELECT d.id, d.user_id, d.title FROM documents d WHERE d.id = ? AND (d.is_delete IS NULL OR d.is_delete = 'false')");
            $stmt->execute([$did]);
        } else {
            $stmt = $pdo->prepare("
                WITH RECURSIVE doc_by_group AS(
                    SELECT id, name, group_id FROM users WHERE id = ?
                    UNION ALL
                    SELECT u.id, u.name, u.group_id FROM users u
                    INNER JOIN doc_by_group g ON u.group_id = g.id
                )
                SELECT d.id, d.user_id, d.title
                FROM documents d
                INNER JOIN doc_by_group g ON d.user_id = g.id
                WHERE d.id = ?
                  AND (d.is_delete IS NULL OR d.is_delete = 'false')
            ");
            $stmt->execute([$uid, $did]);
        }
        $document = $stmt->fetch();


        if (!$document) {
            echo json_encode(["status" => "error", "message" => "delete filed document not fond try agine"]);
            exit();
        }


        $dStmt = $pdo->prepare("UPDATE `documents` SET is_delete = 'true' WHERE id = ?");
        $dStmt->execute([$document['id']]);

        // Log activity

        $logStmt = $pdo->prepare('INSERT INTO activity_log (user_id, action, description, document_id, created_at) VALUES (?, ?, ?, ?, NOW())');
        $logStmt->execute([$uid, 'delete_document', 'Deleted document: ' . ($document['title'] ?? ''), $document['id']]);

        echo json_encode(["status" => "success", "message" => "document delete successfuly"]);
        exit();
    } catch (Exception $e) {
        error_log("Document delete failed: " . $e->getMessage());
        echo json_encode(["status" => "error", "message" => "delete filed try agine"]);
    }
}
