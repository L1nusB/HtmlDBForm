<?php
header('Content-Type: application/json');
require_once 'db_config.php';

try {
    $conn = Database::getConnection();
    
    // Get JSON data
    $data = json_decode(file_get_contents('php://input'));

    if (!isset($data->location) || !isset($data->abbreviation)) {
        throw new Exception("Missing required fields");
    }

    // First check for abbreviation duplicate (not allowed)
    $query = "SELECT Standort FROM USEAP_RPA_Standort WHERE Standort_Kuerzel = ?";
    $stmt = sqlsrv_query($conn, $query, array($data->abbreviation));
    
    if ($stmt === false) {
        throw new Exception("Query failed: " . print_r(sqlsrv_errors(), true));
    }

    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    if ($row) {
        echo json_encode([
            "exists" => true,
            "type" => "abbreviation",
            "existingLocation" => $row['Standort']
        ]);
        exit;
    }

    // Then check for name duplicate (needs confirmation)
    $query = "SELECT Standort_Kuerzel FROM USEAP_RPA_Standort WHERE Standort = ?";
    $stmt = sqlsrv_query($conn, $query, array($data->location));
    
    if ($stmt === false) {
        throw new Exception("Query failed: " . print_r(sqlsrv_errors(), true));
    }

    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    if ($row) {
        echo json_encode([
            "exists" => true,
            "type" => "location",
            "existingAbbreviation" => $row['Standort_Kuerzel']
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
