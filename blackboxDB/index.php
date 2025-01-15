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
            // Fetch data and initialize DataTable
            $.ajax({
                url: 'data.php',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Process data to create dynamic columns
                    const processNames = [...new Set(data.map(item => item.Prozessname))];
                    const tableBody = $('#institutesTable tbody');
                    
                    // Create header columns for processes
                    processNames.forEach(process => {
                        $('#institutesTable thead tr').append(`<th>${process}</th>`);
                    });

                    // Group data by RZBK and Name
                    const groupedData = {};
                    data.forEach(item => {
                        const key = `${item.RZBK}-${item.Name}`;
                        if (!groupedData[key]) {
                            groupedData[key] = { RZBK: item.RZBK, Name: item.Name, processes: {} };
                        }
                        groupedData[key].processes[item.Prozessname] = item.ProduktionsStart || null;
                    });

                    // Populate table rows
                    for (const key in groupedData) {
                        const row = groupedData[key];
                        const newRow = `<tr>
                            <td>${row.RZBK}</td>
                            <td>${row.Name}</td>`;
                        
                        processNames.forEach(process => {
                            const startDate = row.processes[process];
                            console.log(startDate);
                            const checked = startDate ? 'checked' : '';
                            console.log(checked);
                            const displayStart = startDate ? startDate.toISOString().split('T')[0] : '';
                            newRow += `<td>
                                <input type="checkbox" ${checked} disabled>
                                <span>${displayStart}</span>
                            </td>`;
                        });

                        newRow += `</tr>`;
                        tableBody.append(newRow);
                    }

                    // Initialize DataTable
                    $('#institutesTable').DataTable();
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching data:', error);
                }
            });
        });
    </script>
</body>
</html>