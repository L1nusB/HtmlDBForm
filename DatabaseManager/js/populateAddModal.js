function generateAddModalTempTable() {
	return `<div id="tempEntriesContainer" class="mb-4 d-none">
                <h6>Entries to be added:</h6>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>RZBK</th>
                            <th>Name</th>
                            ${processNames.map(col => `<th class="text-center">${col}</th>`).join('')}
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="tempEntriesBody">
                    </tbody>
                </table>
            </div>`;
}

function generateAddModalEntryForm() {
    return `<div id="addModalProcessSelector" class="row mt-3">
                ${processNames.map(col => `
                <div class="col-md-6 mb-3">
                    <div class="row">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="form-check">
                                <input type="checkbox" 
                                    class="form-check-input process-checkbox-new" 
                                    id="newEntry${col.toLowerCase()}" 
                                    data-process="${col.toLowerCase()}">
                                <label class="form-check-label" for="newEntry${col.toLowerCase()}">${col}</label>
                            </div>
                            <input type="date" 
                                class="form-control date-input-new" 
                                style="width: 130px;"
                                id="dateEntry${col.toLowerCase()}"
                                data-process="${col.toLowerCase()}"
                                disabled 
                                required
                                novalidate>
                        </div>
                        <div class="invalid-feedback">
                            Please select a date.
                        </div>
                    </div>
                    <select class="form-select form-select-sm mt-2 newEntryLocation" id="newEntryLocation${col.toLowerCase()}" required disabled>
                        <option value="" selected disabled hidden>Wähle Standort</option>
                        ${Object.entries(locationMapping).map(([id,location]) => `<option value="${id}">${location}</option>`).join('')}
                    </select>
                    <div class="invalid-feedback">
                        Bitte wählen Sie einen Standort aus.
                    </div>
                </div>
                `).join('')}</div>`;
}

function generateLocationSelector() {
    return `<div id="locationSelector" class="row mt-3 align-items-center">
                <div class="col-md-6 mb-3">
                    <label for="newEntryLocation" class="form-label">Standort</label>
                    <select class="form-select newEntryLocation" id="newEntryLocationUniform" required>
                        <option value="" selected disabled hidden>Wähle Standort</option>
                        ${Object.entries(locationMapping).map(([id,location]) => `<option value="${id}">${location}</option>`).join('')}
                    </select>
                    <div class="invalid-feedback">
                        Bitte wählen Sie einen Standort aus.
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-check form-switch">
                        <input type="checkbox" role="switch" class="form-check-input" id="toggleUniformLocation" checked>
                        <label class="form-check-label" for="toggleUniformLocation">Einheitlicher Standort</label>
                    </div>
                </div>
            </div>`;
}

function popuplateAddModal() {
    // Setup table for temporary entries
    $('#tempEntriesContainer').replaceWith(generateAddModalTempTable());

    // Setup the entry form
    $('#addModalProcessSelector').replaceWith(generateAddModalEntryForm());

    // Setup location selector
    $('#locationSelector').replaceWith(generateLocationSelector());
}