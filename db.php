<?php
$serverName = "DESKTOP-38K7GFG";
$connectionInfo = array(
    "Database" => "Serviscope",
    "UID" => "your_username",
    "PWD" => "your_password"
);

try {
    $conn = sqlsrv_connect($serverName, $connectionInfo);
    if (!$conn) {
        throw new Exception("Connection failed: " . print_r(sqlsrv_errors(), true));
    }
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}
?>