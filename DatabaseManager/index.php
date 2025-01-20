<!-- browser-sync start --config DatabaseManager/browser-sync-config.js -->
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
                    <div class="d-flex justify-content-center">
                        <div class="form-check form-switch">
                            <input type="checkbox" role="switch" class="form-check-input" id="toggleDates" checked>
                            <label class="form-check-label" for="toggleDates">Show Dates</label>
                        </div>
                    </div>
                    <!-- Title for the dropdown -->
                    <li class="dropdown-header text-center">
                        <span class="fw-bold">Prozesse</span>
                        <hr class="dropdown-divider">
                    </li>
                    <!-- Checkboxes will be populated dynamically -->
                </ul>
            <button id="deleteBtn" class="btn btn-danger me-2 rounded disabled">
                <i class="bi bi-trash"></i> Delete
            </button>
            <button id="confirmDeleteModeBtn" class="btn btn-danger me-2 rounded d-none">
                <i class="bi bi-check-lg"></i> Confirm
            </button>
            <button id="cancelDeleteBtn" class="btn btn-secondary me-2 rounded d-none">
                <i class="bi bi-x-lg"></i> Cancel
            </button>
            <button id="addEntriesBtn" class="btn btn-success me-2 rounded disabled">
                <i class="bi bi-plus-lg"></i> Add
            </button>
            <button id="modifyBtn" class="btn btn-primary me-2 rounded disabled">
                <i class="bi bi-pencil"></i> Modify
            </button>
            <button id="modifySaveBtn" class="btn btn-success me-2 rounded d-none">
                <i class="bi bi-check-lg"></i> Save
            </button>
            <button id="modifyCancelBtn" class="btn btn-danger me-2 rounded d-none">
                <i class="bi bi-x-lg"></i> Cancel
            </button>
        </div>
        <table id="institutesTable" class="table table-striped">
            <thead>
                <tr>
                    <th class="delete-checkbox-cell d-none">#</th>
                    <th>RZBK</th>
                    <th>Name</th>
                    <th>Standort</th>
                    <!-- Dynamic process columns will be added here -->
                </tr>
            </thead>
            <tbody>
                <!-- Data will be populated by DataTables -->
            </tbody>
        </table>
        <?php include './toast/toast.html'; ?>
        <?php include './modal/confirmationModal.html'; ?>
        <?php include './modal/addModal.html'; ?>
        <?php include './modal/modifyDetailsModal.html'; ?>
    </div>

    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.bootstrap5.min.js"></script>

    <script src="./js/utils.js"></script>
    <script src="./js/toggleButtons.js"></script>
    <script src="./js/addMode.js"></script>
    <script src="./js/deleteMode.js"></script>
    <script src="./js/editMode.js"></script>
    <script src="./js/populateAddModal.js"></script>
    <script src="./js/editDetailsModal.js"></script>

    <!-- Embed PHP variables as JavaScript -->
    <!-- Defines instituteMappingTest -->
    <?php include './db/create_bankenmap.php';?>
    <!-- Defines locationMapping and locationAssignment -->
    <?php include './db/check_standort.php';?>

    <script>
        let table;
        let data;
        let processNames;

        let originalData = null;
        let modifiedRows = new Set();
        let editMode = false;
        // Add new deletion-related variables
        let deleteMode = false;
        let rowsToDelete = new Set();
        // Temporary storage for new entries
        let tempEntries = [];


        $(document).ready(function() {
            // First, fetch the data to create dynamic columns
            $.ajax({
                url: './db/get_data.php',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Process data to create dynamic columns
                    processNames = [...new Set(data.map(item => item.Prozessname))];

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
                    table = $('#institutesTable').DataTable({
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
                                            processes: {},
                                            Standort: locationAssignment[item.fk_RPA_Bankenuebersicht] || 'unknown'
                                        };
                                    }
                                    groupedData[key].processes[item.Prozessname] = item.ProduktionsStart || null;
                                });

                                return Object.values(groupedData).map(row => {
                                    const newRow = {
                                        RZBK: row.RZBK,
                                        Name: row.Name,
                                        Standort: row.Standort,
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
                        columns: [
                            {
                                data: null,
                                title: '#',
                                orderable: false,
                                className: 'delete-checkbox-cell d-none',
                                visible: false,
                                render: function(data, type, row, meta) {
                                    return '<input type="checkbox" class="delete-checkbox" data-row="' + meta.row + '">';
                                },
                                width: '40px'
                            },
                            {
                                data: 'RZBK',
                                title: 'RZBK'
                            },
                            {
                                data: 'Name',
                                title: 'Name'
                            },
                            {
                                data: 'Standort',
                                title: 'Standort',
                                orderable: false,
                            },
                            ...processNames.map(process => ({
                                data: process,
                                className: 'checkbox-cell text-center',
                                orderable: false,
                                render: function(data, type, row, meta) {
                                    // Check the state of the toggle switch
                                    const showDates = $('#toggleDates').is(':checked');

                                    return `
                                        <div class="d-flex flex-column flex-lg-row align-items-center justify-content-center gap-1">
                                            <input type="checkbox" ${data.checked ? 'checked' : ''} disabled 
                                                class="mb-1 mb-lg-0 mr-lg-2 process-checkbox" 
                                                data-process="${process}" data-row="${meta.row}"
                                                data-toggle="tooltip" title="${data.startDate ? data.startDate : ''}">
                                            ${showDates ? 
                                                data.startDate ? 
                                                `<input type="date" style="width: 105px;" 
                                                value="${formatDateStringToISO(data.startDate)}" 
                                                class="form-control form-control-sm process-date-input" 
                                                data-process="${process}" data-row="${meta.row}"
                                                disabled
                                                >` 
                                                : '' 
                                            : ''}
                                            <i class="bi bi-pencil edit-field-btn d-none" data-process="${process}" data-row="${meta.row}"></i>
                                        </div>
                                    `;
                                }
                            })),
                            {
                                data: null,
                                title: 'Revert',
                                className: 'revert-cell d-none',
                                orderable: false,
                                visible: false,
                                render: function(data, type, row, meta) {
                                    return '<i class="bi bi-trash revert-btn" data-row="' + meta.row + '"></i>';
                                }
                            }
                        ],
                        order: [[1, 'asc']],
                        // Do not allow ordering as it messes up in editing and deleting.
                        ordering: false,
                    });
                    // Enable the buttons after the table is initialized
                    enableButtons();
                    
                    // Populate the Add Modal with process names
                    popuplateAddModal();
                    
                    // Handle page change event (Make sure that all checkboxes/fields are shown and in the correct state)
                    table.on('page', handlePageChange);
                    // Handle ordering change event (Make sure that all checkboxes/fields are shown and in the correct state)
                    // table.on('order', handleOrderingChange);
                    
                    //// ----- Edit Mode ----- ////
                    {
                        // Handle Modify button click to enter Edit mode
                        $('#modifyBtn').click(enterEditMode);
                        // Handle Save button click
                        $('#modifySaveBtn').click(handleSave);
                    
                        // Handle Cancel button click
                        $('#modifyCancelBtn').click(handleCancel);
                        // Handle Modal Save confirmation 
                        $('#modifyConfirmSave').click(confirmSave);
                        // Handle Modal Cancel confirmation
                        $('#modifyConfirmCancel').click(confirmCancel);
                        // Handle checkbox changes (set checkbox state in data)
                        $('#institutesTable').on('change', '.process-checkbox', function() {
                            const checkbox = $(this);
                            toggleCheckbox(checkbox);
                        });
                        $('#institutesTable').on('change', '.process-date-input', function() {
                            const dateInput = $(this);
                            updateDateInput(dateInput);
                        });
                        // Handle revert button clicks
                        $('#institutesTable').on('click', '.revert-btn', function() {
                            const rowIndex = $(this).data('row');
                            revertRow(rowIndex);
                        });
                        // Handle modify button clicks
                        $('#institutesTable').on('click', '.edit-field-btn', function() {
                            const modifyButton = $(this);
                            modifyProcessDetails(modifyButton);
                        });
                    }
                    
                    //// ----- Add Mode/Modal ----- ////
                    {
                        // Add button click handler to enter Add mode
                        $('#addEntriesBtn').click(enterAddMode);

                        // Handle Add Entry button click in Modal
                        $('#addToTempBtn').click(addToTempEntries);
                        
                        // Handle remove temporary entry in Modal
                        $('#tempEntriesBody').on('click', '.remove-temp-entry', removeTempEntries);
                        
                        // Handle Save All Entries button click to finalize adding entries
                        $('#saveNewEntriesBtn').click(saveAddedEntries);
                        
                        // Handle modal hidden event
                        $('#addEntriesModal').on('hidden.bs.modal', resetOnHidden);

                        // Handle checkbox changes
                        document.querySelectorAll('.process-checkbox-new').forEach(checkbox => {
                            checkbox.addEventListener('change', handleProcessCheckboxChanges);
                        });

                        // Toggle dates on checkbox change
                        $('#toggleUniformLocation').change(toggleUniformLocation);
                    }
                    
                    //// ----- Deletion Mode ----- ////
                    {
                        // Delete button click handler to enter Delete mode
                        $('#deleteBtn').click(enterDeleteMode);

                        //// Exit Deletion Mode ////
                        // Cancel delete button click handler
                        $('#cancelDeleteBtn').click(exitDeleteMode);

                        // Confirm delete button click handler
                        $('#confirmDeleteModeBtn').click(function() {
                            if (rowsToDelete.size === 0) {
                                showToast("No rows selected for deletion", "finish", "info");
                                exitDeleteMode();
                                return;
                            }
                            
                            $('#confirmDeleteModal').modal('show');
                        });
                        //// Confirmation Modals Delete Mode ////
                        // Final delete confirmation handler
                        $('#finalConfirmDeleteBtn').click(processDeletion);
                        // Delete checkbox change handler
                        $('#institutesTable').on('change', '.delete-checkbox', toggleSelectedForDeletion);
                    }



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