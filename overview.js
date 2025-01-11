function enterEditMode() {
    editMode = true;
    originalData = JSON.parse(JSON.stringify(data));
    table.column(-1).visible(true, false);
    $('.revert-cell').removeClass('d-none');
    table.draw(false);
    $('.process-checkbox').prop('disabled', false);
    $('#modifyBtn').addClass('d-none');
    $('#saveBtn, #cancelBtn').removeClass('d-none');

    // Clear modified rows and check initial state
    modifiedRows.clear();
    data.forEach((_, index) => {
        updateModifiedState(index);
    });
}

function exitEditMode() {
    editMode = false;
    $('.process-checkbox').prop('disabled', true);
    $('#modifyBtn').removeClass('d-none');
    $('#saveBtn, #cancelBtn').addClass('d-none');
    table.$('.revert-btn').removeClass('active');
    table.column(-1).visible(false);
    $('.revert-cell').addClass('d-none');
    modifiedRows.clear();
    table.$('tr').removeClass('modified-row');
}

function revertRow(rowIndex) {
    if (originalData[rowIndex]) {
        data[rowIndex] = JSON.parse(JSON.stringify(originalData[rowIndex]));
        modifiedRows.delete(rowIndex);
        
        // Redraw the row with original data but maintain checkbox state
        const row = table.row(rowIndex);
        row.data(data[rowIndex]).draw(false);
        
        // Re-enable checkboxes after redraw
        if (editMode) {
            $(row.node()).find('.process-checkbox').prop('disabled', false);
        }
        
        updateRowHighlight();
    }
}

function updateRowHighlight() {
    // First remove all highlights and active revert buttons
    table.$('tr').removeClass('modified-row');
    table.$('.revert-btn').removeClass('active');
    
    // Then add highlight to modified rows and activate their revert buttons
    modifiedRows.forEach(index => {
        const row = table.row(index);
        $(row.node()).addClass('modified-row');
        $(row.node()).find('.revert-btn').addClass('active');
        
        // Ensure checkboxes remain enabled in edit mode
        if (editMode) {
            $(row.node()).find('.process-checkbox').prop('disabled', false);
        }
    });
}

function finalizeSave() {
    const hasChanges = modifiedRows.size > 0;
    const message = hasChanges ? 
        "Changes saved successfully!" : 
        "No changes were made";
    
    showToast(message, hasChanges);
    exitEditMode();
}

function isRowModified(rowIndex) {
    if (!originalData) return false;
    
    const original = originalData[rowIndex];
    const current = data[rowIndex];
    
    // Compare process1 and process2 values
    return original.process1 !== current.process1 || 
           original.process2 !== current.process2;
}

function updateModifiedState(rowIndex) {
    if (isRowModified(rowIndex)) {
        modifiedRows.add(rowIndex);
    } else {
        modifiedRows.delete(rowIndex);
    }
    updateRowHighlight();
}