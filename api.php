<?php
header("Content-Type: application/json");
$dataFile = __DIR__ . '/counter_data.json';
$tokenFile = __DIR__ . '/tokens.token';
if (!file_exists($dataFile)) {
    file_put_contents($dataFile, json_encode([]));
}
if (!file_exists($tokenFile)) {
    file_put_contents($tokenFile, json_encode([]));
}
$data = json_decode(file_get_contents($dataFile), true);
$tokens = json_decode(file_get_contents($tokenFile), true);
$token = $_SERVER['HTTP_TOKEN'] ?? '';
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
$tokenHash = hash('sha256', $token);
if (!isset($tokens[$tokenHash]) || $tokens[$tokenHash] !== hash('sha256', $authHeader)) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}
$action = $_GET['action'] ?? '';
if ($action === 'increment') {
    if (!isset($data[$token])) {
        $data[$token] = 0;
    }
    $data[$token]++;
    file_put_contents($dataFile, json_encode($data));
    echo json_encode(['status' => 'success', 'count' => $data[$token]]);
} elseif ($action === 'get') {
    if (!isset($data[$token])) {
        echo json_encode(['status' => 'error', 'message' => 'Token not found']);
    } else {
        echo json_encode(['status' => 'success', 'count' => $data[$token]]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}
?>