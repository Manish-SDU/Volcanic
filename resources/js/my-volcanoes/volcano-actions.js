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

    // Handle mark-visited buttons on my-volcanoes wishlist
    const markVisitedButtons = document.querySelectorAll('.mark-visited-btn');
   
    markVisitedButtons.forEach(button => {
        button.addEventListener('click', async function(e) {
            e.preventDefault();
           
            const form = this.closest('form');
            const action = form.getAttribute('action');
            const csrfToken = form.querySelector('[name="_token"]').value;
            const volcanoCard = this.closest('.volcano-card');
            const gridContainer = volcanoCard.closest('.volcano-grid');
           
            // Store volcano data before removing
            const volcanoId = volcanoCard.dataset.volcanoId;
            const volcanoName = volcanoCard.querySelector('h3').textContent;
            const volcanoCountry = volcanoCard.querySelector('.country').textContent.trim();
            const volcanoImage = volcanoCard.querySelector('.volcano-thumb').src;
           
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
                   
                    // Add to visited panel FIRST
                    addToVisitedPanel(volcanoId, volcanoName, volcanoCountry, volcanoImage, csrfToken);
                   
                    // Then fade out and remove from wishlist
                    volcanoCard.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                    volcanoCard.style.opacity = '0';
                    volcanoCard.style.transform = 'scale(0.95)';
                   
                    setTimeout(() => {
                        volcanoCard.remove();
                       
                        // Check if wishlist is now empty
                        if (gridContainer && gridContainer.children.length === 0) {
                            const template = document.getElementById('wishlist-empty-template');
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
                showErrorMessage('Error marking volcano as visited');
            }
        });
    });


    // Helper function to add volcano to visited panel
    function addToVisitedPanel(volcanoId, volcanoName, volcanoCountry, volcanoImage, csrfToken) {
        const visitedPanel = document.getElementById('visited');
        const visitedGrid = visitedPanel.querySelector('.volcano-grid');
        
        // Remove empty state if it exists
        const emptyState = visitedGrid.querySelector('.empty-state');
        if (emptyState) {
            emptyState.remove();
        }
        
        // Clone template
        const template = document.getElementById('volcano-card-template');
        const newCard = template.content.cloneNode(true);
        
        // Get the volcano card element from the cloned template
        const card = newCard.querySelector('.volcano-card');
        
        // Fill in data
        card.dataset.volcanoId = volcanoId;
        card.style.opacity = '0';
        card.style.transform = 'scale(0.95)';
        
        newCard.querySelector('.volcano-thumb').src = volcanoImage;
        newCard.querySelector('.volcano-thumb').alt = volcanoName;
        newCard.querySelector('h3').textContent = volcanoName;
        newCard.querySelector('.country-name').textContent = volcanoCountry;

        // Add current date
        const today = new Date();
        const dateText = today.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
        newCard.querySelector('.date-text').textContent = `Visited on ${dateText}`;

        // Set up edit date button
        const editBtn = newCard.querySelector('.date-edit-btn');
        const todayFormatted = today.toISOString().split('T')[0]; // YYYY-MM-DD format
        editBtn.onclick = function() {
            openDateModal(volcanoId, todayFormatted, volcanoName);
        };
        
        newCard.querySelector('.remove-form').action = `/user/volcanoes/${volcanoId}/visited`;
        newCard.querySelector('.remove-form input[name="_token"]').value = csrfToken;
        
        // Add to grid
        visitedGrid.appendChild(newCard);
        
        // Animate in
        setTimeout(() => {
            const addedCard = visitedGrid.querySelector(`[data-volcano-id="${volcanoId}"]`);
            if (addedCard) {
                addedCard.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                addedCard.style.opacity = '1';
                addedCard.style.transform = 'scale(1)';
            }
        }, 10);
        
        // Attach event listener to the new remove button
        const removeBtn = newCard.querySelector('.remove-btn');
        if (removeBtn) {
            attachRemoveButtonListener(removeBtn);
        }
    }


    // Helper function to attach remove button listener
    function attachRemoveButtonListener(button) {
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
                       
                        if (gridContainer && gridContainer.querySelectorAll('.volcano-card').length === 0) {
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
    }


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