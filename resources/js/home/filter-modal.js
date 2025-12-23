/**
 * Filter Modal Functionality
 * Handles the filter popup for volcano filtering
 */

document.addEventListener('DOMContentLoaded', function() {
    const filterBtn = document.getElementById('filter-pill');
    const filterModal = document.getElementById('filterModal');
    const closeBtn = filterModal?.querySelector('.close');
    const clearBtn = filterModal?.querySelector('.btn-clear');
    
    // Open modal when filter button is clicked
    if (filterBtn && filterModal) {
        filterBtn.addEventListener('click', function() {
            filterModal.style.display = 'block';
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        });
    }
    
    // Close modal when X button is clicked
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            closeModal();
        });
    }
    
    // Close modal when clicking outside the modal content
    if (filterModal) {
        filterModal.addEventListener('click', function(event) {
            if (event.target === filterModal) {
                closeModal();
            }
        });
    }
    
    // Clear all filters
    if (clearBtn) {
        clearBtn.addEventListener('click', function(e) {
            e.preventDefault();
            // Redirect to home without any query parameters
            window.location.href = clearBtn.href;
        });
    }
    
    // Close modal with ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && filterModal && filterModal.style.display === 'block') {
            closeModal();
        }
    });
    
    // Helper function to close modal
    function closeModal() {
        if (filterModal) {
            filterModal.style.display = 'none';
            document.body.style.overflow = ''; // Restore scrolling
        }
    }
});

