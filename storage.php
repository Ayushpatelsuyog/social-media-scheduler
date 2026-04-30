<?php
require_once 'config.php';

header('Content-Type: application/json');

$action = isset($_GET['action']) ? $_GET['action'] : '';
$storageDir = __DIR__ . '/data';

// Create storage directory if it doesn't exist
if (!is_dir($storageDir)) {
    mkdir($storageDir, 0755, true);
}

switch ($action) {
    case 'save':
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || !isset($input['key']) || !isset($input['plan'])) {
            echo json_encode(['success' => false, 'error' => 'Invalid input']);
            exit;
        }
        
        $key = preg_replace('/[^a-zA-Z0-9_]/', '_', $input['key']);
        $filePath = $storageDir . '/' . $key . '.json';
        
        $result = file_put_contents($filePath, json_encode($input['plan'], JSON_PRETTY_PRINT));
        echo json_encode(['success' => $result !== false]);
        break;

    case 'load':
        $key = isset($_GET['key']) ? $_GET['key'] : '';
        if (empty($key)) {
            echo json_encode([]);
            exit;
        }
        
        $key = preg_replace('/[^a-zA-Z0-9_]/', '_', $key);
        $filePath = $storageDir . '/' . $key . '.json';
        
        if (file_exists($filePath)) {
            echo file_get_contents($filePath);
        } else {
            echo json_encode([]);
        }
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}
?>
