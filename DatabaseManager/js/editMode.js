function enterEditMode() {
	$("#deleteBtn").prop("disabled", true); // Disable Delete button
	editMode = true;
	data = table.rows().data().toArray();
	// Deep copy the data to originalData
	originalData = JSON.parse(JSON.stringify(data));
	table.column(-1).visible(true, false);

	enableEditFields();
	table.draw(false);

	// Clear modified rows and check initial state
	modifiedRows.clear();
	data.forEach((_, index) => {
		updateModifiedState(index);
	});
}

function enableEditFields() {
	// Show revert and modify buttons
	$(".revert-cell").removeClass("d-none");
	$(".edit-field-btn").removeClass("d-none");
	$(".edit-field-btn").addClass("active");
	// Enable checkboxes and date inputs
	$(".process-checkbox").prop("disabled", false);
	$(".process-date-input").prop("disabled", false);
	toggleButtonsEdit(true);
}

function disableEditFields() {
	// Disable checkboxes and date inputs
	$(".process-checkbox").prop("disabled", true);
	$(".process-date-input").prop("disabled", true);
	// Hide revert and modify buttons
	table.$(".revert-btn").removeClass("active");
	$(".revert-cell").addClass("d-none");
	// These are not strictly necessary but good practice (handeled in render function?)
	table.$(".edit-field-btn").removeClass("active");
	$(".edit-field-btn").addClass("d-none");
	table.$("tr").removeClass("modified-row");
}

function exitEditMode() {
	$("#deleteBtn").prop("disabled", false); // Enable Delete button
	editMode = false;
	disableEditFields();
	toggleButtonsEdit(false);
	table.column(-1).visible(false);
	modifiedRows.clear();
}

function toggleButtonsEdit(deletionMode) {
	if (deletionMode) {
		$("#modifySaveBtn, #modifyBtn").addClass("d-none");
		$("#modifySaveBtn, #modifyCancelBtn").removeClass("d-none");
		$("#addEntriesBtn").prop("disabled", true); // Disable Add button
	} else {
		$("#modifyBtn").removeClass("d-none");
		$("#modifySaveBtn, #modifyCancelBtn").addClass("d-none");
		$("#addEntriesBtn").prop("disabled", false); // Enable Add button
	}
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
			$(row.node()).find(".process-checkbox").prop("disabled", false);
			$(row.node()).find(".process-date-input").prop("disabled", false);
			// Need to re-enable and display the edit button due to redraw and custom render
			// This could propably be done more elegantly but this works for now
			$(row.node()).find(".edit-field-btn").removeClass("d-none");
			$(row.node()).find(".edit-field-btn").addClass("active");
		}

		updateRowHighlight();
	}
}

function updateRowHighlight() {
	// First remove all highlights and active revert buttons
	table.$("tr").removeClass("modified-row");
	table.$(".revert-btn").removeClass("active");

	// Then add highlight to modified rows and activate their revert buttons
	modifiedRows.forEach((index) => {
		const row = table.row(index);
		$(row.node()).addClass("modified-row");
		$(row.node()).find(".revert-btn").addClass("active");

		// Ensure checkboxes remain enabled in edit mode
		if (editMode) {
			$(row.node()).find(".process-checkbox").prop("disabled", false);
		}
	});
}

function finalizeSave() {
	const hasChanges = modifiedRows.size > 0;
	const message = hasChanges ? "Changes saved successfully!" : "No changes were made";

	showToast(message, "finish", hasChanges ? "success" : "info");
	exitEditMode();
}

function isRowModified(rowIndex) {
	if (!originalData) return false;

	const original = originalData[rowIndex];
	const current = data[rowIndex];

	// Check if both are the same object reference (see utils.js)
	return !deepEqual(original, current);
}

function toggleCheckbox(checkbox) {
	if (editMode) {
		const rowIndex = checkbox.data("row");
		const process = checkbox.data("process");

		data[rowIndex][process]["checked"] = checkbox.prop("checked");
		updateModifiedState(rowIndex);
	}
}

function updateDateInput(dateInput) {
	if (editMode) {
		const rowIndex = dateInput.data("row");
		const process = dateInput.data("process");

		data[rowIndex][process]["startDate"] = formatDateStringFromISO(dateInput.val());
		updateModifiedState(rowIndex);
	}
}

function updateModifiedState(rowIndex) {
	if (isRowModified(rowIndex)) {
		modifiedRows.add(rowIndex);
	} else {
		modifiedRows.delete(rowIndex);
	}
	updateRowHighlight();
}

function handleSave() {
	if (modifiedRows.size > 0) {
		$("#saveModal").modal("show");
	} else {
		finalizeSave();
	}
}

function handleCancel() {
	if (modifiedRows.size > 0) {
		$("#cancelModal").modal("show");
	} else {
		data = JSON.parse(JSON.stringify(originalData));
		// Re-enable checkboxes if still in edit mode
		if (editMode) {
			$(".process-checkbox").prop("disabled", false);
		}
		exitEditMode();
		table.clear().rows.add(data).draw(false);
	}
}

function confirmSave() {
	$("#saveModal").modal("hide");
	finalizeSave();
}

function confirmCancel() {
	$("#cancelModal").modal("hide");
	data = JSON.parse(JSON.stringify(originalData));
	table.clear().rows.add(data).draw(false);
	// Re-enable checkboxes if still in edit mode
	if (editMode) {
		$(".process-checkbox").prop("disabled", false);
	}
	exitEditMode();
}
