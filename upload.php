<?php
/**
 * upload.php — handles file uploads for the portfolio data page
 * Place this file in the same directory as your HTML files on Ionos
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// ── Config ──────────────────────────────────────────────
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('MAX_SIZE',   20 * 1024 * 1024); // 20 MB per file
define('ALLOWED_EXT', ['pdf','doc','docx','xls','xlsx','ppt','pptx','png','jpg','jpeg','txt','zip']);

// Create uploads directory if it doesn't exist
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

// ── Guard: POST only ────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

if (empty($_FILES['file'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No file received']);
    exit;
}

$file = $_FILES['file'];

// ── Validate ────────────────────────────────────────────
if ($file['error'] !== UPLOAD_ERR_OK) {
    $errors = [
        UPLOAD_ERR_INI_SIZE   => 'File too large (server limit)',
        UPLOAD_ERR_FORM_SIZE  => 'File too large (form limit)',
        UPLOAD_ERR_PARTIAL    => 'Partial upload',
        UPLOAD_ERR_NO_FILE    => 'No file uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing tmp directory',
        UPLOAD_ERR_CANT_WRITE => 'Cannot write file',
    ];
    echo json_encode(['success' => false, 'error' => $errors[$file['error']] ?? 'Upload error']);
    exit;
}

if ($file['size'] > MAX_SIZE) {
    echo json_encode(['success' => false, 'error' => 'File exceeds 20 MB limit']);
    exit;
}

$originalName = basename($file['name']);
$ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

if (!in_array($ext, ALLOWED_EXT)) {
    echo json_encode(['success' => false, 'error' => 'File type not allowed: .' . $ext]);
    exit;
}

// Sanitise filename
$safeName = preg_replace('/[^a-zA-Z0-9._\-]/', '_', $originalName);
$safeName = preg_replace('/_+/', '_', $safeName);

// If file already exists, append timestamp
$destPath = UPLOAD_DIR . $safeName;
if (file_exists($destPath)) {
    $base    = pathinfo($safeName, PATHINFO_FILENAME);
    $safeName = $base . '_' . time() . '.' . $ext;
    $destPath = UPLOAD_DIR . $safeName;
}

// ── Move file ───────────────────────────────────────────
if (!move_uploaded_file($file['tmp_name'], $destPath)) {
    echo json_encode(['success' => false, 'error' => 'Failed to save file']);
    exit;
}

echo json_encode([
    'success'  => true,
    'filename' => $safeName,
    'original' => $originalName,
    'size'     => $file['size'],
    'ext'      => $ext,
]);
