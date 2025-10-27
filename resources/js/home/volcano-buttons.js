(function() {
    // Constants for localStorage keys where we store volcano IDs
    const VISITED_KEY = 'visitedVolcanoes';
    const WISHLIST_KEY = 'volcanoWishlist';

    function readList(key) {
        try {
            return JSON.parse(localStorage.getItem(key) || '[]');
        } catch {
            return [];
        }
    }

    function addToList(key, id) {
        const list = readList(key);
        if (!list.includes(id)) {
            list.push(id);
            localStorage.setItem(key, JSON.stringify(list));
        }
    }

    // Update buttons on volcano cards based on localStorage
    function initButtons() {
        document.querySelectorAll('.volcano-card').forEach(card => {
            const id = card.dataset.volcanoId;
            if (!id) return;

            // Visited button handler
            const visitedBtn = card.querySelector('.visited-btn');
            if (visitedBtn) {
                visitedBtn.addEventListener('click', () => {
                    addToList(VISITED_KEY, id);
                    // Remove from wishlist if present
                    const wishlist = readList(WISHLIST_KEY);
                    const wishIndex = wishlist.indexOf(id);
                    if (wishIndex > -1) {
                        wishlist.splice(wishIndex, 1);
                        localStorage.setItem(WISHLIST_KEY, JSON.stringify(wishlist));
                    }
                });
            }

            // Wishlist button handler
            const wishBtn = card.querySelector('.wishlist-btn');
            if (wishBtn) {
                wishBtn.addEventListener('click', () => {
                    // Don't add to wishlist if already visited
                    const visited = readList(VISITED_KEY);
                    if (visited.includes(id)) return;
                    
                    addToList(WISHLIST_KEY, id);
                });
            }
        });
    }

    // Initialize when DOM is loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initButtons);
    } else {
        initButtons();
    }
})();