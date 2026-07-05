<?php
/**
 * API Translation Handler
 * Central translation system for all API endpoints
 */

session_start();

// Set JSON response header
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Location: /doc/public/error/pages/404.html');
    // echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

// Get language from request
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['lang'])) {
    http_response_code(400);
    // echo json_encode(['status' => 'error', 'message' => 'Language parameter required']);
    exit;
}

// Validate language (only en or ar allowed)
$lang = $data['lang'];
if (!in_array($lang, ['en', 'ar'])) {
    http_response_code(400);
    // echo json_encode(['status' => 'error', 'message' => 'Invalid language']);
    exit;
}

// language in session
$_SESSION['lang'] = $lang;

// Return success response
// echo json_encode([
//     'status' => 'success', 
//     'message' => 'Language updated successfully',
//     'lang' => $lang
// ]);
?>
