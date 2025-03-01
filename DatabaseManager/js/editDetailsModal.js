function modifyProcessDetails(modifyButton) {
	if (editMode) {
		const rowIndex = modifyButton.data("row");
		const process = modifyButton.data("process");
		const enabled = data[rowIndex][process]["checked"];
		const startDate = data[rowIndex][process]["startDate"];

		$("#processDetailsModal").modal("show");
		$("#processDetailsModal").attr("data-row", rowIndex);
		$("#processDetailsModal").attr("data-process", process);
		setProcessDetailsModalValues(enabled, startDate);
	}
}

// Function to set initial values
function setProcessDetailsModalValues(enabled, startDate) {
    $('#processDetailsModalEnabled').prop("checked", enabled);
    $('#processDetailsModalStartDate').val(formatDateStringToISO(startDate));
}

// Function to get current values
function getProcessDetailsModalValues() {
    return {
        enabled: $('#processDetailsModalEnabled').prop("checked"),
        startDate: formatDateStringFromISO($('#processDetailsModalStartDate').val()),
    };
}

function validateProcessDetailsModal(vals) {
    let isValid = true;

    if (!vals.startDate) {
        $("#processDetailsModalStartDate").addClass("is-invalid");
        isValid = false;
    } else {
        $("#processDetailsModalStartDate").removeClass("is-invalid");
    }
    return isValid;
}

function updateData(vals) {
    // Update the data object with the new values
    const rowIndex = $("#processDetailsModal").data("row");
    const process = $("#processDetailsModal").data("process");
    data[rowIndex][process]["checked"] = vals.enabled;
    data[rowIndex][process]["startDate"] = vals.startDate;
}

// Example save function
function saveProcessDetailsModal() {
    const values = getProcessDetailsModalValues();

    if (!validateProcessDetailsModal(values)) {
        return;
    }

    // Handle the save operation here
    updateData(values);
    // Redraw the row with updated data
    const rowIndex = $("#processDetailsModal").data("row");
    const row = table.row(rowIndex);
    row.data(data[rowIndex]).draw(false);
    // Re-enable checkboxes after redraw
    if (editMode) {
        $(row.node()).find(".process-checkbox").prop("disabled", false);
        $(row.node()).find(".process-date-input").prop("disabled", false);
        // Need to re-enable and display the edit button due to redraw and custom render
        // This could propably be done more elegantly but this works for now
        $(row.node()).find(".edit-field-btn").removeClass("d-none");
        $(row.node()).find(".edit-field-btn").addClass("active");
        // Update the row highlight
        updateModifiedState(rowIndex);
    }
    
    // Close the modal
    $("#processDetailsModal").modal("hide");
}