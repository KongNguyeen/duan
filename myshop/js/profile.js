function toggleEdit() {
    document.querySelector('.profile-container').classList.toggle('edit-mode');
}
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const message = urlParams.get('message');
    
    if (message) {
        Swal.fire({
            title: 'Thông báo',
            text: message,
            icon: 'info',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn btn-success'
            }
        });
    }
});

function toggleEdit() {
    document.querySelector('.profile-container').classList.toggle('edit-mode');
}