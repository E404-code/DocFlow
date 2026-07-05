<?php
include_once '../config/database.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/doc/config/database.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/doc/api/api_translations.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/doc/app/authentication.php';

response_ccode('POST', 404, 'Location: /doc/public/error/pages/404.html');




if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate required fields
        $required_fields = ['customerName', 'nationalNumber', 'phone', 'passport', 'contact', 'Price'];
        foreach ($required_fields as $field) {
            if (empty(trim($_POST[$field] ?? ''))) {
                echo json_encode(['status' => 'error', 'message' => "Field $field is required"]);
                exit();
            }
        }

        // Sanitize and collect data
        $data = [
            'user_id' => $_SESSION['uid'],
            'title' => trim($_POST['customerName'] . ' - ' . $_POST['nationalNumber']),
            'customer_name' => trim($_POST['customerName']),
            'national_number' => trim($_POST['nationalNumber']),
            'phone' => trim($_POST['phone']),
            'passport' => trim($_POST['passport']),
            'contact' => trim($_POST['contact']),
            'price' => trim($_POST['Price']),
            'status' => trim($_POST['status'] ?? 'new'),
            'iban' => trim($_POST['ibm'] ?? ''),
            'notes' => trim($_POST['notes'] ?? ''),
            'visibility' => 'public', // Default visibility
            'passport_image' => null,
            'nn_image' => null
            
        ];

        // Check if this is an update operation
        $isUpdate = isset($_POST['id']) && !empty($_POST['id']);
        $oldDocument = null;

        if ($isUpdate) {
            $id = htmlspecialchars(trim($_POST['id']));
            if (!$id) {
                echo json_encode(["status" => "error", "message" => "Document ID is required for update"]);
                exit();
            }

            // Get existing document
            $stmt = $pdo->prepare("SELECT * FROM documents WHERE id = ? AND (is_delete IS NULL OR is_delete = 'false')");
            $stmt->execute([$id]);
            $oldDocument = $stmt->fetch();

            if (!$oldDocument) {
                echo json_encode(["status" => "error", "message" => "Document not found"]);
                exit();
            }
        }

        $mod = '';
        $uploadDir = '../uploads/documents/';
        if ($isUpdate) {
            $mod = "update";
        }

        // Handle file uploads
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Upload passport image
        if (isset($_FILES['passportImage']) && $_FILES['passportImage']['error'] === UPLOAD_ERR_OK) {
            $passportImage = uploadFile($_FILES['passportImage'], $uploadDir, 'passport');
            $data['passport_image'] = $passportImage;

            // Delete old passport image if updating and new image uploaded
            if ($isUpdate && !empty($oldDocument['passport_image']) && file_exists($uploadDir . $oldDocument['passport_image'])) {
                unlink($uploadDir . $oldDocument['passport_image']);
            }
        } elseif ($isUpdate && !empty($oldDocument['passport_image'])) {
            // Keep old passport image if no new image uploaded during update
            $data['passport_image'] = $oldDocument['passport_image'];
        }

        // Upload national number image
        if (isset($_FILES['nnImage']) && $_FILES['nnImage']['error'] === UPLOAD_ERR_OK) {
            $nnImage = uploadFile($_FILES['nnImage'], $uploadDir, 'nn');
            $data['nn_image'] = $nnImage;

            // Delete old nn image if updating and new image uploaded
            if ($isUpdate && !empty($oldDocument['nn_image']) && file_exists($uploadDir . $oldDocument['nn_image'])) {
                unlink($uploadDir . $oldDocument['nn_image']);
            }
        } elseif ($isUpdate && !empty($oldDocument['nn_image'])) {
            // Keep old nn image if no new image uploaded during update
            $data['nn_image'] = $oldDocument['nn_image'];
        }

        // Insert document into database
        $sql = "INSERT INTO documents (user_id, title, customer_name, national_number, phone, passport, 
                contact, price, status, iban, notes, passport_image, nn_image, visibility) 
                VALUES (:user_id, :title, :customer_name, :national_number, :phone, :passport, 
                :contact, :price, :status, :iban, :notes, :passport_image, :nn_image, :visibility)";
        // update document into database
        if ($mod === 'update') {
            $data['id'] = $id;
            $sql = "UPDATE documents 
            SET 
            title = :title,
            customer_name = :customer_name,
            national_number = :national_number,
            phone = :phone,
            passport = :passport,
            contact = :contact,
            price = :price,
            status = :status,
            iban = :iban,
            notes = :notes,
            passport_image = :passport_image,
            nn_image = :nn_image,
            visibility = :visibility
            WHERE id = :id";

        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
        // Log activity
        if ($mod === "update") {
            logActivity($_SESSION['uid'], 'update_document', 'Update document: ' . $data['title'], $data['id']);
        } else {
            logActivity($_SESSION['uid'], 'create_document', 'Created new document: ' . $data['title'], $pdo->lastInsertId());
        }

        if ($mod === "update") {
            echo json_encode(['status' => 'success', 'message' => 'Document update successfully']);
        } else {
            echo json_encode(['status' => 'success', 'message' => 'Document created successfully']);
        }

    } catch (Exception $e) {
        error_log("Error creating document: " . $e->getMessage());
        // echo json_encode(['status' => 'error', 'message' => 'Failed to create document']);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

function uploadFile($file, $uploadDir, $prefix)
{
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    // Validate file type
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Invalid file type. Only images are allowed.');
    }

    // Validate file size
    if ($file['size'] > $maxSize) {
        throw new Exception('File size too large. Maximum 5MB allowed.');
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $prefix . '_' . time() . '_' . uniqid() . '.' . $extension;
    $filepath = $uploadDir . $filename;

    // Move file to upload directory
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        throw new Exception('Failed to upload file.');
    }

    return $filename;
}

function logActivity($userId, $action, $description, $documentId = null)
{
    global $pdo;
    $sql = "INSERT INTO activity_log (user_id, action, description, document_id) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId, $action, $description, $documentId]);
}
?>
