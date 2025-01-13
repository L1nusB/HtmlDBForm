<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if PHP SQL Server extension is loaded
echo "<h2>PHP Configuration Check:</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "SQL Server extension loaded: " . (extension_loaded('sqlsrv') ? 'Yes' : 'No') . "<br><br>";

// Database configuration
$serverName = "DESKTOP-38K7GFG"; // Common default for SQL Server Express
$connectionInfo = array(
    "Database" => "Serviscope",
    "TrustServerCertificate" => true
);

// Attempt connection
if (function_exists('sqlsrv_connect')) {
    $conn = sqlsrv_connect($serverName, $connectionInfo);
} else {
    $conn = false;
    echo "SQL Server functions not available. Please check PHP SQLServer extension installation.<br>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Local Database Connection Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            line-height: 1.6;
        }
        .status {
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .success {
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
        }
        .error {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }
        .debug {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 20px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>Local Database Connection Test</h1>
    <div class="status <?php echo ($conn ? 'success' : 'error'); ?>">
        <?php
        if ($conn) {
            echo "Connection established successfully!";
        } else {
            echo "Connection failed.<br>";
            if (function_exists('sqlsrv_errors')) {
                $errors = sqlsrv_errors();
                if ($errors) {
                    echo "Errors:<br>";
                    foreach ($errors as $error) {
                        echo "SQLSTATE: " . $error['SQLSTATE'] . "<br>";
                        echo "Code: " . $error['code'] . "<br>";
                        echo "Message: " . $error['message'] . "<br>";
                    }
                }
            }
        }
        ?>
    </div>

    <div class="debug">
        <h3>Debug Information:</h3>
        <p>Server Name: <?php echo $serverName; ?></p>
        <p>Database Name: <?php echo $connectionInfo['Database']; ?></p>
        <p>PHP.ini location: <?php echo php_ini_loaded_file(); ?></p>
        <p>Extension directory: <?php echo php_ini_scanned_files(); ?></p>
    </div>
</body>
</html>