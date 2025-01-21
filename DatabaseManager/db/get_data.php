<?php
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// data.php
require_once 'db_config.php';

try {
    // Get database connection
    $conn = Database::getConnection();

    // Query to fetch data from the view
    $query = "
        SELECT 
            RZBK, 
            Name, 
            Prozessname, 
            ProduktionsStart,
            fk_RPA_Bankenuebersicht,
            fk_RPA_Standort
        FROM 
            USEAP_RPA_ViewProzessUebersicht
        ORDER BY 
            RZBK ASC
    ";
    $result = sqlsrv_query($conn, $query);

    if (!$result) {
        throw new Exception("Query failed: " . print_r(sqlsrv_errors(), true));
    }

    $data = array();
    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        $row['ProduktionsStart'] = $row['ProduktionsStart']->format('d.m.Y');
        $data[] = $row;
    }

    echo json_encode($data);
    sqlsrv_free_stmt($result);

} catch (Exception $e) {
    http_response_code(500);
    echo 'Fehler';
    echo 'Error: ' . $e->getMessage();
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    // Close the connection
    Database::closeConnection();
}
?>