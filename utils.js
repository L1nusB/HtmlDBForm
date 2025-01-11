function showToast(message, isSuccess = true) {
    const toast = $('#saveToast');
    toast.removeClass('text-bg-success text-bg-info');
    toast.addClass(isSuccess ? 'text-bg-success' : 'text-bg-info');
    toast.find('.toast-body').text(message);
    
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
}