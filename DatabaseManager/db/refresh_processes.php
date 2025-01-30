<?php
header('Content-Type: application/json');
require_once 'db_config.php';

try {
    $conn = Database::getConnection();
    
    $query = "SELECT pk_RPA_Prozesse, Prozessname FROM USEAP_RPA_Prozesse";
    $result = sqlsrv_query($conn, $query);

    if (!$result) {
        throw new Exception("Query failed: " . print_r(sqlsrv_errors(), true));
    }

    $processMap = array();
    $processNamesList = array();
    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        $processMap[$row['Prozessname']] = $row['pk_RPA_Prozesse'];
        $processNamesList[] = $row['Prozessname'];
    }

    echo json_encode([
        'success' => true,
        'processes' => $processMap,
        'processNames' => array_values(array_unique($processNamesList))
    ]);

    sqlsrv_free_stmt($result);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    Database::closeConnection();
}
