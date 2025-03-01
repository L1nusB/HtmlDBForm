function createAssignmentRecord(combinations, testMode = false) {
    const data = {
        combinations: combinations // Array of objects
    };

    let url = './db/endpoints/create_assignments.php'; // Base URL
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
            showToast(`Successfully added ${data.rowsAffected} entries`, "finish", "success");
        } else if (data.status === "warning") {
            showToast(`Not all entries were added. Only ${data.rowsAffected} entries added.`, "finish", "warning");
        } else if (data.status === "test") {
            if (data.rowsAffected !== undefined) {
                alert("Test Mode: Would add " + data.rowsAffected + " record(s).");
                console.log("Would add: " + data.rowsAffected);
                showToast(`Would add ${data.rowsAffected} entries`, "finish", "success");
            } else {
                console.log("Test data:", data);
                alert("Test Mode: " + JSON.stringify(data));
                showToast(`Problem during test creation ${JSON.stringify(data)}`, "finish", "danger");
            }
        } else {
            console.error(JSON.stringify(data));
            showToast(`Failed to create entries ${data.message}`, "finish", "danger");
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("An error occurred during the request.");
    });
}