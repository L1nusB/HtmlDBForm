<?php
header('Content-Type: application/json');
require_once 'db_config.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $processName = $data['processName'];

    $conn = Database::getConnection();
    
    $query = "INSERT INTO USEAP_RPA_Prozesse (Prozessname) VALUES (?)";
    $params = array($processName);
    
    $result = sqlsrv_query($conn, $query, $params);
    
    if ($result === false) {
        throw new Exception("Error saving process: " . print_r(sqlsrv_errors(), true));
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Process added successfully'
    ]);

    sqlsrv_free_stmt($result);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} finally {
    Database::closeConnection();
}
