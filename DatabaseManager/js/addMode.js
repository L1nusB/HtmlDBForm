function enterAddMode() {
	tempEntries = [];
	resetEntryForm();
	updateTempEntriesTable();
	$("#addEntriesModal").modal("show");
}

// Function to reset the entry form on Cancel or Save (Exit Modal)
function resetEntryForm() {
	$("#newEntryNumber").val("").removeClass("is-invalid");
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

	if (!$("#newEntryNumber").val().trim()) {
		$("#newEntryNumber").addClass("is-invalid");
		isValid = false;
	} else {
		$("#newEntryNumber").removeClass("is-invalid");
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
		console.log(entry);
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

	$("#tempEntriesContainer").toggleClass("d-none", tempEntries.length === 0);
}

function addToTempEntries() {
	if (!validateEntryForm()) return;

	const newEntry = {
		number: $("#newEntryNumber").val().trim(),
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
		showToast("No entries to save", false);
		return;
	}

	// Add all temporary entries to the main data array
	data.push(...tempEntries);

	// Refresh the DataTable
	table.clear().rows.add(data).draw();

	// Show success message
	showToast(`Successfully added ${tempEntries.length} new entries`, true);

	// Close modal and reset
	$("#addEntriesModal").modal("hide");
	tempEntries = [];
}

function handleProcessCheckboxChanges() {
	// Handle checkbox changes
	const process = this.dataset.process;
	const dateInput = document.querySelector(`#dateEntry${process.toLowerCase()}`);
    console.log(process);
    console.log(process.charAt(0).toUpperCase());
    console.log(process.slice(1));
    console.log(dateInput);
	if (this.checked) {
		dateInput.removeAttribute("disabled");
		dateInput.classList.add("required");
	} else {
		dateInput.setAttribute("disabled", "");
		dateInput.classList.remove("required");
		dateInput.classList.remove("is-invalid");
	}
}
