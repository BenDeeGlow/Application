<?php
/**
 * list_files.php — returns JSON array of uploaded files
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('ALLOWED_EXT', ['pdf','doc','docx','xls','xlsx','ppt','pptx','png','jpg','jpeg','txt','zip']);

if (!is_dir(UPLOAD_DIR)) {
    echo json_encode(['success' => true, 'files' => []]);
    exit;
}

$files = [];
foreach (scandir(UPLOAD_DIR) as $f) {
    if ($f === '.' || $f === '..') continue;
    $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_EXT)) continue;
    $path = UPLOAD_DIR . $f;
    $files[] = [
        'filename' => $f,
        'size'     => filesize($path),
        'ext'      => $ext,
        'modified' => filemtime($path),
    ];
}

// Newest first
usort($files, fn($a, $b) => $b['modified'] - $a['modified']);

echo json_encode(['success' => true, 'files' => $files]);
