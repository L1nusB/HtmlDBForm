<!-- Do not forget browser-sync start --config databaseBasics/browser-sync-config.js -->
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prozessübersicht</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/2.2.1/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <style>
        body {
            margin: 20px;
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
    <div class="container-fluid">
        <h1>Prozessübersicht</h1>

        <table id="processTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>RZBK</th>
                    <th>Name</th>
                    <th>Startdatum</th>
                    <th>Prozessname</th>
                    <th>Standort</th>
                </tr>
            </thead>
        </table>
    </div>

    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.bootstrap5.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            let table = $('#processTable').DataTable({
                processing: true,
                serverSide: false, // Set to true for large datasets
                ajax: {
                    url: 'get_process_data.php',
                    dataSrc: ''
                },
                columns: [
                    { data: 'RZBK' },
                    { data: 'Name' },
                    { 
                        data: 'ProduktionsStart',
                        render: function(data) {
                            let date = new Date(data);
                            return date.toLocaleDateString('de-DE');
                        }
                    },
                    { data: 'Prozessname' },
                    { data: 'Standort_Kuerzel' }
                ],
                order: [[2, 'desc']],
                pageLength: 25,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Alle"]]
            });

            // Refresh data every 30 seconds
            setInterval(function() {
                table.ajax.reload(null, false); // null = no callback, false = keep current page
            }, 30000);
        });
    </script>
</body>
</html>