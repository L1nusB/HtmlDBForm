// Add functions for entering deletion mode
function enterDeleteMode() {
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