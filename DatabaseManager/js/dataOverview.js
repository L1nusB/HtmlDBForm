// Function to start the auto-reload
function startAutoReload(table, reloadInterval, RELOAD_TIME = 30000) {
    reloadInterval = setInterval(function() {
        table.ajax.reload(null, false);
    }, RELOAD_TIME);
    return reloadInterval;
}

// Function to pause auto-reload
function pauseAutoReload(reloadInterval) {
    if (reloadInterval) {
        clearInterval(reloadInterval);
        reloadInterval = null;
    }
    return reloadInterval;
}

// Function to resume auto-reload
function resumeAutoReload(reloadInterval) {
    if (!reloadInterval) {
        return startAutoReload();
    }
    return reloadInterval;
}

// Manual reload function if needed
function manualReload(table) {
    table.ajax.reload(null, false);
}