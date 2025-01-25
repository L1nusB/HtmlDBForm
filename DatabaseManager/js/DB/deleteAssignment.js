function deleteAssignmentRecord(combinations, testMode = false) {
    const data = {
        combinations: combinations // Array of objects
    };

    let url = './db/endpoints/delete_assignments.php'; // Base URL
    if (testMode) {
        url += '?test=1'; // Add test parameter if testMode is true
    }

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            showToast(`Successfully removed ${data.rowsAffected} entries`, "finish", "success");
        } else if (data.status === "test") {
            if (data.count !== undefined) {
                alert("Test Mode: Would delete " + data.rowsAffected + " record(s).");
                console.log("Would delete: " + data.rowsAffected);
                showToast(`Successfully removed ${data.rowsAffected} entries`, "finish", "success");
            } else {
                console.log("Test data:", data); // Log the full test data for debugging
                alert("Test Mode: " + JSON.stringify(data));
                showToast(`Problem during test deletion ${JSON.stringify(data)} entries`, "finish", "danger");
            }
        } else {
            console.error(JSON.stringify(data));
            showToast(`Failed to remove entries ${JSON.stringify(data)}`, "finish", "danger");
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("An error occurred during the request.");
    });
}