function deleteRecord(institute, location) {
    const data = {
        fk_RPA_Bankenuebersicht: institute,
        fk_RPA_Standort: location
    };

    fetch('./db/endpoints/delete_assignments.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json' // Set content type to JSON
        },
        body: JSON.stringify(data) // Convert data to JSON string
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            alert(data.message);
            console.log("Rows affected: " + data.rowsAffected);
        } else {
            alert("Error: " + data.message);
            console.error(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("An error occurred during the request.");
    });
}