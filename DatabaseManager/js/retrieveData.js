// function prepareDatabaseData(json) {
//     const groupedData = {};
//     json.forEach(item => {
//         const key = `${item.fk_RPA_Bankenuebersicht}`;
//         if (!groupedData[key]) {
//             groupedData[key] = {
//                 RZBK: item.RZBK,
//                 Name: item.Name,
//                 processes: {},
//                 Standort: locationAssignment[key] || 'unknown'
//             };
//         }
//         groupedData[key].processes[item.Prozessname] = item.ProduktionsStart || null;
//     });

//     return Object.values(groupedData).map(row => {
//         const newRow = {
//             RZBK: row.RZBK,
//             Name: row.Name,
//             Standort: row.Standort,
//         };
//         processNames.forEach(process => {
//             newRow[process] = {
//                 checked: row.processes[process] ? true : false,
//                 startDate: row.processes[process] || ''
//             };
//         });
//         return newRow;
//     });
// }

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
            groupedData[compositeKey].processes[item.Prozessname] = item.ProduktionsStart || null;
        }
    });

    // Transform the grouped data into the final format
    return Object.values(groupedData).map(row => {
        const newRow = {
            RZBK: row.RZBK,
            Name: row.Name,
            Standort: locationMapping[row.location] || 'unknown',
        };

        // Add process information
        processNames.forEach(process => {
            newRow[process] = {
                checked: row.processes[process] ? true : false,
                startDate: row.processes[process] || ''
            };
        });

        return newRow;
    });
}