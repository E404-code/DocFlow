<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/doc/config/database.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/doc/api/api_translations.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/doc/app/authentication.php';

response_ccode('POST', 404, 'Location: /doc/public/error/pages/404.html');





if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON data from request body
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Extract and sanitize input data
    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $currentPassword = trim($data['currentPassword'] ?? '');
    $newPassword = trim($data['newPassword'] ?? '');
    
    // Regex patterns from auth.js and register.js
    $nameRGX = "/^(?!\s)(?!.*\s{2,})[\p{L}]{3,}(?:\s[\p{L}]{3,})*$/u";
    $emRGX = "/^(?!.*[._-]{2})[a-zA-Z](([a-zA-Z0-9._-]+)?[a-zA-Z0-9]+)?@(?=[a-zA-Z]+)([\w\-._]+)?[a-zA-Z]+\.[a-zA-Z]+$/";
    $passRGX = "/^(?!.*[\s`|\/\'\"]{1})(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[?!@$\#%^&*-]).{6,35}$/";
    
    // Validation function from api/index.php
    function validate_value($rgx, $var)
    {
        if (preg_match($rgx, $var) === 1) {
            return true;
        }
        $ms = ['status' => 'error', 'message' => 'invalid data format'];
        echo json_encode($ms);
        return false;
    }
    
    // Get current user info
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND (is_delete IS NULL OR is_delete = 'false')");
    $stmt->execute([$_SESSION['uid']]);
    $currentUser = $stmt->fetch();
    
    if (!$currentUser) {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
        exit();
    }
    
    // Validate required fields (name and email)
    if (empty($name) || empty($email)) {
        echo json_encode(['status' => 'error', 'message' => 'Name and email are required']);
        exit();
    }
    
    // Validate name format
    if (!validate_value($nameRGX, $name)) {
        exit();
    }
    
    // Validate email format
    if (!validate_value($emRGX, $email)) {
        exit();
    }
    
    // Check if email is being changed and if it already exists for another user
    if ($email !== $currentUser['email']) {
        $checkEmail = $pdo->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
        $checkEmail->execute([$email, $_SESSION['uid']]);
        if ($checkEmail->fetch()) {
            echo json_encode(['status' => 'error', 'message' => 'Email already exists']);
            exit();
        }
    }
    
    // Update basic profile information
    $updateStmt = $pdo->prepare('UPDATE users SET name = ?, email = ? WHERE id = ?');
    $profileUpdated = $updateStmt->execute([$name, $email, $_SESSION['uid']]);
    
    // Update session data if email changed
    if ($email !== $currentUser['email']) {
        $_SESSION['email'] = $email;
        $_SESSION['name'] = $name;
    }
    
    $response = ['status' => 'success', 'message' => 'Profile updated successfully'];
    
    // Handle password change if provided
    if (!empty($currentPassword) && !empty($newPassword)) {
        // Validate new password format
        if (!validate_value($passRGX, $newPassword)) {
            exit();
        }
        
        // Verify current password
        if (!password_verify($currentPassword, $currentUser['password'])) {
            echo json_encode(['status' => 'error', 'message' => 'Current password is incorrect']);
            exit();
        }
        
        // Hash new password securely
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        // Update password
        $passwordStmt = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
        $passwordUpdated = $passwordStmt->execute([$hashedPassword, $_SESSION['uid']]);
        
        if ($passwordUpdated) {
            $response['message'] = 'Profile and password updated successfully';
        } else {
            $response = ['status' => 'error', 'message' => 'Password update failed'];
        }
    }
    
    echo json_encode($response);
    exit();
}
?>
