function openDateModal(volcanoId, currentDate, volcanoName) {
    console.log('Opening modal for:', volcanoName, 'Date:', currentDate, 'ID:', volcanoId);
    
    // Check if elements exist
    const form = document.getElementById('dateUpdateForm');
    const dateInput = document.getElementById('modal-visited-date');
    const nameElement = document.getElementById('modal-volcano-name');
    const modal = document.getElementById('dateModal');
    const backdrop = document.getElementById('modalBackdrop');
    
    console.log('Form:', form);
    console.log('Date input:', dateInput);
    console.log('Name element:', nameElement);
    console.log('Modal:', modal);
    console.log('Backdrop:', backdrop);
    
    if (!form || !dateInput || !nameElement || !modal || !backdrop) {
        console.error('Some modal elements are missing!');
        return;
    }
    
    // Set the form action
    form.action = `/user/volcanoes/${volcanoId}/update-date`;
    
    // Set the current date
    dateInput.value = currentDate;
    
    // Set the volcano name
    nameElement.textContent = volcanoName;
    
    // Show the modal
    modal.style.display = 'block';
    backdrop.style.display = 'block';
    
    console.log('Modal should be visible now');
    
    // Focus on the date input
    dateInput.focus();
}

function closeDateModal() {
    console.log('Closing modal');
    
    const modal = document.getElementById('dateModal');
    const backdrop = document.getElementById('modalBackdrop');
    
    if (modal) modal.style.display = 'none';
    if (backdrop) backdrop.style.display = 'none';
}

// Close modal when pressing Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeDateModal();
    }
});

// Make functions globally available
window.openDateModal = openDateModal;
window.closeDateModal = closeDateModal;

// Add this to test if the file is loaded
console.log('Edit date popup script loaded');