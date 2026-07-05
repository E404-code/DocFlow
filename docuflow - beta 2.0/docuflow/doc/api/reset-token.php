<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/doc/config/database.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/doc/api/api_translations.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/doc/app/authentication.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/doc/app/mailer.php';

response_ccode('POST', 404, 'Location: /doc/public/error/pages/404.html');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid = $_SESSION['uid'] ?? null;
    if (!$uid) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT id, name, email, active, is_delete FROM users WHERE id = ?");
        $stmt->execute([$uid]);
        $user = $stmt->fetch();
        if (!$user || $user['is_delete'] === 'true' || $user['active'] === '3') {
            echo json_encode(['status' => 'error', 'message' => 'User not found or disabled']);
            exit();
        }

        $rawToken = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $rawToken);

        $update = $pdo->prepare("UPDATE users SET token = ? WHERE id = ?");
        $update->execute([$tokenHash, $uid]);

        $subject = 'Your New API Token';
        $body = "Hello {$user['name']},\n\nHere is your new API token (save it in a safe place):\n\n{$rawToken}\n\nIf you did not request this, contact support.";
        $sent = send_mail_smtp($user['email'], $subject, $body);

        if (!$sent) {
            error_log('Failed to send reset token email to ' . $user['email']);
        }

        echo json_encode(['status' => 'success', 'message' => 'Token regenerated and sent to your email', 'email_sent' => $sent]);
    } catch (Exception $e) {
        error_log("Error resetting token: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Failed to regenerate token']);
    }
}
?>
