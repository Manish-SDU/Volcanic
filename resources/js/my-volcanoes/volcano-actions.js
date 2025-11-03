document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('notifications-container');

    // Handle visited and wishlist buttons on home page
    const actionButtons = document.querySelectorAll('.visited-btn, .wishlist-btn');
    
    actionButtons.forEach(button => {
        button.addEventListener('click', async function(e) {
            e.preventDefault();
            
            const form = this.closest('form');
            const action = form.getAttribute('action');
            const csrfToken = form.querySelector('[name="_token"]').value;
            const volcanoCard = this.closest('.volcano-card');
            const visitedBtn = volcanoCard.querySelector('.visited-btn');
            const wishlistBtn = volcanoCard.querySelector('.wishlist-btn');
            
            try {
                const response = await fetch(action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showSuccessMessage(data.message);
                    
                    if (data.action === 'added') {
                        this.classList.add('active');
                        
                        if (this === visitedBtn) {
                            wishlistBtn.disabled = true;
                        }
                        if (this === wishlistBtn) {
                            visitedBtn.disabled = true;
                        }
                    } else if (data.action === 'removed') {
                        this.classList.remove('active');
                        
                        if (this === visitedBtn) {
                            wishlistBtn.disabled = false;
                        }
                        if (this === wishlistBtn) {
                            visitedBtn.disabled = false;
                        }
                    }
                } else {
                    showErrorMessage(data.error || 'Something went wrong');
                }
            } catch (error) {
                console.error('Error:', error);
                showErrorMessage('Error updating volcano status');
            }
        });
    });

    // Handle remove buttons on my-volcanoes page
    const removeButtons = document.querySelectorAll('.remove-btn');
    
    removeButtons.forEach(button => {
        button.addEventListener('click', async function(e) {
            e.preventDefault();
            
            const form = this.closest('form');
            const action = form.getAttribute('action');
            const csrfToken = form.querySelector('[name="_token"]').value;
            const volcanoCard = this.closest('.volcano-card');
            const gridContainer = volcanoCard.closest('.volcano-grid');
            
            try {
                const response = await fetch(action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showSuccessMessage(data.message);
                    
                    volcanoCard.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                    volcanoCard.style.opacity = '0';
                    volcanoCard.style.transform = 'scale(0.95)';
                    
                    setTimeout(() => {
                        volcanoCard.remove();
                        
                        // Check if list is now empty
                        if (gridContainer && gridContainer.children.length === 0) {
                            // Get the panel ID to determine which template to use
                            const panelId = gridContainer.closest('.myv-panel').id;
                            const templateId = panelId === 'visited' 
                                ? 'visited-empty-template' 
                                : 'wishlist-empty-template';
                            
                            const template = document.getElementById(templateId);
                            const emptyState = template.content.cloneNode(true);
                            
                            gridContainer.innerHTML = '';
                            gridContainer.appendChild(emptyState);
                        }
                    }, 300);
                } else {
                    showErrorMessage(data.error || 'Something went wrong');
                }
            } catch (error) {
                console.error('Error:', error);
                showErrorMessage('Error removing volcano');
            }
        });
    });

    // Helper function to show success messages
    function showSuccessMessage(message) {
        const template = document.getElementById('success-notification-template');
        const notification = template.content.cloneNode(true);
        notification.querySelector('.notification-message').textContent = message;
        
        container.appendChild(notification);
        const notificationEl = container.lastElementChild;
        setTimeout(() => notificationEl.remove(), 3000);
    }

    // Helper function to show error messages
    function showErrorMessage(message) {
        const template = document.getElementById('error-notification-template');
        const notification = template.content.cloneNode(true);
        notification.querySelector('.notification-message').textContent = message;
        
        container.appendChild(notification);
        const notificationEl = container.lastElementChild;
        setTimeout(() => notificationEl.remove(), 3000);
    }
});