<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/doc/config/chat.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit();
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request format']);
    exit();
}

$sessionId = trim($data['sessionId'] ?? '');
$message = trim($data['message'] ?? '');

if ($sessionId === '' || $message === '') {
    echo json_encode(['status' => 'error', 'message' => 'Missing sessionId or message']);
    exit();
}

$config = require $_SERVER['DOCUMENT_ROOT'] . '/doc/config/chat.php';
$url = $config['n8n_webhook_url'] ?? '';
$timeout = (int) ($config['timeout'] ?? 25);

if ($url === '') {
    echo json_encode(['status' => 'error', 'message' => 'Chat service not configured']);
    exit();
}

$payload = [
    [
        'sessionId' => $sessionId,
        'action' => 'sendMessage',
        'chatInput' => $message
    ]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

$response = curl_exec($ch);
$error = curl_error($ch);
$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response === false) {
    echo json_encode(['status' => 'error', 'message' => 'Chat service error', 'details' => $error]);
    exit();
}

$decoded = json_decode($response, true);
$reply = '';

if (is_array($decoded)) {
    // Expected response: [{sessionId, action, chatInput}]
    if (isset($decoded[0]['chatInput'])) {
        $reply = $decoded[0]['chatInput'];
    } elseif (isset($decoded['reply'])) {
        $reply = $decoded['reply'];
    }
}

if ($reply === '') {
    $reply = 'تم استلام رسالتك وسيتم الرد لاحقاً.';
}

echo json_encode(['status' => 'success', 'reply' => $reply, 'code' => $statusCode]);
