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
    <link href="https://cdn.datatables.net/2.2.1/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Custom styles -->
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
    <div class="container mt-5">
        <h2>Institute Management</h2>
        <div class="action-buttons mb-3">
            <button id="addInstituteBtn" class="btn btn-success">
                <i class="bi bi-plus-lg"></i> Add Institute
            </button>
        </div>
        <table id="instituteTable" class="table table-striped">
            <thead>
                <tr>
                    <th>RZBK</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be populated by DataTables -->
            </tbody>
        </table>
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
                        <form id="instituteForm">
                            <input type="hidden" id="instituteId">
                            <div class="mb-3">
                                <label for="rzbk" class="form-label">RZBK</label>
                                <input type="text" class="form-control" id="rzbk" required>
                            </div>
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" required>
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

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Deletion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this institute?
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
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.bootstrap5.min.js"></script>
    <!-- Utils -->
    <script src="./js/utils.js"></script>

    <script>
        let table;
        let editingId = null;

        $(document).ready(function() {
            // Initialize DataTable
            table = $('#instituteTable').DataTable({
                ajax: {
                    url: './db/get_institutes.php',
                    dataSrc: ''
                },
                columns: [
                    { data: 'RZBK' },
                    { data: 'Name' },
                    {
                        data: null,
                        orderable: false,
                        render: function(data, type, row) {
                            return `
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-primary edit-btn" data-id="${row.pk_RPA_Bankenuebersicht}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-btn" data-id="${row.pk_RPA_Bankenuebersicht}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ],
                order: [[0, 'asc']]
            });

            // Add Institute button click
            $('#addInstituteBtn').click(function() {
                editingId = null;
                $('#modalTitle').text('Add Institute');
                $('#instituteForm')[0].reset();
                $('#instituteModal').modal('show');
            });

            // Edit button click
            $('#instituteTable').on('click', '.edit-btn', function() {
                const id = $(this).data('id');
                editingId = id;
                const row = table.row($(this).closest('tr')).data();
                
                $('#modalTitle').text('Edit Institute');
                $('#rzbk').val(row.RZBK);
                $('#name').val(row.Name);
                $('#instituteModal').modal('show');
            });

            // Delete button click
            $('#instituteTable').on('click', '.delete-btn', function() {
                editingId = $(this).data('id');
                $('#deleteModal').modal('show');
            });

            // Save Institute
            $('#saveInstituteBtn').click(function() {
                const data = {
                    RZBK: $('#rzbk').val(),
                    Name: $('#name').val(),
                    id: editingId
                };

                $.ajax({
                    url: './db/save_institute.php',
                    method: editingId ? 'PUT' : 'POST',
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
            });

            // Confirm Delete
            $('#confirmDeleteBtn').click(function() {
                if (!editingId) return;

                $.ajax({
                    url: './db/delete_institute.php',
                    method: 'DELETE',
                    data: JSON.stringify({ id: editingId }),
                    contentType: 'application/json',
                    success: function(response) {
                        $('#deleteModal').modal('hide');
                        table.ajax.reload();
                        showToast(response.message, 'finish', response.status);
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
