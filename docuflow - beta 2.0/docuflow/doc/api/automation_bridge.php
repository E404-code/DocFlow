<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/doc/config/database.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/doc/api/api_translations.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/doc/app/authentication.php';

response_ccode('POST', 404, 'Location: /doc/public/error/pages/404.html');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || empty($data['ids']) || !is_array($data['ids'])) {
        echo json_encode(['status' => 'error', 'message' => getApiMessage('invalid_request')]);
        exit();
    }

    // Placeholder for automation integration
    echo json_encode([
        'status' => 'success',
        'message' => 'Sent to automation',
        'ids' => $data['ids']
    ]);
    exit();
}
?>
