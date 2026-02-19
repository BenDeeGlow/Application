<?php
/**
 * download.php — serves a file for download
 * Usage: download.php?file=filename.pdf
 */

define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('ALLOWED_EXT', ['pdf','doc','docx','xls','xlsx','ppt','pptx','png','jpg','jpeg','txt','zip']);

if (empty($_GET['file'])) {
    http_response_code(400);
    echo 'Missing file parameter';
    exit;
}

// Sanitise — no path traversal
$filename = basename($_GET['file']);
$ext      = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

if (!in_array($ext, ALLOWED_EXT)) {
    http_response_code(403);
    echo 'File type not allowed';
    exit;
}

$path = UPLOAD_DIR . $filename;

if (!file_exists($path) || !is_file($path)) {
    http_response_code(404);
    echo 'File not found';
    exit;
}

$mimeMap = [
    'pdf'  => 'application/pdf',
    'doc'  => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'xls'  => 'application/vnd.ms-excel',
    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'ppt'  => 'application/vnd.ms-powerpoint',
    'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    'png'  => 'image/png',
    'jpg'  => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'txt'  => 'text/plain',
    'zip'  => 'application/zip',
];

$mime = $mimeMap[$ext] ?? 'application/octet-stream';

header('Content-Type: ' . $mime);
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($path));
header('Cache-Control: no-cache');
readfile($path);
exit;
