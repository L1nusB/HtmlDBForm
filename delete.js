// Add functions for deletion mode
function enterDeleteMode() {
    deleteMode = true;
    rowsToDelete.clear();
    
    // Show delete column and buttons
    table.column(0).visible(true);
    $('.delete-checkbox-cell').removeClass('d-none');
    
    // Update button states
    $('#deleteBtn').addClass('d-none');
    $('#modifyBtn, #addEntriesBtn').prop('disabled', true);
    $('<button id="confirmDeleteBtn" class="btn btn-danger me-2"><i class="bi bi-check-lg"></i> Confirm</button>').insertAfter('#deleteBtn');
    $('<button id="cancelDeleteBtn" class="btn btn-secondary me-2"><i class="bi bi-x-lg"></i> Cancel</button>').insertAfter('#confirmDeleteBtn');
    
    table.draw(false);
}

function exitDeleteMode() {
    deleteMode = false;
    rowsToDelete.clear();
    
    // Hide delete column and remove highlights
    table.column(0).visible(false);
    $('.delete-checkbox-cell').addClass('d-none');
    table.$('tr').removeClass('table-danger');
    
    // Restore button states
    $('#deleteBtn').removeClass('d-none');
    $('#confirmDeleteBtn, #cancelDeleteBtn').remove();
    $('#modifyBtn, #addEntriesBtn').prop('disabled', false);
    
    table.draw(false);
}