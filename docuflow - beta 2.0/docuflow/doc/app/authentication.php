<?php
session_start();

function get_bearer_token() {
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['Authorization'] ?? '';
    if (!$header && function_exists('getallheaders')) {
        $headers = getallheaders();
        if (isset($headers['Authorization'])) {
            $header = $headers['Authorization'];
        }
    }
    if ($header && preg_match('/Bearer\s+(\S+)/i', $header, $matches)) {
        return trim($matches[1]);
    }
    $fallback = $_SERVER['HTTP_X_API_TOKEN'] ?? '';
    return $fallback ? trim($fallback) : null;
}

function hash_api_token($token) {
    return hash('sha256', $token);
}

if (!isset($_SESSION['uid'])) {
    $token = get_bearer_token();
    if ($token && isset($pdo)) {
        try {
            $tokenHash = hash_api_token($token);
            $stmt = $pdo->prepare("SELECT id, active, is_delete FROM users WHERE token = ? LIMIT 1");
            $stmt->execute([$tokenHash]);
            $user = $stmt->fetch();
            if ($user && ($user['is_delete'] === null || $user['is_delete'] === 'false') && $user['active'] !== '3') {
                $_SESSION['uid'] = $user['id'];
                $_SESSION['auth_via_token'] = true;
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
                exit();
            }
        } catch (Exception $e) {
            error_log("Token auth failed: " . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
            exit();
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
        exit();
    }
}

// Session timeout (in seconds)
$sessionTimeout = 1800;

if (empty($_SESSION['auth_via_token']) && isset($_SESSION['last_activity'])) {
    if (time() - $_SESSION['last_activity'] > $sessionTimeout) {
        if (isset($pdo)) {
            try {
                $stmt = $pdo->prepare('UPDATE users SET active = ? WHERE id = ?');
                $stmt->execute(['0', $_SESSION['uid']]);
            } catch (Exception $e) {
                error_log("Failed to update active status on timeout: " . $e->getMessage());
            }
        }
        session_unset();
        session_destroy();
        echo json_encode(['status' => 'error', 'message' => 'Session expired']);
        exit();
    }
}
$_SESSION['last_activity'] = time();

// Block disabled accounts that were turned off by manager
if (isset($pdo)) {
    try {
        $stmt = $pdo->prepare('SELECT active FROM users WHERE id = ?');
        $stmt->execute([$_SESSION['uid']]);
        $row = $stmt->fetch();
        if ($row && isset($row['active']) && $row['active'] === '3') {
            session_unset();
            session_destroy();
            echo json_encode(['status' => 'error', 'message' => 'Account disabled']);
            exit();
        }
    } catch (Exception $e) {
        error_log("Failed to check active status: " . $e->getMessage());
    }
}

function response_ccode($emthod, $code, $url) {
    if ($_SERVER['REQUEST_METHOD'] !== $emthod) {
    http_response_code($code);
    header($url);
    exit();
}
}
