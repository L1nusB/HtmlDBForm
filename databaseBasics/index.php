<!-- Don't forget to run browser-sync start --config databaseBasics/browser-sync-config.js -->

<?php
// Enable error reporting for debugging
error_reporting(error_level: E_ALL);
ini_set(option: 'display_errors', value: 1);

// Database configuration
$serverName = "DESKTOP-38K7GFG";
$connectionInfo = array(
    "Database" => "Serviscope",
    "TrustServerCertificate" => true,
    "CharacterSet" => "UTF-8"  // Add UTF-8 character set
);

// Establish connection
$conn = sqlsrv_connect($serverName, $connectionInfo);

// Query to fetch data from the view
$sql = "SELECT RZBK, Name, ProduktionsStart, Prozessname, Standort_Kuerzel FROM USEAP_RPA_ViewProzessUebersicht ORDER BY ProduktionsStart DESC";
$result = sqlsrv_query($conn, $sql, [], array("Scrollable" => SQLSRV_CURSOR_STATIC));

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Process Overview</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/2.2.1/css/dataTables.bootstrap5.min.css" rel="stylesheet">

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
        <table id="processTable" class="display responsive nowrap table" style="width:100%">
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
                    echo "<td>" . htmlspecialchars(string: $row['RZBK'], flags: ENT_QUOTES, encoding: 'UTF-8') . "</td>";
                    echo "<td>" . htmlspecialchars(string: $row['Name'], flags: ENT_QUOTES, encoding: 'UTF-8') . "</td>";
                    // Format the date nicely
                    echo "<td>" . ($row['ProduktionsStart'] ? $row['ProduktionsStart']->format('d.m.Y') : '') . "</td>";
                    echo "<td>" . htmlspecialchars(string: $row['Prozessname'], flags: ENT_QUOTES, encoding: 'UTF-8') . "</td>";
                    echo "<td>" . htmlspecialchars(string: $row['Standort_Kuerzel'], flags: ENT_QUOTES, encoding: 'UTF-8') . "</td>";
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

    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.bootstrap5.min.js"></script>


    <script type="text/javascript">
        $(document).ready(function() {
            $('#processTable').DataTable({
                responsive: true,
                // language: {
                //     // Inline German translations
                //     "emptyTable": "Keine Daten verfügbar",
                //     "info": "_START_ bis _END_ von _TOTAL_ Einträgen",
                //     "infoEmpty": "0 bis 0 von 0 Einträgen",
                //     "infoFiltered": "(gefiltert von _MAX_ Einträgen)",
                //     "lengthMenu": "_MENU_ Einträge anzeigen",
                //     "loadingRecords": "Wird geladen...",
                //     "processing": "Bitte warten...",
                //     "search": "Suchen:",
                //     "zeroRecords": "Keine passenden Einträge gefunden",
                //     "paginate": {
                //         "first": "Erste",
                //         "last": "Letzte",
                //         "next": "Nächste",
                //         "previous": "Vorherige"
                //     }
                // },
                columnDefs: [
                    {
                        // Sort the date column correctly
                        targets: 2,
                        type: 'date-de'
                    }
                ],
                order: [[2, 'desc']], // Sort by date column by default
                pageLength: 25, // Show 25 entries per page
                dom: 'Bfrtilp', // Add export buttons, filter, processing indicator
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Alle"]]
            });
        });
    </script>
</body>
</html>