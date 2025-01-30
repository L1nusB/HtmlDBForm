<?php
header('Content-Type: application/json');
require_once 'db_config.php';

try {
    $conn = Database::getConnection();
    
    $data = json_decode(file_get_contents('php://input'));
    
    if (!isset($data->id)) {
        throw new Exception("Missing ID parameter");
    }

    // Check if process has any assignments
    $query = "SELECT COUNT(*) as count FROM USEAP_RPA_Prozess_Zuweisung WHERE fk_RPA_Prozesse = ?";
    $params = array($data->id);
    
    $result = sqlsrv_query($conn, $query, $params);
    
    if ($result === false) {
        throw new Exception("Error checking assignments: " . print_r(sqlsrv_errors(), true));
    }

    $row = sqlsrv_fetch_array($result);
    $hasAssignments = $row['count'] > 0;

    echo json_encode([
        "hasAssignments" => $hasAssignments
    ]);

    sqlsrv_free_stmt($result);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
} finally {
    Database::closeConnection();
}
