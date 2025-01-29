<?php
header('Content-Type: application/json');
require_once 'db_config.php';

try {
    $conn = Database::getConnection();
    
    // Get JSON data
    $data = json_decode(file_get_contents('php://input'));

    if (!isset($data->id)) {
        throw new Exception("Missing ID parameter");
    }

    $query = "DELETE FROM USEAP_RPA_Bankenuebersicht WHERE pk_RPA_Bankenuebersicht = ?";
    $stmt = sqlsrv_prepare($conn, $query, array(&$data->id));

    if (!$stmt) {
        throw new Exception("Failed to prepare statement");
    }

    if (!sqlsrv_execute($stmt)) {
        throw new Exception("Failed to execute statement");
    }

    echo json_encode(array("status" => "success"));
} catch (Exception $e) {
    echo json_encode(array("status" => "error", "message" => $e->getMessage()));
}
?>
