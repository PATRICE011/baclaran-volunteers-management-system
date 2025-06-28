let currentVolunteerId = null;

function openArchiveModal(id) {
    currentVolunteerId = id;
    const modal = document.getElementById('archive-modal');
    modal.classList.add('active');
    document.getElementById('archive-reason').value = '';
}

function closeArchiveModal() {
    const modal = document.getElementById('archive-modal');
    modal.classList.remove('active');
}

document.getElementById('cancel-archive').addEventListener('click', closeArchiveModal);

document.getElementById('confirm-archive').addEventListener('click', function() {
    const reason = document.getElementById('archive-reason').value;
    if (!reason) {
        toastr.error('Please enter a reason');
        return;
    }

    fetch(`/volunteers/${currentVolunteerId}/archive`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ reason: reason })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Remove from UI
            const row = document.querySelector(`tr[data-id="${currentVolunteerId}"]`);
            if (row) row.remove();
            
            closeArchiveModal();
            toastr.success('Volunteer archived successfully');
            
            // Refresh the view after archiving
            applyFilters();
        } else {
            toastr.error(data.message || 'Failed to archive volunteer');
        }
    })
    .catch(error => {
        toastr.error(error.message || 'An error occurred. Please try again.');
    });
});