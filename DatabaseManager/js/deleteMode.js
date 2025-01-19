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

function handlePageChange() {
    // Ensure all checkboxes are shown on page change (requires reload once)
    // After first reload, the checkboxes will be shown on page change regardless
	if (deleteMode) {
        // Use timeout because the initial <td> elements are not yet created
        setTimeout(() => {
        $('.delete-checkbox-cell').removeClass('d-none');
        table.draw('page');
        }, 20);
	}
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
                        
    // Send to database
	showToast(`Start deleting ${rowIndices.length} rows from the database`, "start", "info");

    

    try {
        // Show success message
        showToast(`Successfully removed ${rowIndices.length} entries`, "finish", "success");
    } catch (error) {
        showToast("Failed to remove entries", "finish", "danger");
        console.log(error);
    }
    
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
