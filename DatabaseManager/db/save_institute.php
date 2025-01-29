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

    if (isset($data->id)) {
        // Update
        $query = "UPDATE USEAP_RPA_Bankenuebersicht SET RZBK = ?, Name = ? WHERE pk_RPA_Bankenuebersicht = ?";
        $params = array($data->RZBK, $data->Name, $data->id);
    } else {
        // Insert
        $query = "INSERT INTO USEAP_RPA_Bankenuebersicht (RZBK, Name) VALUES (?, ?)";
        $params = array($data->RZBK, $data->Name);
    }

    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt === false) {
        throw new Exception("Query failed: " . print_r(sqlsrv_errors(), true));
    }

    $message = isset($data->id) ? "Institute updated successfully" : "Institute added successfully";
    echo json_encode([
        "success" => true,
        "status" => "success",
        "message" => $message
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
