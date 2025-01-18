<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Institute Process Overview</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/2.2.1/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Custom styles -->
     <link rel="stylesheet" href="./css/style.css">
</head>

<body>
    <div class="container mt-5">
        <h2>Institute Process Overview</h2>
        <div class="action-buttons btn-group">
                <button class="btn btn-secondary dropdown-toggle me-2" type="button" id="processDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="bi bi-gear"></i> <!-- Bootstrap Icons gear icon -->
                </button>
                <ul class="dropdown-menu" aria-labelledby="processDropdown" id="processMenu">
                    <div class="form-check form-switch d-flex justify-content-center">
                        <input type="checkbox" role="switch" class="form-check-input" id="toggleDates" checked>
                        <label class="form-check-label" for="toggleDates">Show Dates</label>
                    </div>
                    <!-- Title for the dropdown -->
                    <li class="dropdown-header text-center">
                        <span class="fw-bold">Prozesse</span>
                        <hr class="dropdown-divider">
                    </li>
                    <!-- Checkboxes will be populated dynamically -->
                </ul>
            <button id="deleteBtn" class="btn btn-danger me-2 rounded">
                <i class="bi bi-trash"></i> Delete
            </button>
            <button id="confirmDeleteModeBtn" class="btn btn-danger me-2 rounded d-none">
                <i class="bi bi-check-lg"></i> Confirm
            </button>
            <button id="cancelDeleteBtn" class="btn btn-secondary me-2 rounded d-none">
                <i class="bi bi-x-lg"></i> Cancel
            </button>
            <button id="addEntriesBtn" class="btn btn-success me-2 rounded">
                <i class="bi bi-plus-lg"></i> Add
            </button>
            <button id="modifyBtn" class="btn btn-primary rounded">
                <i class="bi bi-pencil"></i> Modify
            </button>
            <button id="modifySaveBtn" class="btn btn-success rounded d-none">
                <i class="bi bi-check-lg"></i> Save
            </button>
            <button id="modifyCancelBtn" class="btn btn-danger rounded d-none">
                <i class="bi bi-x-lg"></i> Cancel
            </button>
        </div>
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

    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            // First, fetch the data to create dynamic columns
            $.ajax({
                url: './db/get_data.php',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Process data to create dynamic columns
                    const processNames = [...new Set(data.map(item => item.Prozessname))];

                    // Create header columns for processes
                    processNames.forEach(process => {
                        $('#institutesTable thead tr').append(`<th>${process}</th>`);
                        $('#processMenu').append(`
                            <li class="dropdown-item form-check">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="${process}" id="check-${process}" checked>
                                    <label class="form-check-label" for="check-${process}">${process}</label>
                                </div>
                            </li>
                        `);
                    });

                    // Initialize DataTable with dynamic columns
                    const table = $('#institutesTable').DataTable({
                        ajax: {
                            url: './db/get_data.php',
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
                                    // Check the state of the toggle switch
                                    const showDates = $('#toggleDates').is(':checked');

                                    return `
                                        <div class="d-flex flex-column flex-lg-row align-items-center justify-content-center h-100">
                                            <input type="checkbox" ${data.checked ? 'checked' : ''} disabled class="mb-1 mb-lg-0 mr-lg-2" 
                                                data-toggle="tooltip" title="${data.startDate ? data.startDate : ''}">
                                            ${showDates ? `<span>${data.startDate}</span>` : ''}
                                        </div>
                                    `;
                                }
                            })),
                            {
                                data: null,
                                className: 'revert-cell d-none',
                                orderable: false,
                                visible: false,
                                render: function(data, type, row, meta) {
                                    return '<i class="bi bi-trash revert-btn" data-row="' + meta.row + '"></i>';
                                }
                            }
                        ],
                    });
                    // Initialize Bootstrap tooltips
                    $('[data-toggle="tooltip"]').tooltip();
                    
                    // Toggle dates on checkbox change
                    $('#toggleDates').change(function() {
                        table.rows().invalidate().draw(); // Redraw the table to reflect changes
                        $('[data-toggle="tooltip"]').tooltip(); // Reinitialize tooltips after redraw
                    });

                    // Event listener for the checkboxes in the dropdown
                    $('#processMenu input[type="checkbox"]').change(function() {
                        const selectedProcesses = $('#processMenu input[type="checkbox"]:checked').map(function() {
                            return $(this).val();
                        }).get(); // Get selected process names
                        selectedProcesses.push('RZBK', 'Name'); // Always show RZBK and Name columns

                        // Loop through all columns and set visibility based on selection
                        const columns = table.columns().indexes(); // Get all column indexes
                        columns.each(function(index) {
                            const columnName = table.column(index).header().textContent;
                            if (selectedProcesses.includes(columnName)) {
                                table.column(index).visible(true);
                            } else {
                                table.column(index).visible(false);
                            }
                        });
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching data:', error);
                },


            });
            
        });
    </script>
    
</body>

</html>