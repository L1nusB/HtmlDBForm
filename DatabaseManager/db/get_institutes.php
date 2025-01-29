<?php
header('Content-Type: application/json');
require_once 'db_config.php';

try {
    $conn = Database::getConnection();
    
    $query = "SELECT pk_RPA_Bankenuebersicht, RZBK, Name FROM USEAP_RPA_Bankenuebersicht ORDER BY RZBK ASC";
    $result = sqlsrv_query($conn, $query);

    if (!$result) {
        throw new Exception("Query failed: " . print_r(sqlsrv_errors(), true));
    }

    $data = array();
    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        $data[] = $row;
    }

    echo json_encode($data);
    sqlsrv_free_stmt($result);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    Database::closeConnection();
}
