function createAssignmentRecord(combinations, testMode = false) {
    console.log("Creating assignment record(s):", combinations);
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
        } else if (data.status === "test") {
            if (data.count !== undefined) {
                alert("Test Mode: Would add " + data.count + " record(s).");
                console.log("Would add: " + data.count);
                showToast(`Would add ${data.count} entries`, "finish", "success");
            } else {
                console.log("Test data:", data);
                alert("Test Mode: " + JSON.stringify(data));
                showToast(`Problem during test creation ${JSON.stringify(data)}`, "finish", "danger");
            }
        } else {
            console.error(JSON.stringify(data));
            showToast(`Failed to create entries ${JSON.stringify(data)}`, "finish", "danger");
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("An error occurred during the request.");
    });
}