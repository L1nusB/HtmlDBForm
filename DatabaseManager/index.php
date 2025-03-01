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
    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.2.1/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/datatables.min.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <!-- Custom styles -->
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
    <div class="container mt-5">
        <h2>Institute Process Overview</h2>
        <!-- Add new navigation row -->
        <div class="navigation-buttons mb-3">
            <a href="./institute.php" class="btn btn-outline-primary me-2">
                <i class="bi bi-building"></i> Manage Institutes
            </a>
            <a href="./location.php" class="btn btn-outline-primary me-2">
                <i class="bi bi-geo-alt"></i> Manage Locations
            </a>
            <a href="./process.php" class="btn btn-outline-primary me-2">
                <i class="bi bi-gear"></i> Manage Processes
            </a>
        </div>
        <div class="action-buttons btn-group">
            <button class="btn btn-secondary dropdown-toggle me-2 rounded" type="button" id="processDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
        <div class="table-responsive full-width-table">
            <table id="institutesTable" class="table table-striped" >
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
        </div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.2.1/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/datatables.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="./js/utils.js"></script>
    <script src="./js/dataOverview.js"></script>
    <script src="./js/retrieveData.js"></script>
    <script src="./js/toggleButtons.js"></script>
    <script src="./js/DB/createAssignment.js"></script>
    <script src="./js/DB/deleteAssignment.js"></script>
    <script src="./js/DB/updateAssignment.js"></script>


    <script src="./js/addMode.js"></script>
    <script src="./js/deleteMode.js"></script>
    <script src="./js/editMode.js"></script>
    <script src="./js/populateAddModal.js"></script>
    <script src="./js/editDetailsModal.js"></script>

    <!-- Embed PHP variables as JavaScript -->
    <!-- Defines instituteMappingTest -->
    <?php include './db/create_bankenmap.php'; ?>
    <!-- Defines locationMapping and locationAssignment -->
    <?php include './db/check_standort.php'; ?>
    <!-- Defines processMapping and processNames -->
    <?php include './db/get_processes.php'; ?>

    <script>
        let testMode = false;
        // let testMode = true;
        let table;
        let data;
        // let processNames;

        let originalData = null;
        let modifiedRows = [];
        let editMode = false;
        // Add new deletion-related variables
        let deleteMode = false;
        let rowsToDelete = new Set();
        // Temporary storage for new entries
        let tempEntries = [];
        // Interval for reloading the table
        let reloadInterval;

        $(document).ready(function() {
            // First, fetch the data to create dynamic columns
            $.ajax({
                url: './db/get_data.php',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
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

                    // Initialize table using the shared function
                    table = initializeTable();
                    
                    // Populate the Add Modal with process names
                    popuplateAddModal();
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
                        // Toggle dates on checkbox change
                        $('#newEntryLocationUniform').change(updateAllLocations);
                    }

                    //// ----- Deletion Mode ----- ////
                    {
                        // Delete button click handler to enter Delete mode
                        $('#deleteBtn').click(enterDeleteMode);

                        //// Exit Deletion Mode ////
                        // Cancel delete button click handler (default für DB reload is false)
                        $('#cancelDeleteBtn').click(exitDeleteMode);

                        // Confirm delete button click handler
                        $('#confirmDeleteModeBtn').click(function() {
                            if (rowsToDelete.size === 0) {
                                showToast("No rows selected for deletion", "finish", "info");
                                // (default für DB reload is false)
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
                        let nonProcessColumns = exclude(table.columns(':visible').titles().toArray(), processNames);
                        selectedProcesses.push(...nonProcessColumns); // Always show non process columns (that are visible)

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