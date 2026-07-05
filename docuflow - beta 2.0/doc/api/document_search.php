<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/doc/config/database.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/doc/api/api_translations.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/doc/app/authentication.php';

response_ccode('POST', 404, 'Location: /doc/public/error/pages/404.html');


if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        echo json_encode(['status' => 'error', 'message' => 'Document NOt Fond']);
        exit();
    }


    if (!$data['q'] || trim($data['q']) === '') {
        echo json_encode(['status' => 'error', 'message' => 'Document Not Fond']);
        exit();
    }


    try {

        $id = htmlspecialchars(trim($_SESSION['uid'])) ?? '';
        $q = htmlspecialchars(trim($data['q'])) ?? '';

        $like = "%$q%";

        $query = "
            WITH RECURSIVE doc_by_group AS(
            SELECT id, name, group_id FROM users WHERE id = ?
    
            UNION ALL
            SELECT u.id, u.name, u.group_id FROM users u
            INNER JOIN doc_by_group g ON u.group_id = g.id
            )
      
            SELECT d.id, d.customer_name, d.national_number, d.phone, d.passport, d.created_at 
            FROM documents d INNER JOIN doc_by_group g ON d.user_id = g.id
    
           WHERE d.user_id = g.id
            AND (d.is_delete IS NULL OR d.is_delete = 'false')
            AND (
            customer_name   LIKE ?
            OR national_number LIKE ?
            OR phone           LIKE ?
            OR passport        LIKE ?
            OR contact         LIKE ?
            OR status          LIKE ?
            OR iban            LIKE ?
            OR notes           LIKE ?
            OR created_at      LIKE ?
            )
            ORDER BY created_at DESC
            LIMIT 200

        ";


        $stmt = $pdo->prepare($query);
        $stmt->execute([$id, $like, $like, $like, $like, $like, $like, $like, $like, $like]);
        $docsAviableForSearch = $stmt->fetchAll();


        echo json_encode(['document' => $docsAviableForSearch, 'status' => 'success']);
    } catch (Exception $e) {
        error_log("Error fetching documents: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Failed to fetch documents']);
    }

}
