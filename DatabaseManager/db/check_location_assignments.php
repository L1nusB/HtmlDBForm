<?php
header('Content-Type: application/json');
require_once 'db_config.php';

try {
    $conn = Database::getConnection();
    
    $data = json_decode(file_get_contents('php://input'));

    if (!isset($data->id)) {
        throw new Exception("Missing ID parameter");
    }

    // Check if there are any process assignments for this location
    $query = "SELECT COUNT(*) as count FROM USEAP_RPA_Prozess_Zuweisung WHERE fk_RPA_Standort = ?";
    $stmt = sqlsrv_query($conn, $query, array($data->id));
    
    if ($stmt === false) {
        throw new Exception("Query failed: " . print_r(sqlsrv_errors(), true));
    }

    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    
    echo json_encode([
        "hasAssignments" => $row['count'] > 0
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
} finally {
    Database::closeConnection();
}
