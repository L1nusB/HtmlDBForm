<?php
header('Content-Type: application/json');
require_once 'db_config.php';

try {
    $conn = Database::getConnection();
    
    $data = json_decode(file_get_contents('php://input'));

    if (!isset($data->id) || !isset($data->location) || !isset($data->abbreviation)) {
        throw new Exception("Missing required fields");
    }

    // Check if the new abbreviation is already used by another location
    $checkQuery = "SELECT Standort FROM USEAP_RPA_Standort WHERE Standort_Kuerzel = ? AND pk_RPA_Standort != ?";
    $stmt = sqlsrv_query($conn, $checkQuery, array($data->abbreviation, $data->id));
    
    if ($stmt === false) {
        throw new Exception("Error checking abbreviation: " . print_r(sqlsrv_errors(), true));
    }

    if (sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        echo json_encode([
            "success" => false,
            "status" => "error",
            "duplicate" => true,
            "message" => "This abbreviation is already in use by another location"
        ]);
        exit;
    }

    // Update the location
    $updateQuery = "UPDATE USEAP_RPA_Standort SET Standort = ?, Standort_Kuerzel = ? WHERE pk_RPA_Standort = ?";
    $params = array($data->location, $data->abbreviation, $data->id);
    
    $stmt = sqlsrv_query($conn, $updateQuery, $params);
    
    if ($stmt === false) {
        throw new Exception("Error updating location: " . print_r(sqlsrv_errors(), true));
    }

    echo json_encode([
        "success" => true,
        "status" => "success",
        "message" => "Location updated successfully"
    ]);

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
