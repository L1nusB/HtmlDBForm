function enterAddMode() {
	tempEntries = [];
    data = table.rows().data().toArray();
	resetEntryForm();
	updateTempEntriesTable();
	$("#addEntriesModal").modal("show");
}

// Function to reset the entry form on Cancel or Save (Exit Modal)
function resetEntryForm() {
	$("#newEntryRZBK").val("").removeClass("is-invalid");
	$("#newEntryName").val("").removeClass("is-invalid");
	// Reset all checkboxes
	document.querySelectorAll(".process-checkbox-new").forEach((checkbox) => {
		checkbox.checked = false;
	});
	// Reset all date inputs
	document.querySelectorAll(".date-input-new").forEach((dateInput) => {
		dateInput.value = "";
		dateInput.setAttribute("disabled", "");
		dateInput.classList.remove("is-invalid");
	});
	processNames.forEach((col) => {
		$(`#newEntry${col}`).prop("checked", false);
	});
}

// Function to validate the entry form
function validateEntryForm() {
	let isValid = true;

	if (!$("#newEntryRZBK").val().trim()) {
		$("#newEntryRZBK").addClass("is-invalid");
		isValid = false;
	} else {
		$("#newEntryRZBK").removeClass("is-invalid");
	}

	if (!$("#newEntryName").val().trim()) {
		$("#newEntryName").addClass("is-invalid");
		isValid = false;
	} else {
		$("#newEntryName").removeClass("is-invalid");
	}

	// Check all checked checkboxes have valid dates
	document.querySelectorAll(".process-checkbox-new:checked").forEach((checkbox) => {
		const process = checkbox.dataset.process;
		const dateInput = document.querySelector(`#dateEntry${process.toLowerCase()}`);
		if (!dateInput.value) {
			dateInput.classList.add("is-invalid");
			isValid = false;
		} else {
			dateInput.classList.remove("is-invalid");
		}
	});

	return isValid;
}

// Function to update temporary entries table
function updateTempEntriesTable() {
	const tbody = $("#tempEntriesBody");
	tbody.empty();

	tempEntries.forEach((entry, index) => {
		const processColumnsHTML = processNames
			.map(
				(col) => `
            <td class="text-center">
                <i data-bs-toggle="tooltip" title="${entry[col.toLowerCase()] ? entry[`${col.toLowerCase()}_date`] : ""}" class="bi ${
					entry[col.toLowerCase()] ? "bi-check-lg text-success" : "bi-x-lg text-danger"
				}"></i>
            </td>
        `
			)
			.join("");

		tbody.append(`
            <tr>
                <td>${entry.rzbk}</td>
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

	$("#tempEntriesContainer").toggleClass("d-none", tempEntries.length === 0);
}

function addToTempEntries() {
	if (!validateEntryForm()) return;

	const newEntry = {
		rzbk: $("#newEntryRZBK").val().trim(),
		name: $("#newEntryName").val().trim(),
	};

	dateFormatOptions = {
		day: "2-digit",
		month: "2-digit",
		year: "numeric",
	};

	// Add process values dynamically
	processNames.forEach((col) => {
		newEntry[col.toLowerCase()] = $(`#newEntry${col.toLowerCase()}`).prop("checked");
		newEntry[`${col.toLowerCase()}_date`] = (new Date($(`#dateEntry${col.toLowerCase()}`).val())).toLocaleDateString(
			"de-DE",
			dateFormatOptions
		);
	});

	tempEntries.push(newEntry);
	updateTempEntriesTable();
	resetEntryForm();
}

function removeTempEntries() {
	const index = $(this).data("index");
	tempEntries.splice(index, 1);
	updateTempEntriesTable();
}

function resetOnHidden() {
	resetEntryForm();
	tempEntries = [];
	updateTempEntriesTable();
}

function saveAddedEntries() {
	if (tempEntries.length === 0) {
		showToast("No entries to save", "finished", "info");
        $("#addEntriesModal").modal("hide");
		return;
	}

    // Send to database
	showToast(`Start adding ${tempEntries.length} new entries to database`, "start", "info");
    try {
        // Show success message
        showToast(`Successfully added ${tempEntries.length} new entries`, "finish", "success");
    } catch (error) {
        showToast("Failed to add entries", "finish", "danger");
    }

	// // Add all temporary entries to the main data array
	// data.push(...tempEntries);

	// // Refresh the DataTable
	// table.clear().rows.add(data).draw();


	// Close modal and reset
	$("#addEntriesModal").modal("hide");
	tempEntries = [];
}

function handleProcessCheckboxChanges() {
	// Handle checkbox changes
	const process = this.dataset.process;
	const dateInput = document.querySelector(`#dateEntry${process.toLowerCase()}`);
	if (this.checked) {
		dateInput.removeAttribute("disabled");
		dateInput.classList.add("required");
	} else {
		dateInput.setAttribute("disabled", "");
		dateInput.classList.remove("required");
		dateInput.classList.remove("is-invalid");
	}
}
