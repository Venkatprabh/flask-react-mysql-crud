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
$productId = isset($pathParts[2]) ? intval($pathParts[2]) : null;

// Get request data
$input = json_decode(file_get_contents('php://input'), true) ?? [];

// Handle different HTTP methods
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if ($productId) {
            // Get single product
            try {
                $stmt = $db->query("SELECT * FROM products WHERE id = ?", [$productId]);
                $product = $stmt->fetch();
                
                if ($product) {
                    echo json_encode(['status' => 'success', 'data' => $product]);
                } else {
                    http_response_code(404);
                    echo json_encode(['status' => 'error', 'message' => 'Product not found']);
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Database error']);
            }
        } else {
            // Get all products with filters
            try {
                $filters = [];
                $params = [];
                
                // Category filter
                if (isset($_GET['category'])) {
                    $filters[] = 'category = ?';
                    $params[] = $_GET['category'];
                }
                
                // Price range filter
                if (isset($_GET['min_price'])) {
                    $filters[] = 'price >= ?';
                    $params[] = floatval($_GET['min_price']);
                }
                if (isset($_GET['max_price'])) {
                    $filters[] = 'price <= ?';
                    $params[] = floatval($_GET['max_price']);
                }
                
                // Search filter
                if (isset($_GET['search'])) {
                    $filters[] = '(name LIKE ? OR description LIKE ?)';
                    $searchTerm = '%' . $_GET['search'] . '%';
                    $params[] = $searchTerm;
                    $params[] = $searchTerm;
                }
                
                // Build WHERE clause
                $whereClause = '';
                if (!empty($filters)) {
                    $whereClause = 'WHERE ' . implode(' AND ', $filters);
                }
                
                // Pagination
                $page = $_GET['page'] ?? 1;
                $limit = $_GET['limit'] ?? 20;
                $offset = ($page - 1) * $limit;
                
                // Sorting
                $sortBy = $_GET['sort_by'] ?? 'created_at';
                $sortOrder = $_GET['sort_order'] ?? 'DESC';
                $allowedSort = ['name', 'price', 'created_at', 'stock_quantity'];
                $sortBy = in_array($sortBy, $allowedSort) ? $sortBy : 'created_at';
                $sortOrder = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';
                
                // Query products
                $sql = "SELECT * FROM products $whereClause 
                        ORDER BY $sortBy $sortOrder 
                        LIMIT ? OFFSET ?";
                $params[] = $limit;
                $params[] = $offset;
                
                $stmt = $db->query($sql, $params);
                $products = $stmt->fetchAll();
                
                // Get total count
                $countSql = "SELECT COUNT(*) as total FROM products $whereClause";
                $countStmt = $db->query($countSql, array_slice($params, 0, -2));
                $total = $countStmt->fetch()['total'];
                
                echo json_encode([
                    'status' => 'success',
                    'data' => $products,
                    'pagination' => [
                        'page' => intval($page),
                        'limit' => intval($limit),
                        'total' => intval($total),
                        'pages' => ceil($total / $limit)
                    ],
                    'filters' => [
                        'category' => $_GET['category'] ?? null,
                        'min_price' => $_GET['min_price'] ?? null,
                        'max_price' => $_GET['max_price'] ?? null,
                        'search' => $_GET['search'] ?? null
                    ]
                ]);
                
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Failed to fetch products']);
            }
        }
        break;
        
    case 'POST':
        // Create new product
        $required = ['name', 'price'];
        foreach ($required as $field) {
            if (!isset($input[$field])) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => "Missing required field: $field"]);
                exit();
            }
        }
        
        try {
            $db->query(
                "INSERT INTO products (name, description, price, category, stock_quantity, created_at) 
                 VALUES (?, ?, ?, ?, ?, NOW())",
                [
                    $input['name'],
                    $input['description'] ?? '',
                    floatval($input['price']),
                    $input['category'] ?? 'Uncategorized',
                    intval($input['stock_quantity'] ?? 0)
                ]
            );
            
            $productId = $db->lastInsertId();
            $productStmt = $db->query("SELECT * FROM products WHERE id = ?", [$productId]);
            $newProduct = $productStmt->fetch();
            
            http_response_code(201);
            echo json_encode([
                'status' => 'success',
                'message' => 'Product created successfully',
                'data' => $newProduct
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to create product']);
        }
        break;
        
    case 'PUT':
        // Update product
        if (!$productId) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Product ID required']);
            break;
        }
        
        try {
            $updates = [];
            $params = [];
            
            $fields = ['name', 'description', 'price', 'category', 'stock_quantity'];
            foreach ($fields as $field) {
                if (isset($input[$field])) {
                    $updates[] = "$field = ?";
                    if ($field === 'price') {
                        $params[] = floatval($input[$field]);
                    } elseif ($field === 'stock_quantity') {
                        $params[] = intval($input[$field]);
                    } else {
                        $params[] = $input[$field];
                    }
                }
            }
            
            if (empty($updates)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'No fields to update']);
                break;
            }
            
            $params[] = $productId;
            $sql = "UPDATE products SET " . implode(', ', $updates) . " WHERE id = ?";
            $db->query($sql, $params);
            
            echo json_encode(['status' => 'success', 'message' => 'Product updated successfully']);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to update product']);
        }
        break;
        
    case 'DELETE':
        // Delete product
        if (!$productId) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Product ID required']);
            break;
        }
        
        try {
            $db->query("DELETE FROM products WHERE id = ?", [$productId]);
            echo json_encode(['status' => 'success', 'message' => 'Product deleted successfully']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete product']);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}
?>