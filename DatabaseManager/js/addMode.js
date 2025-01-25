function enterAddMode() {
	tempEntries = [];
	data = table.rows().data().toArray();
	resetEntryForm();
	updateTempEntriesTable();
	$("#addEntriesModal").modal("show");
}

// Function to reset the entry form on Cancel or Save (Exit Modal)
function resetEntryForm() {
	$("#newEntryRZBK").val("");
	$("#newEntryName").val("");
	// Trigger change to update Select2
	$("#newEntryInstitute").val("").trigger('change').removeClass("is-invalid");
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
	// Reset all location selectors
	document.querySelectorAll(".newEntryLocation").forEach((locationSelect) => {
		locationSelect.value = "";
		locationSelect.setAttribute("disabled", "");
		locationSelect.classList.remove("is-invalid");
	});
	// The main location selector needs to be reeanbled
	$("#newEntryLocationUniform").prop("disabled", false);
	// Reset uniform location toggle to being active
	$("#toggleUniformLocation").prop("checked", true);
	processNames.forEach((col) => {
		$(`#newEntry${col}`).prop("checked", false);
	});
}

// Function to validate the entry form
function validateEntryForm() {
	let isValid = true;

	if (!$("#newEntryInstitute").val().trim()) {
		$("#newEntryInstitute").addClass("is-invalid");
		isValid = false;
	} else {
		$("#newEntryInstitute").removeClass("is-invalid");
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

	// Check all active location selectors have a valid location
	document.querySelectorAll(".newEntryLocation").forEach((locationSelect) => {
		const disabled = locationSelect.disabled;
		if (!disabled && !locationSelect.value) {
			locationSelect.classList.add("is-invalid");
			isValid = false;
		} else {
			locationSelect.classList.remove("is-invalid");
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
                <i data-bs-toggle="tooltip" title="${
									entry.processes[col.toLowerCase()] ? entry.processes[col.toLowerCase()].startDate : ""
								}" class="bi ${entry.processes[col.toLowerCase()] ? "bi-check-lg text-success" : "bi-x-lg text-danger"}"></i>
            </td>
        `
			)
			.join("");

		tbody.append(`
            <tr>
                <td>${entry.rzbk}</td>
                <td>${entry.name}</td>
                <td>${resolveLocation(entry.location)}</td>
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

    const uniformLocation = $('#toggleUniformLocation').is(':checked');
    const instituteId = Number($("#newEntryInstitute").val().trim());
    const rzbk = Number($("#newEntryRZBK").val().trim());
    const name = $("#newEntryName").val().trim();

    const newEntries = {};

    dateFormatOptions = {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
    };

    processNames.forEach((col) => {
        const processChecked = $(`#newEntry${col.toLowerCase()}`).prop("checked");
        if (processChecked) {
            const processDate = new Date($(`#dateEntry${col.toLowerCase()}`).val()).toLocaleDateString("de-DE", dateFormatOptions);
            const processLocation = uniformLocation ? Number($("#newEntryLocationUniform").val()) : Number($(`#newEntryLocation${col.toLowerCase()}`).val());

            const compositeKey = `${instituteId}_${processLocation}`;
            if (!newEntries[compositeKey]) {
                newEntries[compositeKey] = {
                    fk_RPA_Bankenuebersicht: instituteId,
                    rzbk: rzbk,
                    name: name,
                    location: processLocation,
                    processes: {}
                };
            }

            newEntries[compositeKey].processes[col.toLowerCase()] = {
                checked: processChecked,
                startDate: processDate
            };
        }
    });

    Object.values(newEntries).forEach(entry => {
        tempEntries.push(entry);
    });

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

	tempEntries.forEach((entry) => {
		// Check if entry already exists and update it otherwise add it
		const existingEntryIndex = data.findIndex(
			(item) => item.fk_Bankenuebersicht === entry.fk_RPA_Bankenuebersicht && item.fk_Location === entry.location
		);

		if (existingEntryIndex !== -1) {
			// Update existing entry
			const existingEntry = data[existingEntryIndex];
			processNames.forEach((process) => {
				const processKey = process.toLowerCase();
				if (entry.processes[processKey]) {
					existingEntry[process] = {
						checked: entry.processes[processKey].checked,
						startDate: entry.processes[processKey].startDate
					};
				}
			});
		} else {
			// Add new entry
			const formattedEntry = {
				RZBK: Number(entry.rzbk),
				Name: entry.name,
				Standort: locationMapping[entry.location],
				fk_Bankenuebersicht: entry.fk_RPA_Bankenuebersicht,
				fk_Location: entry.location,
				...processNames.reduce((acc, process) => {
					const processKey = process.toLowerCase();
					acc[process] = {
						checked: entry.processes[processKey]?.checked || false,
						startDate: entry.processes[processKey]?.startDate || ''
					};
					return acc;
				}, {})
			};
			insertSorted(data, formattedEntry);
		}
	});

	// Refresh the DataTable
	table.clear().rows.add(data).draw();

	try {
		// Show success message
		showToast(`Successfully added ${tempEntries.length} new entries`, "finish", "success");
	} catch (error) {
		showToast("Failed to add entries", "finish", "danger");
		console.log(error);
	}

	// Close modal and reset
	$("#addEntriesModal").modal("hide");
	tempEntries = [];
}

function toggleUniformLocation() {
	const uniformLocation = $('#toggleUniformLocation').is(':checked');
	if (uniformLocation) {
		// Disable all location selectors except the main one
		document.querySelectorAll(".newEntryLocation").forEach((locationSelect) => {
			if (locationSelect.id !== "newEntryLocationUniform") {
				locationSelect.setAttribute("disabled", "");
			} else if (locationSelect.id === "newEntryLocationUniform") {
				locationSelect.removeAttribute("disabled");
			}
		});
	} else {
		// Enable all location selectors except the main one
		document.querySelectorAll(".newEntryLocation").forEach((locationSelect) => {
			if (locationSelect.id !== "newEntryLocationUniform") {
				// locationSelect.removeAttribute("disabled");
				document.querySelectorAll(".process-checkbox-new:checked").forEach((checkbox) => {
					const process = checkbox.dataset.process;
					const locationSelect = document.querySelector(`#newEntryLocation${process.toLowerCase()}`);
					locationSelect.removeAttribute("disabled");
				});
			} else if (locationSelect.id === "newEntryLocationUniform") {
				locationSelect.setAttribute("disabled", "");
			}
		});
	}
}

function updateAllLocations() {
	const curLocation = document.getElementById("newEntryLocationUniform").value;
	const uniformLocation = $('#toggleUniformLocation').is(':checked');
	if (uniformLocation) {
		document.querySelectorAll(".newEntryLocation").forEach((locationSelect) => {
			if (locationSelect.id !== "newEntryLocationUniform") {
				locationSelect.value = curLocation;
			}
		});
	}
}

function handleProcessCheckboxChanges() {
	const uniformLocation = $('#toggleUniformLocation').is(':checked');
	// Handle checkbox changes
	const process = this.dataset.process;
	const dateInput = document.querySelector(`#dateEntry${process.toLowerCase()}`);
	const locationSelect = document.querySelector(`#newEntryLocation${process.toLowerCase()}`);
	if (this.checked) {
		dateInput.removeAttribute("disabled");
		dateInput.classList.add("required");
		if (!uniformLocation) {
			locationSelect.removeAttribute("disabled");
			locationSelect.classList.add("required");
		}
	} else {
		dateInput.setAttribute("disabled", "");
		dateInput.classList.remove("required");
		dateInput.classList.remove("is-invalid");

		locationSelect.setAttribute("disabled", "");
		locationSelect.classList.remove("required");
		locationSelect.classList.remove("is-invalid");
	}
}
