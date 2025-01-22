<?php
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../assignment_operations.php';

try {
    // Check if it's a POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST requests are allowed');
    }

    // Get JSON data from request body
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);

    if ($data === null) {
        throw new Exception('Invalid JSON data provided');
    }

    // Create the process using the operations class
    $result = AssignmentOperations::createAssignment($data);
    
    // Return success response
    echo json_encode($result);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>