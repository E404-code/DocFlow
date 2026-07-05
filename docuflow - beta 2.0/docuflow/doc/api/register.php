<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/doc/config/database.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/doc/api/api_translations.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/doc/app/authentication.php';

response_ccode('POST', 404, 'Location: /doc/public/error/pages/404.html');



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid = $_SESSION['uid'];
    $checkpermission = $pdo->prepare("SELECT * FROM users WHERE id = ? AND (is_delete IS NULL OR is_delete = 'false')");
    $checkpermission->execute([$uid]);
    $user = $checkpermission->fetch();
    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
        exit();
    }
    if ($user['role'] !== 'admin' && $user['role'] !== 'group') {
        echo json_encode(['status' => 'error', 'message' => getApiMessage('register_per')]);
        exit();
    }
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirmpassword = trim($_POST['confirmPassword'] ?? '');
    $role = trim($_POST['role-option'] ?? '');

    // Validation
    if (empty($name) || empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => getApiMessage('required_fields')]);
        exit();
    }

    if ($password !== $confirmpassword) {
        echo json_encode(['status' => 'error', 'message' => getApiMessage('pass_not_match')]);
        exit();
    }

    if (strlen($password) < 6) {
        echo json_encode(['status' => 'error', 'message' => getApiMessage('pass_less_6')]);
        exit();
    }

    // Check if email already exists
    $checkQuery = $pdo->prepare("SELECT id FROM users WHERE email = ? AND (is_delete IS NULL OR is_delete = 'false')");
    $checkQuery->execute([$email]);

    if ($checkQuery->fetch()) {
        echo json_encode(['status' => 'error', 'message' => getApiMessage('email_exists')]);
        exit();
    }

    // Hash password securely
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Generate API token (store hash only)
    $rawToken = bin2hex(random_bytes(32));
    $tokenHash = hash('sha256', $rawToken);

    // // Insert new user
    $insertQuery = $pdo->prepare('INSERT INTO users (name, email, password, role, group_id, active, token, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())');

    if ($insertQuery->execute([$name, $email, $hashedPassword, $role, $user['id'], 0, $tokenHash])) {
        try {
            $logStmt = $pdo->prepare('INSERT INTO activity_log (user_id, action, description) VALUES (?, ?, ?)');
            $logStmt->execute([$_SESSION['uid'], 'create_user', 'Created user: ' . $email]);
        } catch (Exception $e) {
            error_log("Activity log failed: " . $e->getMessage());
        }
        include_once $_SERVER['DOCUMENT_ROOT'] . '/doc/app/mailer.php';
        $subject = 'Your API Token';
        $body = "Hello {$name},\n\nHere is your API token (save it in a safe place):\n\n{$rawToken}\n\nIf you did not request this, contact support.";
        $sent = send_mail_smtp($email, $subject, $body);
        if (!$sent) {
            error_log('Failed to send token email to ' . $email);
        }
        echo json_encode(['status' => 'success', 'message' => getApiMessage('registration_success'), 'email_sent' => $sent]);
    } else {
        echo json_encode(['status' => 'error', 'message' => getApiMessage('registration_failed')]);
    }
}
?>
