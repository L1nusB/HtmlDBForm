<?php
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../assignment_operations.php';

try {
    // Check if it's a DELETE request
    if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
        throw new Exception('Only PUT requests are allowed');
    }

    // Get RZBK from URL parameter
    $rzbk = $_GET['rzbk'] ?? null;
    
    if ($rzbk === null) {
        throw new Exception('No RZBK provided');
    }

    $data = null;

    // Delete the process using the operations class
    $result = AssignmentOperations::updateAssignment($rzbk, $data);
    
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