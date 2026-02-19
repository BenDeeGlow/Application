<?php
/**
 * delete.php — deletes an uploaded file
 * POST body: { "file": "filename.pdf" }
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('ALLOWED_EXT', ['pdf','doc','docx','xls','xlsx','ppt','pptx','png','jpg','jpeg','txt','zip']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'POST required']);
    exit;
}

$body     = json_decode(file_get_contents('php://input'), true);
$filename = basename($body['file'] ?? '');
$ext      = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

if (!$filename || !in_array($ext, ALLOWED_EXT)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid file']);
    exit;
}

$path = UPLOAD_DIR . $filename;

if (!file_exists($path)) {
    echo json_encode(['success' => false, 'error' => 'File not found']);
    exit;
}

if (unlink($path)) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Could not delete file']);
}
