let originalData = null;
let modifiedRows = new Set();
let table;
let editMode = false;

// Add new deletion-related variables
let deleteMode = false;
let rowsToDelete = new Set();

function initializeTable() {
    table = $('#dataTable').DataTable({
        data: data,
        columns: [
            {
                data: null,
                orderable: false,
                className: 'delete-checkbox-cell d-none',
                visible: false,
                render: function(data, type, row, meta) {
                    return '<input type="checkbox" class="delete-checkbox" data-row="' + meta.row + '">';
                },
                width: '40px'
            },
            { 
                data: 'number',
                className: 'text-start'
            },
            { 
                data: 'name',
                className: 'text-start'
            },
            { 
                data: 'process1',
                className: 'checkbox-cell',
                orderable: false,
                render: function(data, type, row, meta) {
                    return `<input type="checkbox" ${data ? 'checked' : ''} class="process-checkbox" data-process="process1" data-row="${meta.row}" disabled>`;
                }
            },
            { 
                data: 'process2',
                className: 'checkbox-cell',
                orderable: false,
                render: function(data, type, row, meta) {
                    return `<input type="checkbox" ${data ? 'checked' : ''} class="process-checkbox" data-process="process2" data-row="${meta.row}" disabled>`;
                }
            },
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
        searching: true,
        ordering: true,
        pageLength: 10,
        lengthChange: false,
        columnDefs: [
            { targets: [0], orderable: false },  // Explicitly set delete column as not orderable
            { targets: [3, 4], width: '100px' }
        ],
        order: [[1, 'asc']],  // Set initial sort to second column (Number)
        language: {
            search: "Filter records:"
        },
        autoWidth: false  // Add this to prevent columns from auto-resizing
    });
}