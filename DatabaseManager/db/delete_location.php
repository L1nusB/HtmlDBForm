<?php
header('Content-Type: application/json');
require_once 'db_config.php';

try {
    $conn = Database::getConnection();
    
    $data = json_decode(file_get_contents('php://input'));

    if (!isset($data->id)) {
        throw new Exception("Missing ID parameter");
    }

    // Start transaction
    if (sqlsrv_begin_transaction($conn) === false) {
        throw new Exception("Could not begin transaction");
    }

    // First delete process assignments
    $deleteAssignments = "DELETE FROM USEAP_RPA_Prozess_Zuweisung WHERE fk_RPA_Standort = ?";
    $stmt = sqlsrv_prepare($conn, $deleteAssignments, array($data->id));

    if (!$stmt || !sqlsrv_execute($stmt)) {
        throw new Exception("Failed to delete process assignments");
    }

    // Then delete the location
    $deleteLocation = "DELETE FROM USEAP_RPA_Standort WHERE pk_RPA_Standort = ?";
    $stmt = sqlsrv_prepare($conn, $deleteLocation, array($data->id));

    if (!$stmt || !sqlsrv_execute($stmt)) {
        throw new Exception("Failed to delete location");
    }

    // Commit transaction
    sqlsrv_commit($conn);

    echo json_encode([
        "status" => "success",
        "success" => true,
        "message" => "Location " . $data->location . " deleted successfully"
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($conn) && sqlsrv_begin_transaction($conn) !== false) {
        sqlsrv_rollback($conn);
    }
    echo json_encode([
        "status" => "error", 
        "message" => $e->getMessage()
    ]);
} finally {
    Database::closeConnection();
}
