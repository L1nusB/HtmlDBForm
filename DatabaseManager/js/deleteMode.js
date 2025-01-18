// Add new deletion-related variables
let deleteMode = false;
let rowsToDelete = new Set();

// Add functions for entering deletion mode
function enterDeleteMode() {
    data = table.rows().data().toArray();
    deleteMode = true;
    rowsToDelete.clear();
    
    // Show delete column
    table.column(0).visible(true);
    $('.delete-checkbox-cell').removeClass('d-none');
    
    // Update button states
    $('#deleteBtn').addClass('d-none');
    $('#confirmDeleteModeBtn, #cancelDeleteBtn').removeClass('d-none');
    $('#modifyBtn, #addEntriesBtn').prop('disabled', true);
    
    table.draw(false);
}

// Function for leaving deletion mode
function exitDeleteMode() {
    deleteMode = false;
    // Select all checkboxes in the table
    const checkboxes = document.querySelectorAll('#institutesTable .delete-checkbox');
            
    // Uncheck each checkbox
    checkboxes.forEach(checkbox => checkbox.checked = false);
    rowsToDelete.clear();
    
    // Hide delete column and remove highlights
    table.column(0).visible(false);
    $('.delete-checkbox-cell').addClass('d-none');
    table.$('tr').removeClass('table-danger');
    
    // Restore button states
    $('#deleteBtn').removeClass('d-none');
    $('#confirmDeleteModeBtn, #cancelDeleteBtn').addClass('d-none');
    $('#modifyBtn, #addEntriesBtn').prop('disabled', false);
    
    table.draw(false);
}

function processDeletion() {
    const rowIndices = Array.from(rowsToDelete).sort((a, b) => b - a);
                        
    // Remove the rows from the data array
    rowIndices.forEach(index => {
        data.splice(index, 1);
    });
    
    // Update the table
    table.clear().rows.add(data).draw();
    
    // Show success message and exit delete mode
    showToast(`Successfully deleted ${rowIndices.length} rows`, true);
    $('#confirmDeleteModal').modal('hide');
    exitDeleteMode();
}

function toggleSelectedForDeletion() {
    if (!deleteMode) return;
                
    const rowIndex = $(this).data('row');
    const row = $(this).closest('tr');
    
    if (this.checked) {
        rowsToDelete.add(rowIndex);
        row.addClass('table-danger');
    } else {
        rowsToDelete.delete(rowIndex);
        row.removeClass('table-danger');
    }
}