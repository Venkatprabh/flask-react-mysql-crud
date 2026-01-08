<?php
// Include database
$db = Database::getInstance();

// Check API key
$apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';
if ($apiKey !== ($_ENV['API_KEY'] ?? '')) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Invalid API key']);
    exit();
}

// Parse URL for ID
$pathParts = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
$userId = isset($pathParts[2]) ? intval($pathParts[2]) : null;

// Get request data
$input = json_decode(file_get_contents('php://input'), true) ?? [];

// Handle different HTTP methods
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if ($userId) {
            // Get single user
            try {
                $stmt = $db->query("SELECT id, username, email, created_at FROM users WHERE id = ?", [$userId]);
                $user = $stmt->fetch();
                
                if ($user) {
                    echo json_encode(['status' => 'success', 'data' => $user]);
                } else {
                    http_response_code(404);
                    echo json_encode(['status' => 'error', 'message' => 'User not found']);
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Database error']);
            }
        } else {
            // Get all users
            try {
                $page = $_GET['page'] ?? 1;
                $limit = $_GET['limit'] ?? 10;
                $offset = ($page - 1) * $limit;
                
                $stmt = $db->query("SELECT id, username, email, created_at FROM users LIMIT ? OFFSET ?", [$limit, $offset]);
                $users = $stmt->fetchAll();
                
                // Get total count
                $countStmt = $db->query("SELECT COUNT(*) as total FROM users");
                $total = $countStmt->fetch()['total'];
                
                echo json_encode([
                    'status' => 'success',
                    'data' => $users,
                    'pagination' => [
                        'page' => intval($page),
                        'limit' => intval($limit),
                        'total' => intval($total),
                        'pages' => ceil($total / $limit)
                    ]
                ]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Failed to fetch users']);
            }
        }
        break;
        
    case 'POST':
        // Create new user
        if (!isset($input['username']) || !isset($input['email']) || !isset($input['password'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
            break;
        }
        
        try {
            // Check if user exists
            $checkStmt = $db->query("SELECT id FROM users WHERE email = ? OR username = ?", 
                [$input['email'], $input['username']]);
            
            if ($checkStmt->fetch()) {
                http_response_code(409);
                echo json_encode(['status' => 'error', 'message' => 'User already exists']);
                break;
            }
            
            // Create user
            $hashedPassword = password_hash($input['password'], PASSWORD_DEFAULT);
            $db->query(
                "INSERT INTO users (username, email, password_hash, created_at) VALUES (?, ?, ?, NOW())",
                [$input['username'], $input['email'], $hashedPassword]
            );
            
            $userId = $db->lastInsertId();
            $userStmt = $db->query("SELECT id, username, email, created_at FROM users WHERE id = ?", [$userId]);
            $newUser = $userStmt->fetch();
            
            http_response_code(201);
            echo json_encode([
                'status' => 'success',
                'message' => 'User created successfully',
                'data' => $newUser
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to create user']);
        }
        break;
        
    case 'PUT':
        // Update user
        if (!$userId) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'User ID required']);
            break;
        }
        
        try {
            $updates = [];
            $params = [];
            
            if (isset($input['username'])) {
                $updates[] = 'username = ?';
                $params[] = $input['username'];
            }
            if (isset($input['email'])) {
                $updates[] = 'email = ?';
                $params[] = $input['email'];
            }
            
            if (empty($updates)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'No fields to update']);
                break;
            }
            
            $params[] = $userId;
            $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
            $db->query($sql, $params);
            
            echo json_encode(['status' => 'success', 'message' => 'User updated successfully']);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to update user']);
        }
        break;
        
    case 'DELETE':
        // Delete user
        if (!$userId) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'User ID required']);
            break;
        }
        
        try {
            $db->query("DELETE FROM users WHERE id = ?", [$userId]);
            echo json_encode(['status' => 'success', 'message' => 'User deleted successfully']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete user']);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}
?>