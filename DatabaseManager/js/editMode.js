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
	modifiedRows = {};
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
	clearValidationErrors();
	editMode = false;
	disableEditFields();
	toggleButtonsEdit(false);
	table.column(-1).visible(false);
	modifiedRows = {};
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
		delete modifiedRows[rowIndex];

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
	Object.entries(modifiedRows).forEach((rowIndex, processes) => {
		const row = table.row(rowIndex);
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
	if (hasChanges) {
		const modifiedSummary = createModifiedSummary(data, originalData, processNames, modifiedRows);
		const numModifications = Object.values(modifiedSummary).reduce((total, arr) => {
									return total + (Array.isArray(arr) ? arr.length : 0);
								}, 0);
		showToast(`Verarbeite ${numModifications} Änderungen`, "start", "info");
		// Send to database
	} else {
		showToast("No changes were made", "finish", "info");
	}
	exitEditMode();
}

function isRowModified(rowIndex) {
	if (!originalData) return false;

	const original = originalData[rowIndex];
	const current = data[rowIndex];

	// Check if both are the same object reference (see utils.js)
	return !deepEqual(original, current);
}

function getModifiedProcesses(rowIndex) {
	if (!originalData) return false;

	const original = originalData[rowIndex];
	const current = data[rowIndex];
	let modifiedProcesses = [];
	processNames.forEach((process) => {
		if (!deepEqual(original[process], current[process])) {
			modifiedProcesses.push(process);
		}
	});
	return modifiedProcesses;
}

function updateModifiedState(rowIndex) {
	if (isRowModified(rowIndex)) {
		modifiedRows[rowIndex] = getModifiedProcesses(rowIndex);
	} else {
		delete modifiedRows[rowIndex];
	}
	updateRowHighlight();
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

function clearValidationErrors() {
	$(".process-date-input").removeClass("is-invalid");
}

function validate() {
	clearValidationErrors();
	let isValid = true;
	Object.entries(modifiedRows).forEach((rowIndex, processes) => {
		console.log(rowIndex, processes);
		// Check all checked checkboxes have valid dates
		const rowNode = table.row(rowIndex).node();

		// Find all div containers in this row that have a checked checkbox
		const dateInputs = $(rowNode)
			.find("div.d-flex")
			.filter(function () {
				// Find the checkbox within this div and check if it's checked
				return $(this).find(".process-checkbox").prop("checked");
			})
			.find(".process-date-input");

		// Process each date input
		dateInputs.each(function () {
			const dateInput = $(this);
			const dateValue = dateInput.val();

			// Check if the date is empty or invalid
			if (!dateValue || dateValue.trim() === "") {
				dateInput.addClass("is-invalid");
				isValid = false;
			} else {
				dateInput.removeClass("is-invalid");
			}
		});
	});
	return isValid;
}

function createModifiedSummary(data, originalData, processNames, modifiedRows) {
	const newProcesses = [];
    const updatedProcesses = [];
    const removedProcesses = [];

    modifiedRows.forEach(rowIndex => {
        const currentRow = data[rowIndex];
        const originalRow = originalData[rowIndex];
		const fk_RPA_Bankenuebersicht = currentRow.fk_Bankenuebersicht;

        processNames.forEach(processName => {
            const current = currentRow[processName];
            const original = originalRow[processName];

            // Skip if both are unchecked
            if (!current.checked && !original.checked) {
                return;
            }

            // Create info object with all properties except 'checked'
            const processInfo = {
                rowIndex,
                processName,
                ...Object.fromEntries(
                    Object.entries(current).filter(([key]) => key !== 'checked')
                )
            };

            if (!original.checked && current.checked) {
                // New process
                newProcesses.push({
                    ...processInfo,
                    checked: current.checked,
					fk_RPA_Bankenuebersicht: fk_RPA_Bankenuebersicht
                });
            } else if (original.checked && !current.checked) {
                // Removed process
                removedProcesses.push({
                    ...processInfo,
                    checked: current.checked,
					fk_RPA_Bankenuebersicht: fk_RPA_Bankenuebersicht
                });
            } else if (original.checked && current.checked) {
                // If checked is true in both, directly compare the objects
                if (!deepEqual(current, original)) {
                    updatedProcesses.push({
                        ...processInfo,
                        checked: current.checked,
                        previous: original,
						fk_RPA_Bankenuebersicht: fk_RPA_Bankenuebersicht
                    });
                }
            }
        });
    });

    return {
        new: newProcesses,
        updated: updatedProcesses,
        removed: removedProcesses
    };
}

function handleSave() {
	clearValidationErrors();
	if (Object.keys(modifiedRows).length > 0) {
		if (!validate()) {
			return;
		}
		$("#saveModal").modal("show");
	} else {
		finalizeSave();
	}
}

function handleCancel() {
	if (Object.keys(modifiedRows).length > 0) {
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
