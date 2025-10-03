// admin_panel.js - JavaScript for admin panel functionality

function confirmDelete(type, id, name) {
    const modal = document.getElementById('delete-modal');
    const modalTitle = document.getElementById('modal-title');
    const modalMessage = document.getElementById('modal-message');
    const confirmBtn = document.getElementById('confirm-delete-btn');
    
    modalTitle.textContent = `Usuń ${type}`;
    modalMessage.textContent = `Czy na pewno chcesz usunąć ${type}: "${name}"? Ta akcja nie może być cofnięta.`;
    
    confirmBtn.onclick = function() {
        if (type === 'projekt') {
            window.location.href = `delete_project.php?id=${id}`;
        } else if (type === 'pracownika') {
            window.location.href = `delete_employee.php?id=${id}`;
        }
    };
    
    modal.style.display = 'flex';
}

function closeModal() {
    const modal = document.getElementById('delete-modal');
    modal.style.display = 'none';
}

// Close modal when clicking outside of it
window.onclick = function(event) {
    const modal = document.getElementById('delete-modal');
    if (event.target === modal) {
        closeModal();
    }
}

// Close modal on Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal();
    }
});