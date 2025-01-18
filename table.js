let originalData = null;
let modifiedRows = new Set();
let table;
let editMode = false;

// Add new deletion-related variables
let deleteMode = false;
let rowsToDelete = new Set();

// Update the table initialization to handle dynamic columns
function initializeTable() {
    // Create basic columns configuration
    const columns = [
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
        }
    ];

    // Add process columns dynamically
    processColumns.forEach(col => {
        columns.push({
            data: col.toLowerCase(),
            className: 'checkbox-cell',
            orderable: false,
            render: function(data, type, row, meta) {
                return `<input type="checkbox" ${data ? 'checked' : ''} class="process-checkbox" data-process="${col.toLowerCase()}" data-row="${meta.row}" disabled>`;
            }
        });
    });

    // Add the revert column
    columns.push({
        data: null,
        className: 'revert-cell d-none',
        orderable: false,
        visible: false,
        render: function(data, type, row, meta) {
            return '<i class="bi bi-trash revert-btn" data-row="' + meta.row + '"></i>';
        }
    });

    table = $('#dataTable').DataTable({
        // data: data,
        // ajax: 'dataSource.json',
        ajax: {
            url: 'dataSource.json',
            dataSrc: 'data',
        },
        columns: columns,
        searching: true,
        ordering: true,
        pageLength: 10,
        lengthChange: false,
        columnDefs: [
            { targets: [0], orderable: false },
            { targets: processColumns.map((_, i) => i + 3), width: '100px' }
        ],
        order: [[1, 'asc']],
        language: {
            search: "Filter records:"
        },
        autoWidth: false
    });
}