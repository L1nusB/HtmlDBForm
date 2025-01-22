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
function exitDeleteMode(updateData = false) {
	deleteMode = false;
    disableFields();

	rowsToDelete.clear();

	// Hide delete column and remove highlights
	table.column(0).visible(false);

	// Restore button states
	toggleButtonsDelete(false);

	// Check if the data should be updated to reduce database calls
	if (updateData) {
		// Reloads the table with the current data of the database
		manualReload(table);
	} else {
		table.draw(false);
	}
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
		// Remove the rows from the data array (local)
		rowIndices.forEach(index => {
			data.splice(index, 1);
		});
		// Update the table and redraw (is only temporary)
		// After database is finished the table is updated with new data anyways
		table.clear().rows.add(data).draw();

		// Delete records from the database
		rowIndices.forEach((index) => {
			deleteRecord(data[index].fk_Bankenuebersicht, data[index].fk_Location);
		});
		// Show success message
		showToast(`Successfully removed ${rowIndices.length} entries`, "finish", "success");
	} catch (error) {
		showToast("Failed to remove entries", "finish", "danger");
		console.log(error);
	}

	$("#confirmDeleteModal").modal("hide");
	// After handling deletion always refresh the data from the database
	exitDeleteMode(true);
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
