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

    // Query to fetch data from the view
    $query = "
        WITH LocationCounts AS (
            SELECT 
                fk_RPA_Bankenuebersicht,
                COUNT(DISTINCT fk_RPA_Standort) as unique_locations_count,
                MAX(fk_RPA_Standort) as sample_location
            FROM 
                USEAP_RPA_ViewProzessUebersicht
            GROUP BY 
                fk_RPA_Bankenuebersicht
        )
        SELECT 
            fk_RPA_Bankenuebersicht,
            CASE 
                WHEN unique_locations_count = 1 THEN Cast(1 As Bit)
                ELSE Cast(0 As Bit)
            END as all_locations_same,
            CASE 
                WHEN unique_locations_count = 1 THEN sample_location
                ELSE NULL
            END as location_if_same,
            unique_locations_count as number_of_different_locations
        FROM 
            LocationCounts
    ";
    $result = sqlsrv_query($conn, $query);

    if (!$result) {
        throw new Exception("Query failed: " . print_r(sqlsrv_errors(), true));
    }

    $data = array();
    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        $data[] = $row;
    }

    $mapping = [];
    foreach ($data as $row) {
        $fk = $row['fk_RPA_Bankenuebersicht'];
        if ($row['all_locations_same'] == true) {
            $key = $row['location_if_same'];
            $mapping[$fk] = $locationMapping[$key] ?? "unknown"; // Use 'unknown' if the key doesn't exist
        } else {
            $mapping[$fk] = "mixed";
        }
    }
    sqlsrv_free_stmt($result);

    // Pass the mapping to JavaScript (e.g., as JSON)
    echo "<script>
            const locationAssignment = " . json_encode($mapping) . ";
            const locationMapping = " . json_encode($locationMapping) . ";
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
