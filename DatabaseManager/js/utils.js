function showToast(message, type = "misc", result = "info") {
	let toast;
	switch (type) {
		case "start":
			toast = $("#startToast");
			break;
		case "finish":
			toast = $("#finishToast");
			break;
		default:
			toast = $("#miscToast");
			break;
	}
	toast.removeClass("text-bg-success text-bg-info text-bg-danger text-bg-warning");
	switch (result) {
		case "success":
			toast.addClass("text-bg-success");
			break;
		case "danger":
			toast.addClass("text-bg-danger");
			break;
		case "warning":
			toast.addClass("text-bg-warning");
			break;
		default:
			toast.addClass("text-bg-info");
			break;
	}
	toast.find(".toast-body").text(message);

	const bsToast = new bootstrap.Toast(toast);
	bsToast.show();
}

function deepEqual(obj1, obj2) {
	// Check if both are the same object reference
	if (obj1 === obj2) {
		return true;
	}

	// Check if either is null or not an object (base case)
	if (obj1 === null || obj2 === null || typeof obj1 !== "object" || typeof obj2 !== "object") {
		return false;
	}

	// Get all keys from both objects
	const keys1 = Object.keys(obj1);
	const keys2 = Object.keys(obj2);

	// Check if both objects have the same number of keys
	if (keys1.length !== keys2.length) {
		return false;
	}

	// Check each key and value recursively
	for (let key of keys1) {
		if (!keys2.includes(key) || !deepEqual(obj1[key], obj2[key])) {
			return false;
		}
	}

	return true;
}

// Define a function to remove specified elements from an array
function exclude(array, exclude) {
    let excludeSet = new Set(exclude);
    return array.filter(item => !excludeSet.has(item));
}

// Symmetric set difference between two arrays
function difference(array1, array2) {
    let set1 = new Set(array1);
    let set2 = new Set(array2);

    // Elements in array1 but not in array2
    let uniqueToSet1 = array1.filter(item => !set2.has(item));
    // Elements in array2 but not in array1
    let uniqueToSet2 = array2.filter(item => !set1.has(item));

    // Combine the unique elements from both sets
    return [...uniqueToSet1, ...uniqueToSet2];
}

function resolveLocation(locationId) {
	// Resolve the location ID to the location name
	return locationMapping[locationId];
}

function formatDateStringToISO(dateString) {
	if (!dateString) return "";
	/* Convert a date string from dd.MM.yyyy to yyyy-MM-dd */
	
	// Split the string into day, month, and year
	const [day, month, year] = dateString.split(".");
	
	// Convert to the new format yyyy-MM-dd
	const formattedDate = `${year}-${month}-${day}`;
	return formattedDate;
}
function formatDateStringFromISO(dateString) {
	if (!dateString) return "";
	/* Convert a date string from yyyy-MM-dd to dd.MM.yyyy*/

	// Split the string into day, month, and year
	const [year, month, day] = dateString.split("-");

	// Convert to the new format yyyy-MM-dd
	const formattedDate = `${day}.${month}.${year}`;
	return formattedDate;
}

function handlePageChange() {
	if (deleteMode) {
		// Ensure all checkboxes are shown on page change (requires reload once)
		// After first reload, the checkboxes will be shown on page change regardless
		// Use timeout because the initial <td> elements are not yet created
		setTimeout(() => {
			$(".delete-checkbox-cell").removeClass("d-none");
			table.draw("page");
		}, 20);
	} else {
		exitDeleteMode();
	}
	if (editMode) {
		// Ensure all fields are editable
		enableEditFields();
	} else {
		// Ensure all fields not editable when not in Edit Mode
		exitEditMode();
	}
}

function handleOrderingChange() {
	console.log("Ordering changed");
	if (deleteMode) {
		// Ensure all checkboxes are shown on ordering change (requires reload once)
		// After first reload, the checkboxes will be shown on ordering change regardless
		// Use timeout because the initial <td> elements are not yet created
		setTimeout(() => {
			$(".delete-checkbox-cell").removeClass("d-none");
			table.draw("page");
		}, 20);
	} else {
		disableFields();
        toggleButtonsDelete(false);
	}
	if (editMode) {
		// Ensure all fields are editable
		// Use timeout because the initial <td> elements are not yet created
		setTimeout(() => {
			enableEditFields();
			table.draw("page");
		}, 20);
	} else {
		// Ensure all fields not editable when not in Edit Mode
		disableEditFields();
        toggleButtonsEdit(false);
	}
}

// Binary search to find insertion point
function insertSorted(arr, newElement, property = "RZBK") {
    let left = 0;
    let right = arr.length - 1;
    
    // Binary search to find insertion point
    while (left <= right) {
        const mid = Math.floor((left + right) / 2);
        if (arr[mid][property] === newElement[property]) {
            left = mid;
            break;
        }
        if (arr[mid][property] < newElement[property]) {
            left = mid + 1;
        } else {
            right = mid - 1;
        }
    }
    
    // Insert at the found position
    arr.splice(left, 0, newElement);
    return arr;
}

// Add new function for refreshing locations
async function refreshLocations() {
    try {
        const response = await fetch('./db/refresh_locations.php');
        if (!response.ok) throw new Error('Network response was not ok');
        const data = await response.json();
        
        if (data.success) {
            locationMapping = data.locations;
            showToast('Locations have been updated', 'refresh', 'success');
        } else {
            throw new Error(data.error || 'Unknown error occurred');
        }
    } catch (error) {
        console.error('Error refreshing locations:', error);
        showToast('Error updating locations', 'error', 'danger');
    }
}

