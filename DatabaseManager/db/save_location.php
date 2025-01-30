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

    $query = "INSERT INTO USEAP_RPA_Standort (Standort, Standort_Kuerzel) VALUES (?, ?)";
    $params = array($data->location, $data->abbreviation);
    
    $stmt = sqlsrv_query($conn, $query, $params);
    
    if ($stmt === false) {
        throw new Exception("Error saving location: " . print_r(sqlsrv_errors(), true));
    }

    echo json_encode([
        "success" => true,
        "status" => "success",
        "message" => "Location added successfully"
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
