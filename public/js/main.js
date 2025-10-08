/* ===========================================
   🌋 VOLCANIC - COMMON JAVASCRIPT LOGIC 🌋 
   =========================================== */

/**
 * Year updating for the footer
 */
function updateYear() {
    const currentYearElement = document.getElementById('currentYear');
    if (currentYearElement) {
        const currentYear = new Date().getFullYear();
        currentYearElement.textContent = currentYear;
    }
}

/**
 * Immediate image loading for instant page transitions
 */
function initializeLazyLoading() {
    // Preload all images immediately for instant navigation experience
    const loadAllImages = () => {
        document.querySelectorAll('img[data-src]').forEach(img => {
            const src = img.dataset.src;
            const placeholder = img.dataset.placeholder;
            
            if (src) {
                // Create a new image to preload
                const preloadImg = new Image();
                
                preloadImg.onload = () => {
                    // Set the actual image source immediately
                    img.src = src;
                    img.classList.add('image-loaded');
                    img.removeAttribute('data-src');
                    img.removeAttribute('data-placeholder');
                };
                
                preloadImg.onerror = () => {
                    // Fallback to placeholder if actual image fails
                    if (placeholder) {
                        img.src = placeholder;
                        img.classList.add('placeholder-shown');
                    }
                    img.removeAttribute('data-src');
                };
                
                // Start preloading immediately
                preloadImg.src = src;
            }
        });
    };
    
    // Load all images immediately when the page loads
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadAllImages);
    } else {
        loadAllImages();
    }
}

/**
 * Initialize volcano card interactions
 */
function initializeVolcanoCards() {
    // Handle visited button clicks
    document.querySelectorAll('.visited-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const volcanoId = this.dataset.volcano;
            const card = this.closest('.volcano-card');
            
            this.classList.toggle('active');
            card.classList.toggle('visited');
            
            // Store in localStorage
            const visited = JSON.parse(localStorage.getItem('visitedVolcanoes') || '[]');
            if (this.classList.contains('active')) {
                if (!visited.includes(volcanoId)) {
                    visited.push(volcanoId);
                }
                showNotification('Volcano marked as visited!', 'success');
            } else {
                const index = visited.indexOf(volcanoId);
                if (index > -1) {
                    visited.splice(index, 1);
                }
                showNotification('Removed from visited list', 'info');
            }
            localStorage.setItem('visitedVolcanoes', JSON.stringify(visited));
        });
    });

    // Handle wishlist button clicks
    document.querySelectorAll('.wishlist-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const volcanoId = this.dataset.volcano;
            const card = this.closest('.volcano-card');
            
            this.classList.toggle('active');
            card.classList.toggle('wishlisted');
            
            // Store in localStorage
            const wishlist = JSON.parse(localStorage.getItem('volcanoWishlist') || '[]');
            if (this.classList.contains('active')) {
                if (!wishlist.includes(volcanoId)) {
                    wishlist.push(volcanoId);
                }
                showNotification('Added to wishlist!', 'success');
            } else {
                const index = wishlist.indexOf(volcanoId);
                if (index > -1) {
                    wishlist.splice(index, 1);
                }
                showNotification('Removed from wishlist', 'info');
            }
            localStorage.setItem('volcanoWishlist', JSON.stringify(wishlist));
        });
    });

    // Handle learn more button clicks
    document.querySelectorAll('.learn-more-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const volcanoId = this.dataset.volcano;
            showNotification('Learn more feature coming soon!', 'info');
            // Here you could redirect to a detailed page
            // window.location.href = `/volcano/${volcanoId}`;
        });
    });

    // Load saved states
    loadSavedStates();
}

/**
 * Load saved visited and wishlist states from localStorage
 */
function loadSavedStates() {
    const visited = JSON.parse(localStorage.getItem('visitedVolcanoes') || '[]');
    const wishlist = JSON.parse(localStorage.getItem('volcanoWishlist') || '[]');

    visited.forEach(volcanoId => {
        const btn = document.querySelector(`.visited-btn[data-volcano="${volcanoId}"]`);
        const card = document.querySelector(`.volcano-card[data-volcano-id="${volcanoId}"]`);
        if (btn && card) {
            btn.classList.add('active');
            card.classList.add('visited');
        }
    });

    wishlist.forEach(volcanoId => {
        const btn = document.querySelector(`.wishlist-btn[data-volcano="${volcanoId}"]`);
        const card = document.querySelector(`.volcano-card[data-volcano-id="${volcanoId}"]`);
        if (btn && card) {
            btn.classList.add('active');
            card.classList.add('wishlisted');
        }
    });
}

/**
 * Show notification to user
 */
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existing = document.querySelector('.notification');
    if (existing) {
        existing.remove();
    }

    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <span>${message}</span>
            <button class="notification-close">&times;</button>
        </div>
    `;

    document.body.appendChild(notification);

    // Auto remove after 3 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 3000);

    // Close button functionality
    notification.querySelector('.notification-close').addEventListener('click', () => {
        notification.remove();
    });
}

function initializeCommonFeatures() {
    updateYear();
    initializeLazyLoading();
    initializeVolcanoCards();
}

document.addEventListener('DOMContentLoaded', function() {
    initializeCommonFeatures();
});

window.VolcanicCommon = {
    updateYear: updateYear,
    initializeLazyLoading: initializeLazyLoading,
    initializeCommonFeatures: initializeCommonFeatures
};