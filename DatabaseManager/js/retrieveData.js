function prepareDatabaseData(json) {
    // First, group by combination of bank and location
    const groupedData = {};
    json.forEach(item => {
        // Create a composite key using both bank ID and location
        const compositeKey = `${item.fk_RPA_Bankenuebersicht}_${item.fk_RPA_Standort}`;
        
        if (!groupedData[compositeKey]) {
            groupedData[compositeKey] = {
                RZBK: item.RZBK,
                Name: item.Name,
                bankId: item.fk_RPA_Bankenuebersicht,
                location: item.fk_RPA_Standort,
                processes: {},
            };
        }
        
        // Only add processes that match the location
        if (item.fk_RPA_Standort === groupedData[compositeKey].location) {
            groupedData[compositeKey].processes[item.Prozessname] = {startDate : item.ProduktionsStart || null,
                                                                    fk_Prozess_ID : item.fk_RPA_Prozesse
            };
        }
    });

    // Transform the grouped data into the final format
    return Object.values(groupedData).map(row => {
        const newRow = {
            RZBK: row.RZBK,
            Name: row.Name,
            Standort: locationMapping[row.location] || 'unknown',
            fk_Location: row.location,
            fk_Bankenuebersicht: row.bankId,
        };

        // Add process information
        processNames.forEach(process => {
            newRow[process] = {
                checked: row.processes[process] ? true : false,
                startDate: row.processes[process] ? row.processes[process]['startDate'] : '',
                processID: row.processes[process] ? row.processes[process]['fk_Prozess_ID'] : null,
            };
        });

        return newRow;
    });
}