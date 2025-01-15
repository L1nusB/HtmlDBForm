<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Institute Process Overview</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/2.2.1/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.bootstrap5.min.js"></script>
    <style>
        /* Optional custom styles */
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2>Institute Process Overview</h2>
        <table id="institutesTable" class="table table-striped">
            <thead>
                <tr>
                    <th>RZBK</th>
                    <th>Name</th>
                    <!-- Dynamic process columns will be added here -->
                </tr>
            </thead>
            <tbody>
                <!-- Data will be populated by DataTables -->
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            // First, fetch the data to create dynamic columns
            $.ajax({
                url: 'data.php',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Process data to create dynamic columns
                    const processNames = [...new Set(data.map(item => item.Prozessname))];

                    // Create header columns for processes
                    processNames.forEach(process => {
                        $('#institutesTable thead tr').append(`<th>${process}</th>`);
                    });

                    // Initialize DataTable with dynamic columns
                    const table = $('#institutesTable').DataTable({
                        ajax: {
                            url: 'data.php',
                            dataSrc: function(json) {
                                const groupedData = {};
                                json.forEach(item => {
                                    const key = `${item.RZBK}-${item.Name}`;
                                    if (!groupedData[key]) {
                                        groupedData[key] = {
                                            RZBK: item.RZBK,
                                            Name: item.Name,
                                            processes: {}
                                        };
                                    }
                                    groupedData[key].processes[item.Prozessname] = item.ProduktionsStart || null;
                                });

                                return Object.values(groupedData).map(row => {
                                    const newRow = {
                                        RZBK: row.RZBK,
                                        Name: row.Name
                                    };
                                    processNames.forEach(process => {
                                        newRow[process] = {
                                            checked: row.processes[process] ? true : false,
                                            startDate: row.processes[process] || ''
                                        };
                                    });
                                    return newRow;
                                });
                            }
                        },
                        columns: [{
                                data: 'RZBK'
                            },
                            {
                                data: 'Name'
                            },
                            ...processNames.map(process => ({
                                data: process,
                                render: function(data) {
                                    const startDate = data.startDate.date ? new Date(data.startDate.date) : null;
                                    const formattedDate = startDate ? `${startDate.getDate()}.${startDate.getMonth() + 1}.${startDate.getFullYear()}` : '';
                                    return `
                                        <div class="d-flex flex-column flex-lg-row align-items-center">
                                            <input type="checkbox" ${data.checked ? 'checked' : ''} disabled class="mb-1 mb-sm-0 mr-sm-2">
                                            <span>${formattedDate}</span>
                                        </div>
                                    `;
                                }
                            }))
                        ]
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching data:', error);
                }
            });
        });
    </script>
</body>

</html>