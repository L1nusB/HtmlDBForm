<?php
header('Content-Type: application/json');
require_once 'db_config.php';

try {
    $conn = Database::getConnection();
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id']) || !isset($data['processName'])) {
        throw new Exception("Missing required parameters");
    }

    // Check for duplicates excluding current process
    $checkQuery = "SELECT COUNT(*) as count FROM USEAP_RPA_Prozesse WHERE Prozessname = ? AND pk_RPA_Prozesse != ?";
    $params = array($data['processName'], $data['id']);
    
    $result = sqlsrv_query($conn, $checkQuery, $params);
    
    if ($result === false) {
        throw new Exception("Error checking for duplicates: " . print_r(sqlsrv_errors(), true));
    }

    $row = sqlsrv_fetch_array($result);
    if ($row['count'] > 0) {
        echo json_encode([
            'status' => 'error',
            'duplicate' => true,
            'message' => 'Process name already exists'
        ]);
        return;
    }

    // Update process
    $updateQuery = "UPDATE USEAP_RPA_Prozesse SET Prozessname = ? WHERE pk_RPA_Prozesse = ?";
    $updateResult = sqlsrv_query($conn, $updateQuery, $params);

    if ($updateResult === false) {
        throw new Exception("Error updating process: " . print_r(sqlsrv_errors(), true));
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Process updated successfully'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} finally {
    Database::closeConnection();
}
