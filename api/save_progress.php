<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Get the JSON data from the request
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate the data
    if (!$data || !isset($data['frontend']) || !isset($data['backend'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid data format']);
        exit;
    }
    
    // Add timestamp
    $data['last_updated'] = date('Y-m-d H:i:s');
    $data['user_id'] = isset($data['user_id']) ? $data['user_id'] : 'default_user';
    
    // Save to JSON file
    $jsonFile = '../json/progress_tracking.json';
    $result = file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT));
    
    if ($result !== false) {
        echo json_encode([
            'success' => true,
            'message' => 'Progress saved successfully',
            'timestamp' => $data['last_updated']
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to save progress']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Return current progress data
    $jsonFile = '../json/progress_tracking.json';
    if (file_exists($jsonFile)) {
        $data = json_decode(file_get_contents($jsonFile), true);
        echo json_encode($data);
    } else {
        echo json_encode([
            'frontend' => [],
            'backend' => [],
            'data_science' => [],
            'last_updated' => '',
            'user_id' => 'default_user'
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?> 