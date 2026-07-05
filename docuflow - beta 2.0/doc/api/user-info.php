<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/doc/config/database.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/doc/api/api_translations.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/doc/app/authentication.php';

response_ccode('POST', 404, 'Location: /doc/public/error/pages/404.html');



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data) || !isset($data['email']) || empty($data['email'])) {
        echo json_encode(['status' => 'error', 'massage' => 'loading user info filed']);
        exit();
    }
    $sqlQuery = "SELECT id, name, email, role, created_at FROM users WHERE email = ? AND (is_delete IS NULL OR is_delete = 'false')";
    $stmt = $pdo->prepare($sqlQuery);
    $stmt->execute([htmlspecialchars(trim($data['email']))]);
    $userInfo = $stmt->fetch();

    if (!$userInfo || empty($userInfo)) {
        echo json_encode(['status' => 'error', 'message' => 'your account not found try agein']);
        exit();
    }

    echo json_encode(['status' => 'success', 'message' => 'loading data successfuly', 'content' => $userInfo]);
}
