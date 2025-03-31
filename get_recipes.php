<?php
header('Content-Type: application/json');

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the database connection
require_once 'db.php';

// Create response array
$response = [
    'success' => false,
    'message' => '',
    'data' => []
];

try {
    // Verify connection
    if (!$conn) {
        throw new Exception("Database connection failed: " . mysqli_connect_error());
    }

    // Get number of random recipes requested (max 20)
    $count = isset($_GET['random']) ? min((int)$_GET['random'], 20) : 5;

    // Check if recipes table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'recipes'");
    if (!$tableCheck || $tableCheck->num_rows === 0) {
        throw new Exception("Recipes table doesn't exist in database");
    }

    // Query for random recipes
    $query = "
        SELECT 
            r.id,
            r.title,
            r.steps,
            r.benefits,
            COALESCE(r.contents, 'Not provided') AS contents,
            r.image,
            r.video,
            COALESCE(u.username, 'Unknown') AS uploaded_by
        FROM recipes r
        LEFT JOIN users u ON r.uploaded_by = u.id
        WHERE (r.approved = 1 OR r.approved IS NULL)
        ORDER BY RAND()
        LIMIT ?
    ";
    
    // Prepare statement
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    // Bind parameter
    $stmt->bind_param("i", $count);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    // Get results
    $result = $stmt->get_result();
    if (!$result) {
        throw new Exception("Get result failed: " . $conn->error);
    }
    
    $recipes = $result->fetch_all(MYSQLI_ASSOC);
    
    if (empty($recipes)) {
        $response['message'] = 'No approved recipes found in database';
    } else {
        // Process media paths
        foreach ($recipes as &$recipe) {
            // Handle image paths
            $recipe['image'] = !empty($recipe['image']) ? 'uploads/' . ltrim($recipe['image'], '/') : null;
            if ($recipe['image'] && !file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $recipe['image'])) {
                $recipe['image'] = null;
            }
            
            // Handle video paths
            $recipe['video'] = !empty($recipe['video']) ? 'uploads/' . ltrim($recipe['video'], '/') : null;
            if ($recipe['video'] && !file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $recipe['video'])) {
                $recipe['video'] = null;
            }
            
            // Decode HTML entities
            foreach (['title', 'steps', 'benefits', 'contents'] as $field) {
                $recipe[$field] = html_entity_decode($recipe[$field] ?? '', ENT_QUOTES, 'UTF-8');
            }
        }
        unset($recipe);
        
        $response = [
            'success' => true,
            'data' => $recipes,
            'message' => 'Recipes retrieved successfully',
            'count' => count($recipes)
        ];
    }
    
    $stmt->close();
} catch (Exception $e) {
    http_response_code(500);
    $response['message'] = 'Error: ' . $e->getMessage();
    error_log("Recipe API Error: " . $e->getMessage() . " | Line: " . $e->getLine());
}

$conn->close();

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);