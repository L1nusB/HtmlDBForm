<?php
header('Content-Type: application/json');
require_once 'db_config.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $processName = $data['processName'];

    $conn = Database::getConnection();
    
    $query = "SELECT COUNT(*) as count FROM USEAP_RPA_Prozesse WHERE Prozessname = ?";
    $params = array($processName);
    
    $result = sqlsrv_query($conn, $query, $params);
    
    if ($result === false) {
        throw new Exception("Error checking for duplicates: " . print_r(sqlsrv_errors(), true));
    }

    $row = sqlsrv_fetch_array($result);
    $exists = $row['count'] > 0;

    echo json_encode([
        'exists' => $exists,
        'message' => $exists ? 'Process name already exists' : 'Process name is available'
    ]);

    sqlsrv_free_stmt($result);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    Database::closeConnection();
}
