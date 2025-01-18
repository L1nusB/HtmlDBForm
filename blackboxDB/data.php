<?php
/*
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// data.php
require_once 'db.php';

try {
    echo 'Start';
    // Get database connection
    $conn = Database::getConnection();

    // Query to fetch data from the view
    $query = "
        SELECT 
            b.RZBK, 
            b.Name, 
            p.Prozessname, 
            z.ProduktionsStart
        FROM 
            USEAP_RPA_Bankenuebersicht b
        LEFT JOIN 
            USEAP_RPA_Prozess_Zuweisung z ON b.RZBK = z.RZBK
        LEFT JOIN 
            USEAP_RPA_Prozesse p ON z.ProzessID = p.ProzessID
        ORDER BY 
            b.RZBK, p.Prozessname
    ";
    $result = sqlsrv_query($conn, $query);

    if (!$result) {
        throw new Exception("Query failed: " . print_r(sqlsrv_errors(), true));
    }

    $data = array();
    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        // Convert date to ISO format for consistent handling
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
*/
// data.php
include 'db.php';

header('Content-Type: application/json');

$query = "
    SELECT 
        RZBK, 
        Name, 
        Prozessname, 
        ProduktionsStart
    FROM 
        USEAP_RPA_ViewProzessUebersicht
";

$result = sqlsrv_query($conn, $query);
$data = [];

if ($result) {
    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        $data[] = $row;
    }
}
echo json_encode($data);
// sqlsrv_free_stmt($result);
sqlsrv_close($conn);
?>