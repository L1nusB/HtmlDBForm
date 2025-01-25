<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// data.php
require_once 'db_config.php';

try {
    // Get database connection
    $conn = Database::getConnection();

    // Query to fetch data from the view
    $query = "SELECT pk_RPA_Prozesse, Prozessname FROM USEAP_RPA_Prozesse";
    $result = sqlsrv_query($conn, $query);

    if (!$result) {
        throw new Exception("Query failed: " . print_r(sqlsrv_errors(), true));
    }

    $data = array();
    $prozessnameSet = array();
    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        $data[$row['Prozessname']] = $row['pk_RPA_Prozesse'];
        $prozessnameSet[] = $row['Prozessname'];
    }

    sqlsrv_free_stmt($result);

    // Pass the mapping and set to JavaScript
    echo "<script>
            const processMapping = " . json_encode($data) . ";
            const processNames = " . json_encode(array_values(array_unique($prozessnameSet))) . ";
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
