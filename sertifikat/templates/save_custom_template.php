<?php
/**
 * Save Custom Template
 * Accepts JSON from editor/index.html and saves to database
 * 
 * POST /templates/save_custom_template.php
 * Content-Type: application/json
 * 
 * Request:
 * {
 *   "name": "Template Name",
 *   "canvas_json": { ... fabric.js canvas data ... }
 * }
 * 
 * Response:
 * {
 *   "success": true,
 *   "template_id": 1,
 *   "message": "Template saved successfully"
 * }
 */

header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Use POST.'
    ]);
    exit;
}

// Include database connection
require_once '../config/database.php';

try {
    // Get raw JSON input
    $input = file_get_contents('php://input');
    
    if (empty($input)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Request body is empty'
        ]);
        exit;
    }

    $data = json_decode($input, true);

    // Validate JSON parsing
    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid JSON: ' . json_last_error_msg()
        ]);
        exit;
    }

    // Validate required fields
    if (empty($data['name'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Template name is required'
        ]);
        exit;
    }

    if (empty($data['canvas_json'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Canvas JSON is required'
        ]);
        exit;
    }

    // Validate name (string, not too long)
    $name = trim($data['name']);
    if (strlen($name) > 255) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Template name is too long (max 255 characters)'
        ]);
        exit;
    }

    if (strlen($name) < 3) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Template name is too short (min 3 characters)'
        ]);
        exit;
    }

    // Validate and convert canvas_json to string
    $canvasJson = $data['canvas_json'];
    if (is_array($canvasJson) || is_object($canvasJson)) {
        $canvasJson = json_encode($canvasJson);
    }

    if (!is_string($canvasJson) || empty($canvasJson)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Canvas JSON must be a valid object or string'
        ]);
        exit;
    }

    // Additional validation: ensure it's valid JSON
    if (json_decode($canvasJson) === null && json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Canvas JSON is invalid'
        ]);
        exit;
    }

    // Insert into database using prepared statement
    $stmt = $pdo->prepare("
        INSERT INTO certificate_templates_custom (name, canvas_json)
        VALUES (?, ?)
    ");

    $stmt->execute([
        $name,
        $canvasJson
    ]);

    $templateId = $pdo->lastInsertId();

    // Return success response
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'template_id' => (int)$templateId,
        'message' => 'Template saved successfully',
        'name' => $name,
        'created_at' => date('Y-m-d H:i:s')
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
