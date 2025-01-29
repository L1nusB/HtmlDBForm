<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Institute Management</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.2.1/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/datatables.min.css" rel="stylesheet">
    <!-- Custom styles -->
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
    <div class="container mt-5">
        <h2>Institute Management</h2>
        <div class="action-buttons mb-3">
            <a href="./index.php" class="btn btn-outline-primary me-2">
                <i class="bi bi-table"></i> Process Overview
            </a>
            <button id="addInstituteBtn" class="btn btn-success">
                <i class="bi bi-plus-lg"></i> Add Institute
            </button>
        </div>
        <!-- Add a div with max-width to constrain table width -->
        <div style="max-width: 800px;">
            <table id="instituteTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>RZBK</th>
                        <th>Name</th>
                        <th>Modify</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be populated by DataTables -->
                </tbody>
            </table>
        </div>
        <?php include './toast/toast.html'; ?>

        <!-- Add/Edit Institute Modal -->
        <div class="modal fade" id="instituteModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Add Institute</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="instituteForm" novalidate>
                            <input type="hidden" id="instituteId">
                            <div class="mb-3">
                                <label for="rzbk" class="form-label">RZBK</label>
                                <input type="text" class="form-control" id="rzbk" required>
                                <div class="invalid-feedback">
                                    Please provide an RZBK.
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" required>
                                <div class="invalid-feedback">
                                    Please provide an institute name.
                                </div>
                            </div>
                            <!-- Add duplicate error message container -->
                            <div id="duplicateError" class="alert alert-danger d-none">
                                This exact combination of RZBK and Name already exists.
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="saveInstituteBtn">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- RZBK Duplicate Confirmation Modal -->
        <div class="modal fade" id="duplicateConfirmModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Potential Duplicate RZBK</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>An institute with RZBK <span id="duplicateRZBK"></span> already exists with name: <span id="existingName"></span></p>
                        <p>Do you want to create another entry with the same RZBK but different name?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="confirmDuplicateBtn">Create Anyway</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Deletion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete institute <strong><span id="deleteInstituteRZBK"></span></strong>?</p>
                        <div id="assignedProcessesWarning" class="alert alert-warning d-none">
                            <p>This institute has the following processes assigned:</p>
                            <p id="assignedProcessesList" class="mb-2 fw-bold"></p>
                            <hr>
                            <p class="mb-0">These process assignments will also be deleted.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.2.1/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/datatables.min.js"></script>
    <!-- Utils -->
    <script src="./js/utils.js"></script>

    <script>
        let table;
        let editingId = null;
        let deletingRZBK = null; // Store RZBK for deletion message
        let pendingSave = null; // Store pending save data for duplicate confirmation

        $(document).ready(function() {
            // Initialize DataTable
            table = $('#instituteTable').DataTable({
                ajax: {
                    url: './db/get_institutes.php',
                    dataSrc: ''
                },
                columns: [{
                        data: 'RZBK',
                        width: '80px' // RZBK is max 4 digits, so this is plenty
                    },
                    {
                        data: 'Name',
                        width: '400px' // Give Name column fixed width to prevent wrapping
                    },
                    {
                        data: null,
                        orderable: false,
                        className: 'text-center',
                        width: '50px', // Set fixed width for modify column
                        render: function(data, type, row) {
                            return `
                                <button class="btn btn-sm btn-primary edit-btn" data-id="${row.pk_RPA_Bankenuebersicht}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            `;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        className: 'text-center',
                        width: '50px', // Set fixed width for delete column
                        render: function(data, type, row) {
                            return `
                                <button class="btn btn-sm btn-danger delete-btn" data-id="${row.pk_RPA_Bankenuebersicht}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            `;
                        }
                    }
                ],
                order: [
                    [0, 'asc']
                ],
                layout: {
                    topStart: ['pageLength'],
                    topEnd: ['buttons', 'search'],
                },
                buttons: [{
                    text: '<i class="bi bi-arrow-clockwise"></i> Refresh',
                    action: function(e, dt, node, config) {
                        dt.ajax.reload();
                    },
                }],
            });

            // Add Institute button click
            $('#addInstituteBtn').click(function() {
                editingId = null;
                $('#modalTitle').text('Add Institute');
                $('#instituteForm')[0].reset();
                $('#rzbk').prop('readonly', false).removeClass('bg-light'); // Remove disabled appearance
                $('#instituteModal').modal('show');
            });

            // Edit button click
            $('#instituteTable').on('click', '.edit-btn', function() {
                const id = $(this).data('id');
                editingId = id;
                const row = table.row($(this).closest('tr')).data();

                $('#modalTitle').text('Edit Institute');
                $('#rzbk').val(row.RZBK).prop('readonly', true).addClass('bg-light'); // Add disabled appearance
                $('#name').val(row.Name);
                $('#instituteModal').modal('show');
            });

            // Handle modal hidden event (triggers for both X and Cancel button)
            $('#instituteModal').on('hidden.bs.modal', function() {
                $('.is-invalid').removeClass('is-invalid');
                $('#duplicateError').addClass('d-none');
            });

            // Also clear error state when inputs change
            $('#rzbk, #name').on('input', function() {
                $(this).removeClass('is-invalid');
                if (!$('.is-invalid').length) {
                    $('#duplicateError').addClass('d-none');
                }
            });

            // Delete button click
            $('#instituteTable').on('click', '.delete-btn', function() {
                const row = table.row($(this).closest('tr')).data();
                editingId = row.pk_RPA_Bankenuebersicht;
                deletingRZBK = row.RZBK;

                // Check for assigned processes before showing delete modal
                $.ajax({
                    url: './db/check_assigned_processes.php',
                    method: 'POST',
                    data: JSON.stringify({
                        id: editingId
                    }),
                    contentType: 'application/json',
                    success: function(response) {
                        $('#deleteInstituteRZBK').text(deletingRZBK);
                        if (response.hasProcesses) {
                            $('#assignedProcessesList').text(response.processes.join(', '));
                            $('#assignedProcessesWarning').removeClass('d-none');
                        } else {
                            $('#assignedProcessesWarning').addClass('d-none');
                        }
                        $('#deleteModal').modal('show');
                    },
                    error: function(xhr) {
                        showToast('Error checking assigned processes', 'finish', 'error');
                    }
                });
            });

            // Reset warning when modal is hidden
            $('#deleteModal').on('hidden.bs.modal', function() {
                $('#assignedProcessesWarning').addClass('d-none');
            });

            // Save Institute
            $('#saveInstituteBtn').click(function() {
                // Reset validation and error states
                $('.is-invalid').removeClass('is-invalid');
                $('#duplicateError').addClass('d-none');
                let isValid = true;

                // Validate both fields
                const rzbkField = $('#rzbk');
                const nameField = $('#name');

                if (!rzbkField.val().trim()) {
                    rzbkField.addClass('is-invalid');
                    isValid = false;
                }

                if (!nameField.val().trim()) {
                    nameField.addClass('is-invalid');
                    isValid = false;
                }

                if (!isValid) return;

                const data = {
                    RZBK: rzbkField.val().trim(),
                    Name: nameField.val().trim(),
                    id: editingId
                };

                // If editing, bypass duplicate check
                if (editingId) {
                    performSave(data);
                    return;
                }

                // For new entries, check for duplicates first
                $.ajax({
                    url: './db/check_duplicate.php',
                    method: 'POST',
                    data: JSON.stringify(data),
                    contentType: 'application/json',
                    success: function(response) {
                        if (response.exists) {
                            if (response.exactMatch) {
                                // Show duplicate error and highlight fields
                                $('#duplicateError').removeClass('d-none');
                                rzbkField.addClass('is-invalid');
                                nameField.addClass('is-invalid');
                            } else {
                                // Show confirmation modal for RZBK duplicate
                                $('#duplicateRZBK').text(data.RZBK);
                                $('#existingName').text(response.existingName);
                                pendingSave = data;
                                $('#duplicateConfirmModal').modal('show');
                            }
                        } else {
                            performSave(data);
                        }
                    },
                    error: function(xhr) {
                        showToast('Error checking for duplicates', 'finish', 'error');
                    }
                });
            });

            // Handle duplicate confirmation
            $('#confirmDuplicateBtn').click(function() {
                if (pendingSave) {
                    performSave(pendingSave);
                    $('#duplicateConfirmModal').modal('hide');
                }
            });

            function performSave(data) {
                $.ajax({
                    url: './db/save_institute.php',
                    method: data.id ? 'PUT' : 'POST',
                    data: JSON.stringify(data),
                    contentType: 'application/json',
                    success: function(response) {
                        $('#instituteModal').modal('hide');
                        table.ajax.reload();
                        showToast(response.message, 'finish', response.status);
                    },
                    error: function(xhr) {
                        showToast('Error saving institute', 'finish', 'error');
                    }
                });
            }

            // Confirm Delete
            $('#confirmDeleteBtn').click(function() {
                if (!editingId) return;

                $.ajax({
                    url: './db/delete_institute.php',
                    method: 'DELETE',
                    data: JSON.stringify({
                        id: editingId,
                        RZBK: deletingRZBK // Pass RZBK to server
                    }),
                    contentType: 'application/json',
                    success: function(response) {
                        $('#deleteModal').modal('hide');
                        table.ajax.reload();
                        showToast(response.message || `Institute ${deletingRZBK} deleted successfully`, 'finish', response.status);
                    },
                    error: function(xhr) {
                        showToast('Error deleting institute', 'finish', 'error');
                    }
                });
            });
        });
    </script>
</body>

</html>