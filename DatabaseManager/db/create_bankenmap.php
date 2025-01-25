<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// data.php
require_once 'db_config.php';

try {
    // Get database connection
    $conn = Database::getConnection();

    // Query to fetch location (Standort) data
    $query = "SELECT pk_RPA_Bankenuebersicht, RZBK, Name FROM USEAP_RPA_Bankenuebersicht";
    $result = sqlsrv_query($conn, $query);
    if (!$result) {
        throw new Exception("Query failed: " . print_r(sqlsrv_errors(), true));
    }

    $instituteData = array();
    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        $instituteData[] = $row;
    }
    $instituteMapping = [];
    foreach ($instituteData as $row) {
        $pk = $row['pk_RPA_Bankenuebersicht'];
        $instituteMapping[$pk] = [
            'RZBK' => $row['RZBK'],
            'Name' => $row['Name'],
        ];
    }
    sqlsrv_free_stmt($result);

    // Pass the mapping to JavaScript
    echo "<script>
            const instituteMapping = " . json_encode($instituteMapping) . ";
        </script>";
} catch (Exception $e) {
    http_response_code(500);
    echo 'Fehler';
    echo 'Error: ' . $e->getMessage();
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    // Close the connection
    Database::closeConnection();
}
