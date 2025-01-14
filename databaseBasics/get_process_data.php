<?php
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db_config.php';

try {
    // Get database connection
    $conn = Database::getConnection();

    // Query to fetch data from the view
    $sql = "SELECT RZBK, Name, ProduktionsStart, Prozessname, Standort_Kuerzel FROM USEAP_RPA_ViewProzessUebersicht ORDER BY ProduktionsStart DESC";
    $result = sqlsrv_query($conn, $sql);

    if (!$result) {
        throw new Exception("Query failed: " . print_r(sqlsrv_errors(), true));
    }

    $data = array();
    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        // Convert date to ISO format for consistent handling
        $row['ProduktionsStart'] = $row['ProduktionsStart']->format('Y-m-d');
        $data[] = $row;
    }

    echo json_encode($data);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    // Close the connection
    Database::closeConnection();
}
?>