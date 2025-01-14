<?php
// Enable error reporting for debugging
error_reporting(error_level: E_ALL);
ini_set(option: 'display_errors', value: 1);

// Database configuration
$serverName = "DESKTOP-38K7GFG";
$connectionInfo = array(
    "Database" => "Serviscope",
    "TrustServerCertificate" => true
);

// Establish connection
$conn = sqlsrv_connect($serverName,$connectionInfo);

// Query to fetch data from the view
$sql = "SELECT RZBK, Name, ProduktionsStart, Prozessname, Standort_Kuerzel FROM USEAP_RPA_ViewProzessUebersicht ORDER BY ProduktionsStart DESC";
$result = sqlsrv_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Process Overview</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            line-height: 1.6;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f0f0f0;
        }
        .error {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <h1>Process Overview</h1>

    <?php
    if (!$conn) {
        echo '<div class="error">Database connection failed:<br>';
        echo print_r(value: sqlsrv_errors(), return: true) . '</div>';
    } elseif (!$result) {
        echo '<div class="error">Query execution failed:<br>';
        echo print_r(value: sqlsrv_errors(), return: true) . '</div>';
    } else {
    ?>
        <table>
            <thead>
                <tr>
                    <th>RZBK</th>
                    <th>Name</th>
                    <th>ProduktionsStart</th>
                    <th>Prozessname</th>
                    <th>Standort</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars(string: $row['RZBK']) . "</td>";
                    echo "<td>" . htmlspecialchars(string: $row['Name']) . "</td>";
                    // Format the date nicely
                    echo "<td>" . ($row['ProduktionsStart'] ? $row['ProduktionsStart']->format('Y-m-d') : '') . "</td>";
                    echo "<td>" . htmlspecialchars(string: $row['Prozessname']) . "</td>";
                    echo "<td>" . htmlspecialchars(string: $row['Standort_Kuerzel']) . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    <?php
    }

    // Close the connection
    sqlsrv_close(conn: $conn);
    ?>
</body>
</html>