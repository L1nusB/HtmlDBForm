function updateAssignmentRecord(updateSummary, testMode = false) {
	console.log("Updating assignments:", updateSummary);
	const data = {
		updateSummary: updateSummary, // Array of objects
	};

	let url = "./db/endpoints/update_assignments.php"; // Base URL
	if (testMode) {
		url += "?test=1"; // Add test parameter if testMode is true
	}

	fetch(url, {
		method: "POST",
		headers: {
			"Content-Type": "application/json",
		},
		body: JSON.stringify(data),
	})
		.then((response) => response.json())
		.then((data) => {
			console.log("Data results from DB:", data);
			if (data.status === "success") {
				showToast(
					`Successfully modified ${data.total} entries. ${data.inserts} insertions, 
                    ${data.updates} updates, ${data.deletions} deletions.`,
					"finish",
					"success"
				);
			} else if (data.status === "test") {
				if (data.success) {
					alert(`Test Mode: Would modified ${data.total} record(s). `+
                        `${data.inserts} insertions, ${data.updates} updates, ${data.deletions} deletions.`);
					console.log(`Would update ${data.total} record(s). ` + 
                        `${data.inserts} insertions,  ${data.updates} updates, ${data.deletions} deletions.`);
					showToast(
						`Successfully modified ${data.total} entries. `+
                        `${data.inserts} insertions, ${data.updates} updates, ${data.deletions} deletions.`,
						"finish",
						"success"
					);
				} else {
					console.log("Test data:", data); // Log the full test data for debugging
					alert("Test Mode: " + JSON.stringify(data));
					showToast(`Problem during test update ${JSON.stringify(data)}`, "finish", "danger");
				}
			} else {
				console.error(JSON.stringify(data));
				showToast(`Failed to update entries ${JSON.stringify(data)}`, "finish", "danger");
			}
		})
		.catch((error) => {
			console.error("Error:", error);
			alert("An error occurred during the request.");
		});
}
