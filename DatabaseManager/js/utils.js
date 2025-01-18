function showToast(message, isSuccess = true) {
    const toast = $('#saveToast');
    toast.removeClass('text-bg-success text-bg-info');
    toast.addClass(isSuccess ? 'text-bg-success' : 'text-bg-info');
    toast.find('.toast-body').text(message);
    
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
}

function deepEqual(obj1, obj2) {
    // Check if both are the same object reference
    if (obj1 === obj2) {
        return true;
    }

    // Check if either is null or not an object (base case)
    if (obj1 === null || obj2 === null || typeof obj1 !== 'object' || typeof obj2 !== 'object') {
        return false;
    }

    // Get all keys from both objects
    const keys1 = Object.keys(obj1);
    const keys2 = Object.keys(obj2);

    // Check if both objects have the same number of keys
    if (keys1.length !== keys2.length) {
        return false;
    }

    // Check each key and value recursively
    for (let key of keys1) {
        if (!keys2.includes(key) || !deepEqual(obj1[key], obj2[key])) {
            return false;
        }
    }

    return true;
}