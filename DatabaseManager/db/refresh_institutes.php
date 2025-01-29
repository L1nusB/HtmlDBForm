<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db_config.php';

header('Content-Type: application/json');

try {
    $conn = Database::getConnection();
    
    $query = "SELECT pk_RPA_Bankenuebersicht, RZBK, Name FROM USEAP_RPA_Bankenuebersicht ORDER BY RZBK ASC";
    $result = sqlsrv_query($conn, $query);
    
    if (!$result) {
        throw new Exception("Query failed: " . print_r(sqlsrv_errors(), true));
    }

    $instituteMapping = [];
    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        $pk = $row['pk_RPA_Bankenuebersicht'];
        $instituteMapping[$pk] = [
            'RZBK' => $row['RZBK'],
            'Name' => $row['Name'],
        ];
    }
    
    echo json_encode([
        'success' => true,
        'institutes' => $instituteMapping
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    Database::closeConnection();
}
