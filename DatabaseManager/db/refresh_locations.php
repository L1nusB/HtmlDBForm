<?php
header('Content-Type: application/json');
require_once 'db_config.php';

try {
    $conn = Database::getConnection();
    
    $query = "SELECT pk_RPA_Standort, Standort_Kuerzel FROM USEAP_RPA_Standort";
    $result = sqlsrv_query($conn, $query);

    if ($result === false) {
        throw new Exception("Query failed: " . print_r(sqlsrv_errors(), true));
    }

    $locations = array();
    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        $locations[$row['pk_RPA_Standort']] = $row['Standort_Kuerzel'];
    }

    echo json_encode([
        "success" => true,
        "locations" => $locations
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
} finally {
    Database::closeConnection();
}
