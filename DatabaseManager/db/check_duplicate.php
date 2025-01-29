<?php
header('Content-Type: application/json');
require_once 'db_config.php';

try {
    $conn = Database::getConnection();
    
    // Get JSON data
    $data = json_decode(file_get_contents('php://input'));

    if (!isset($data->RZBK) || !isset($data->Name)) {
        throw new Exception("Missing required fields");
    }

    // First check for exact match
    $query = "SELECT COUNT(*) as count FROM USEAP_RPA_Bankenuebersicht WHERE RZBK = ? AND Name = ?";
    $stmt = sqlsrv_query($conn, $query, array($data->RZBK, $data->Name));
    
    if ($stmt === false) {
        throw new Exception("Query failed: " . print_r(sqlsrv_errors(), true));
    }

    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    if ($row['count'] > 0) {
        echo json_encode([
            "exists" => true,
            "exactMatch" => true
        ]);
        exit;
    }

    // Then check for RZBK duplicate with different name
    $query = "SELECT Name FROM USEAP_RPA_Bankenuebersicht WHERE RZBK = ?";
    $stmt = sqlsrv_query($conn, $query, array($data->RZBK));
    
    if ($stmt === false) {
        throw new Exception("Query failed: " . print_r(sqlsrv_errors(), true));
    }

    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    if ($row) {
        echo json_encode([
            "exists" => true,
            "exactMatch" => false,
            "existingName" => $row['Name']
        ]);
    } else {
        echo json_encode([
            "exists" => false
        ]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "status" => "error",
        "message" => $e->getMessage()
    ]);
} finally {
    Database::closeConnection();
}
