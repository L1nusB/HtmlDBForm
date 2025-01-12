const processColumns = ['Process1', 'Process2', 'Process3'];


// Update the table HTML structure to be dynamic
function generateTableHeader() {
    return `
    <table id="dataTable" class="table">
        <thead>
            <tr>
                <th class="delete-checkbox-cell d-none border-0"></th>
                <th>Number</th>
                <th>Name</th>
                ${processColumns.map(col => `<th class="border-0">${col}</th>`).join('')}
                <th class="revert-cell d-none border-0">Revert</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>`;
}

// Update the add entries modal HTML to be dynamic
function generateAddEntriesModal() {
    return `
    <div class="modal fade" id="addEntriesModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Entries</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Temporary entries table -->
                    <div id="tempEntriesContainer" class="mb-4 d-none">
                        <h6>Entries to be added:</h6>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Number</th>
                                    <th>Name</th>
                                    ${processColumns.map(col => `<th class="text-center">${col}</th>`).join('')}
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="tempEntriesBody">
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Entry form -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="newEntryNumber" class="form-label">Number</label>
                            <input type="text" class="form-control" id="newEntryNumber" required>
                            <div class="invalid-feedback">Please enter a number.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="newEntryName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="newEntryName" required>
                            <div class="invalid-feedback">Please enter a name.</div>
                        </div>
                        ${processColumns.map(col => `
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input process-checkbox-new" id="newEntry${col}" data-process="${col.toLowerCase()}">
                                    <label class="form-check-label" for="newEntry${col}">${col}</label>
                                </div>
                            </div>
                        `).join('')}
                        <div class="col-12">
                            <button id="addToTempBtn" class="btn btn-primary">
                                <i class="bi bi-plus-lg"></i> Add Entry
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="saveNewEntriesBtn">Save All Entries</button>
                </div>
            </div>
        </div>
    </div>`;
}