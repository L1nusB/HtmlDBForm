// Add functions for entering deletion mode
function enterDeleteMode() {
	data = table.rows().data().toArray();
	deleteMode = true;
	rowsToDelete.clear();

	// Show delete column
	table.column(0).visible(true);
	$(".delete-checkbox-cell").removeClass("d-none");

	// Update button states
	toggleButtonsDelete(true);

	table.draw(false);
}

function toggleButtonsDelete(deletionMode) {
    if (deletionMode) {
        $("#deleteBtn").addClass("d-none");
        $("#confirmDeleteModeBtn, #cancelDeleteBtn").removeClass("d-none");
        $("#modifyBtn, #addEntriesBtn").prop("disabled", true);
    } else {
        $("#deleteBtn").removeClass("d-none");
        $("#confirmDeleteModeBtn, #cancelDeleteBtn").addClass("d-none");
        $("#modifyBtn, #addEntriesBtn").prop("disabled", false);
    }
}

// Function for leaving deletion mode
function exitDeleteMode() {
	deleteMode = false;
    disableFields();

	rowsToDelete.clear();

	// Hide delete column and remove highlights
	table.column(0).visible(false);

	// Restore button states
	toggleButtonsDelete(false);

	table.draw(false);
}

function disableFields() {
	// Select all checkboxes in the table
	const checkboxes = document.querySelectorAll("#institutesTable .delete-checkbox");

	// Uncheck each checkbox
	checkboxes.forEach((checkbox) => (checkbox.checked = false));

	$(".delete-checkbox-cell").addClass("d-none");
	table.$("tr").removeClass("table-danger");
}

function processDeletion() {
	const rowIndices = Array.from(rowsToDelete).sort((a, b) => b - a);

	// Send to database
	showToast(`Start deleting ${rowIndices.length} rows from the database`, "start", "info");

	// Log the data of the rows to be deleted
	rowIndices.forEach((index) => {
		console.log(data[index]);
		console.log('Bankenuebersicht', data[index].fk_Bankenuebersicht);
		console.log('Standort', data[index].fk_Location);
	});

	try {
		// Delete records from the database
		// deleteRecord();
		// Show success message
		showToast(`Successfully removed ${rowIndices.length} entries`, "finish", "success");
	} catch (error) {
		showToast("Failed to remove entries", "finish", "danger");
		console.log(error);
	}

	$("#confirmDeleteModal").modal("hide");
	exitDeleteMode();
}

function toggleSelectedForDeletion() {
	if (!deleteMode) return;

	const rowIndex = $(this).data("row");
	const row = $(this).closest("tr");

	if (this.checked) {
		rowsToDelete.add(rowIndex);
		row.addClass("table-danger");
	} else {
		rowsToDelete.delete(rowIndex);
		row.removeClass("table-danger");
	}
}
