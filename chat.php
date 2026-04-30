<?php
require_once 'config.php';

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('X-Accel-Buffering: no');

$prompt = isset($_GET['prompt']) ? $_GET['prompt'] : '';
$system = isset($_GET['system']) ? $_GET['system'] : '';

if (empty($prompt)) {
    echo "data: " . json_encode(['error' => 'No prompt provided']) . "\n\n";
    exit;
}

$data = [
    'model' => OLLAMA_MODEL,
    'prompt' => $prompt,
    'system' => $system,
    'stream' => true
];

$ch = curl_init(OLLAMA_API_URL . '/generate');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($ch, $data) {
    $lines = explode("\n", $data);
    foreach ($lines as $line) {
        $line = trim($line);
        if (!empty($line)) {
            $json = json_decode($line, true);
            if ($json) {
                echo "data: " . json_encode([
                    'response' => isset($json['response']) ? $json['response'] : '',
                    'done' => isset($json['done']) ? $json['done'] : false
                ]) . "\n\n";
                ob_flush();
                flush();
            }
        }
    }
    return strlen($data);
});

curl_exec($ch);

if (curl_errno($ch)) {
    echo "data: " . json_encode(['error' => curl_error($ch)]) . "\n\n";
}

curl_close($ch);
?>
