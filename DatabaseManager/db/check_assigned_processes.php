<?php
header('Content-Type: application/json');
require_once 'db_config.php';

try {
    $conn = Database::getConnection();
    
    $data = json_decode(file_get_contents('php://input'));

    if (!isset($data->id)) {
        throw new Exception("Missing ID parameter");
    }

    $query = "SELECT DISTINCT Prozessname 
              FROM USEAP_RPA_ViewProzessUebersicht 
              WHERE fk_RPA_Bankenuebersicht = ?
              ORDER BY Prozessname";
    
    $stmt = sqlsrv_query($conn, $query, array($data->id));
    
    if ($stmt === false) {
        throw new Exception("Query failed: " . print_r(sqlsrv_errors(), true));
    }

    $processes = array();
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $processes[] = $row['Prozessname'];
    }

    echo json_encode([
        "hasProcesses" => !empty($processes),
        "processes" => $processes
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
} finally {
    Database::closeConnection();
}