// Add new function for refreshing processes
async function refreshProcesses() {
    try {
        const response = await fetch('./db/refresh_processes.php');
        if (!response.ok) throw new Error('Network response was not ok');
        const data = await response.json();
        
        if (data.success) {
            processMapping = data.processes;
            processNames = data.processNames;
            
            // Update process columns in table
            updateProcessColumns();
            
            showToast('Processes have been updated', 'refresh', 'success');
        } else {
            throw new Error(data.error || 'Unknown error occurred');
        }
    } catch (error) {
        console.error('Error refreshing processes:', error);
        showToast('Error updating processes', 'error', 'danger');
    }
}

// Helper function to update process columns
function updateProcessColumns() {
    const table = $('#institutesTable').DataTable();
    
    // Store current column visibility states
    const visibilityStates = {};
    table.columns().every(function() {
        const columnHeader = this.header().textContent;
        visibilityStates[columnHeader] = this.visible();
    });

    // Clear existing process checkboxes in menu
    $('#processMenu li:not(:first-child)').remove();

    // Add new process checkboxes to menu
    processNames.forEach(process => {
        $('#processMenu').append(`
            <li class="dropdown-item form-check">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="${process}" 
                        id="check-${process}" ${visibilityStates[process] !== false ? 'checked' : ''}>
                    <label class="form-check-label" for="check-${process}">${process}</label>
                </div>
            </li>
        `);
    });

    // Redraw table with updated columns
    table.destroy();
    $('#institutesTable thead tr th:gt(3)').remove(); // Remove all process columns
    
    // Add new process columns
    processNames.forEach(process => {
        $('#institutesTable thead tr').append(`<th>${process}</th>`);
    });

    // Reinitialize table
    // Note: You'll need to reinitialize with the same configuration as before
    // This might need to be adjusted based on your specific needs
    initializeTable();
}

// Add function for table initialization with the same configuration as in index.php
function initializeTable() {
    table = $('#institutesTable').DataTable({
        ajax: {
            url: './db/get_data.php',
            dataSrc: prepareDatabaseData,
        },
        columns: [{
                data: null,
                title: '#',
                orderable: false,
                className: 'delete-checkbox-cell d-none',
                visible: false,
                render: function(data, type, row, meta) {
                    return '<input type="checkbox" class="delete-checkbox" data-row="' + meta.row + '">';
                },
                width: '40px'
            },
            {
                data: 'RZBK',
                title: 'RZBK'
            },
            {
                data: 'Name',
                title: 'Name'
            },
            {
                data: 'Standort',
                title: 'Standort',
                orderable: false,
            },
            ...processNames.map(process => ({
                data: process,
                className: 'checkbox-cell text-center',
                orderable: false,
                render: function(data, type, row, meta) {
                    const showDates = $('#toggleDates').is(':checked');
                    const date = data?.startDate;
                    const enabled = data?.checked;

                    return `
                        <div class="d-flex flex-column flex-lg-row align-items-center justify-content-center gap-1">
                            <input type="checkbox" ${enabled ? 'checked' : ''} disabled 
                                class="mb-1 mb-lg-0 mr-lg-2 process-checkbox" 
                                data-process="${process}" data-row="${meta.row}"
                                data-toggle="tooltip" title="${date ? date : ''}">
                            ${showDates ? 
                                `<input type="date" style="width: 105px;" 
                                value="${formatDateStringToISO(date)}" 
                                class="form-control form-control-sm process-date-input" 
                                data-process="${process}" data-row="${meta.row}"
                                disabled>` 
                                : ''
                            }
                            <i class="bi bi-pencil edit-field-btn d-none" data-process="${process}" data-row="${meta.row}"></i>
                        </div>
                    `;
                }
            })),
            {
                data: null,
                title: 'Revert',
                className: 'revert-cell d-none',
                orderable: false,
                visible: false,
                render: function(data, type, row, meta) {
                    return '<i class="bi bi-trash revert-btn" data-row="' + meta.row + '"></i>';
                }
            }
        ],
        order: [[1, 'asc']],
        ordering: false,
        layout: {
            topStart: ['pageLength'],
            topEnd: ['buttons', 'search'],
        },
        buttons: [{
            text: '<i class="bi bi-arrow-clockwise"></i> Refresh',
            action: async function(e, dt, node, config) {
                await Promise.all([
                    refreshInstitutes(),
                    refreshLocations(),
                    refreshProcesses()
                ]);
                dt.ajax.reload();
            },
        }, {
            extend: 'colvis',
            columns: ':not(.delete-checkbox-cell):not(.revert-cell)',
            text: 'Visibility',
            className: 'btn-secondary'
        }],
        initComplete: function(settings, json) {
            data = this.api().rows().data().toArray();
            
            // Reinitialize tooltips
            $('[data-toggle="tooltip"]').tooltip();
            
            // Restore edit/delete mode if active
            if (editMode) enableEditFields();
            if (deleteMode) $('.delete-checkbox-cell').removeClass('d-none');
        }
    });

    // (Re-)Enable the buttons after the table is initialized
	enableButtons();

    table.on('page', handlePageChange);

    return table;
}
