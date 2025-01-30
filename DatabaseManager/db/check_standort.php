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
    $query = "SELECT pk_RPA_Standort, Standort_Kuerzel FROM USEAP_RPA_Standort";
    $result = sqlsrv_query($conn, $query);
    if (!$result) {
        throw new Exception("Query failed: " . print_r(sqlsrv_errors(), true));
    }

    $locationData = array();
    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        $locationData[] = $row;
    }
    $locationMapping = [];
    foreach ($locationData as $row) {
        $locationMapping[$row['pk_RPA_Standort']] = $row['Standort_Kuerzel'];
    }
    sqlsrv_free_stmt($result);

    // Pass the mapping to JavaScript (e.g., as JSON)
    echo "<script>
            let locationMapping = " . json_encode($locationMapping) . ";
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
