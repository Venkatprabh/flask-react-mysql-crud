<?php
// Load environment variables
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

// Include configurations
require_once __DIR__ . '/config/cors.php';
require_once __DIR__ . '/config/database.php';

// Set content type
header('Content-Type: application/json');

// Get request info
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/api', '', $path); // Remove /api prefix if present

// Route the request
$response = ['status' => 'error', 'message' => 'Endpoint not found'];
$statusCode = 404;

if ($path === '/users' || strpos($path, '/users/') === 0) {
    require_once __DIR__ . '/v1/users.php';
} elseif ($path === '/products' || strpos($path, '/products/') === 0) {
    require_once __DIR__ . '/v1/products.php';
} else {
    // Home/health endpoint
    if ($path === '/' || $path === '') {
        $response = [
            'status' => 'success',
            'message' => 'PHP API is running',
            'endpoints' => [
                '/users' => 'User management',
                '/products' => 'Product management'
            ]
        ];
        $statusCode = 200;
    }
    
    http_response_code($statusCode);
    echo json_encode($response);
}
?>