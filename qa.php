<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

$file = __DIR__ . '/qa_data.json';

// Load existing data
function loadData($file) {
    if (!file_exists($file)) return [];
    $raw = file_get_contents($file);
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

// Save data
function saveData($file, $data) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

if ($method === 'GET' && $action === 'list') {
    echo json_encode(['ok' => true, 'questions' => loadData($file)]);
    exit;
}

if ($method === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true);
    $data = loadData($file);

    if ($action === 'submit') {
        $text = trim($body['text'] ?? '');
        $name = trim($body['name'] ?? '');
        if (!$text) { echo json_encode(['ok' => false, 'error' => 'Empty']); exit; }
        $entry = [
            'id'    => uniqid(),
            'text'  => htmlspecialchars($text, ENT_QUOTES),
            'name'  => htmlspecialchars($name, ENT_QUOTES),
            'time'  => date('d.m.Y H:i'),
            'reply' => ''
        ];
        array_unshift($data, $entry);
        saveData($file, $data);
        echo json_encode(['ok' => true, 'entry' => $entry]);
        exit;
    }

    if ($action === 'reply') {
        $pass  = $body['pass']  ?? '';
        $id    = $body['id']    ?? '';
        $reply = trim($body['reply'] ?? '');
        // Change this passphrase!
        if ($pass !== 'commerzbank2025') {
            echo json_encode(['ok' => false, 'error' => 'Wrong passphrase']); exit;
        }
        foreach ($data as &$q) {
            if ($q['id'] === $id) {
                $q['reply'] = htmlspecialchars($reply, ENT_QUOTES);
                break;
            }
        }
        saveData($file, $data);
        echo json_encode(['ok' => true]);
        exit;
    }

    if ($action === 'delete') {
        $pass = $body['pass'] ?? '';
        $id   = $body['id']   ?? '';
        if ($pass !== 'commerzbank2025') {
            echo json_encode(['ok' => false, 'error' => 'Wrong passphrase']); exit;
        }
        $data = array_values(array_filter($data, fn($q) => $q['id'] !== $id));
        saveData($file, $data);
        echo json_encode(['ok' => true]);
        exit;
    }
}

echo json_encode(['ok' => false, 'error' => 'Unknown action']);
