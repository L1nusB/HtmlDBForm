<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Process Management</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.2.1/b-3.2.1/b-html5-3.2.1/b-print-3.2.1/datatables.min.css" rel="stylesheet">
    <!-- Custom styles -->
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
    <div class="container mt-5">
        <h2>Process Management</h2>
        <div class="action-buttons mb-3">
            <a href="./index.php" class="btn btn-outline-primary me-2">
                <i class="bi bi-table"></i> Process Overview
            </a>
            <button id="addProcessBtn" class="btn btn-success">
                <i class="bi bi-plus-lg"></i> Add Process
            </button>
        </div>
        <!-- Add a div with max-width to constrain table width -->
        <div style="max-width: 800px;">
            <table id="processTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>Process Name</th>
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

        <!-- Add/Edit Process Modal -->
        <div class="modal fade" id="processModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Add Process</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="processForm" novalidate>
                            <input type="hidden" id="processId">
                            <div class="mb-3">
                                <label for="processName" class="form-label">Process Name</label>
                                <input type="text" class="form-control" id="processName" required>
                                <div class="invalid-feedback">
                                    Please provide a process name.
                                </div>
                            </div>
                            <!-- Add duplicate error message container -->
                            <div id="duplicateError" class="alert alert-danger d-none">
                                This process name already exists.
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="saveProcessBtn">Save</button>
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
                        <p>Are you sure you want to delete process <strong><span id="deleteProcessName"></span></strong>?</p>
                        <div id="assignedInstituteWarning" class="alert alert-warning d-none">
                            <p class="fw-bold">This process has institute assignments.</p>
                            <hr>
                            <p class="mb-0 fw-bold">These assignments will also be deleted.</p>
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
    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.2.1/b-3.2.1/b-html5-3.2.1/b-print-3.2.1/datatables.min.js"></script>
    <!-- Utils -->
    <script src="./js/utils.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            const table = $('#processTable').DataTable({
                ajax: {
                    url: './db/get_processes_list.php',
                    dataSrc: ''
                },
                columns: [{
                        data: 'Prozessname',
                        width: '400px'
                    },
                    {
                        data: null,
                        orderable: false,
                        className: 'text-center',
                        width: '50px',
                        render: function(data, type, row) {
                            return `
                                <button class="btn btn-sm btn-primary edit-btn" data-id="${row.pk_RPA_Prozesse}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            `;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        className: 'text-center',
                        width: '50px',
                        render: function(data, type, row) {
                            return `
                                <button class="btn btn-sm btn-danger delete-btn" data-id="${row.pk_RPA_Prozesse}">
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

            // Placeholder for future functionality
            $('#addProcessBtn').click(function() {
                $('#processModal').modal('show');
            });
        });
    </script>
</body>

</html>
