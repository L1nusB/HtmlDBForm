// Temporary storage for new entries
let tempEntries = [];

// Function to reset the entry form
function resetEntryForm() {
    $('#newEntryNumber').val('').removeClass('is-invalid');
    $('#newEntryName').val('').removeClass('is-invalid');
    processColumns.forEach(col => {
        $(`#newEntry${col}`).prop('checked', false);
    });
}

// Function to validate the entry form
function validateEntryForm() {
    let isValid = true;
    
    if (!$('#newEntryNumber').val().trim()) {
        $('#newEntryNumber').addClass('is-invalid');
        isValid = false;
    } else {
        $('#newEntryNumber').removeClass('is-invalid');
    }
    
    if (!$('#newEntryName').val().trim()) {
        $('#newEntryName').addClass('is-invalid');
        isValid = false;
    } else {
        $('#newEntryName').removeClass('is-invalid');
    }
    
    return isValid;
}

// Function to update temporary entries table
function updateTempEntriesTable() {
    const tbody = $('#tempEntriesBody');
    tbody.empty();
    
    tempEntries.forEach((entry, index) => {
        const processColumnsHTML = processColumns.map(col => `
            <td class="text-center">
                <i class="bi ${entry[col.toLowerCase()] ? 'bi-check-lg text-success' : 'bi-x-lg text-danger'}"></i>
            </td>
        `).join('');
        
        tbody.append(`
            <tr>
                <td>${entry.number}</td>
                <td>${entry.name}</td>
                ${processColumnsHTML}
                <td class="text-center">
                    <button class="btn btn-link text-danger p-0 remove-temp-entry" data-index="${index}">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `);
    });
    
    $('#tempEntriesContainer').toggleClass('d-none', tempEntries.length === 0);
}