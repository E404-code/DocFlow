<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/doc/config/database.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/doc/api/api_translations.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(404);
    header('Location: /doc/public/error/pages/404.html');
    exit();
}

// Start secure session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $emRGX = "/^(?!.*[._-]{2})[a-zA-Z](([a-zA-Z0-9._-]+)?[a-zA-Z0-9]+)?@(?=[a-zA-Z]+)([\w\-._]+)?[a-zA-Z]+\.[a-zA-Z]+$/";
    $password = trim($_POST['password'] ?? '');
    $passRGX = "/^(?!.*[\s`|\/\'\"]{1})(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[?!@$\#%^&*-]).{6,35}$/";
    $agreet = trim($_POST['agreet'] ?? 'false');

    function validate_value($rgx, $var)
    {
        if (preg_match($rgx, $var) === 1) {
            return true;
        }
        $ms = ['status' => 'error', 'message' => getApiMessage('invalid_data')];
        echo json_encode($ms);
        return false;
    }

    if (validate_value($emRGX, $email) === true && validate_value($passRGX, $password) === true && $agreet === 'true') {

        // First, get the user by email only
        $qury = $pdo->prepare("SELECT * FROM users WHERE email = ? AND (is_delete IS NULL OR is_delete = 'false')");
        $qury->execute([$email]);
        $user = $qury->fetch();

        // Verify password using secure password verification
        if (!$user || !password_verify($password, $user['password'])) {
            $ms = ['status' => 'error', 'message' => getApiMessage('invalid_credentials')];
            echo json_encode($ms);
            exit();
        }

        // Block disabled accounts
        if (isset($user['active']) && $user['active'] === '3') {
            $ms = ['status' => 'error', 'message' => getApiMessage('account_disabled')];
            echo json_encode($ms);
            exit();
        }

        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);

        $_SESSION['uid'] = $user['id'] ?? ''; // Store user ID instead of email
        $_SESSION['email'] = $user['email'] ?? '';
        $_SESSION['name'] = $user['name'] ?? '';
        $_SESSION['role'] = $user['role'] ?? '';

        // Mark user as active
        try {
            $activeStmt = $pdo->prepare('UPDATE users SET active = ? WHERE id = ?');
            $activeStmt->execute(['1', $user['id']]);
            $logStmt = $pdo->prepare('INSERT INTO activity_log (user_id, action, description) VALUES (?, ?, ?)');
            $logStmt->execute([$user['id'], 'login', 'User logged in']);
        } catch (Exception $e) {
            error_log("Failed to update active status: " . $e->getMessage());
        }

        $ms = ['status' => 'success', 'message' => getApiMessage('login_successful')];
        echo json_encode($ms);
        exit();
    }

}
?>
