(function() {
    // Keys for localStorage
    const VISITED_KEY = 'visitedVolcanoes';
    const WISHLIST_KEY = 'volcanoWishlist';

    // Fetch volcano data from API based on IDs
    async function fetchVolcanoes(ids) {
        if (!ids.length) return [];
        try {
            const params = new URLSearchParams();
            params.set('visited', ids.join(','));
            params.set('wishlist', ids.join(','));
            
            const response = await fetch(`/api/volcanoes/lists?${params}`);
            console.log('API Response Status:', response.status);

            if (!response.ok) {
                throw new Error(`Network response was not ok: ${response.status}`);
            }

            const data = await response.json();
            console.log('API Response Data:', data);

            return data.visited || data.wishlist || [];
        } catch (error) {
            console.error('Error fetching volcanoes:', error);
            return [];
        }
    }

    // Supposed to determine the best image URL for a volcano
    function getImageUrl(volcano) {
        // Try the provided URLs first
        if (volcano.image_url) return volcano.image_url;
        if (volcano.safe_image_url) return volcano.safe_image_url;

        // Try different extensions
        const baseImagePath = `images/volcanoes/${volcano.id}`;
        const extensions = ['.jpg', '.jpeg', '.png'];
        
        // Return the first image that loads successfully
        return extensions.find(ext => {
            const img = new Image();
            img.src = baseImagePath + ext;
            return img.complete;
        }) || 'images/placeholder.png';
    }

    // Render volcano list in the specified container (visited or wishlist)
    function renderVolcanoList(container, volcanoes) {
        if (!container) {
            console.error('Container not found');
            return;
        }

        // Get the parent panel ID (either 'visited' or 'wish') to know which list we're working with
        const panelId = container.closest('.myv-panel').id;

        // Find the required elements within the container
        const volcanoesContainer = container.querySelector('.volcanoes-container');
        const emptyState = container.querySelector('.empty-state');
        const template = document.getElementById('volcano-card-template');

        if (!template) {
            console.error('Template not found');
            return;
        }

        if (!volcanoes || !volcanoes.length) {
            emptyState.style.display = 'block';
            volcanoesContainer.style.display = 'none';
            return;
        }

        emptyState.style.display = 'none';
        volcanoesContainer.style.display = 'block';
        volcanoesContainer.innerHTML = '';

        // Create and add a card for each volcano
        volcanoes.forEach(volcano => {
            const card = template.content.cloneNode(true).firstElementChild;
            const img = card.querySelector('img');
            const title = card.querySelector('h3');
            const country = card.querySelector('.country');
            const removeBtn = card.querySelector('.remove-btn');

            card.dataset.id = volcano.id;

            img.src = getImageUrl(volcano);
            img.alt = volcano.name;
            img.onerror = () => {
                console.warn(`Image failed to load for volcano ${volcano.name}:`, img.src);
                img.src = 'images/placeholder.png';
            };

            title.textContent = volcano.name;
            country.textContent = volcano.country || '';

            removeBtn.dataset.type = panelId;
            removeBtn.dataset.id = volcano.id;
            
            volcanoesContainer.appendChild(card);
        });
    }

    async function updatePanels() {
        // Get volcano IDs from localStorage for both lists
        const visitedIds = JSON.parse(localStorage.getItem(VISITED_KEY) || '[]');
        const wishlistIds = JSON.parse(localStorage.getItem(WISHLIST_KEY) || '[]');

        console.log('Updating panels with IDs:', { visited: visitedIds, wishlist: wishlistIds });

        // Find both panel containers in the DOM
        const visitedContainer = document.querySelector('#visited .volcano-grid');
        const wishContainer = document.querySelector('#wish .volcano-grid');

        if (!visitedContainer || !wishContainer) {
            console.error('Container(s) not found');
            return;
        }

        // Handle visited volcanoes panel
        if (visitedIds.length > 0) {
            const visitedVolcanoes = await fetchVolcanoes(visitedIds);
            renderVolcanoList(visitedContainer, visitedVolcanoes);
        } else {
            renderVolcanoList(visitedContainer, []);
        }

        // Handle wishlist panel
        if (wishlistIds.length > 0) {
            const wishlistVolcanoes = await fetchVolcanoes(wishlistIds);
            renderVolcanoList(wishContainer, wishlistVolcanoes);
        } else {
            renderVolcanoList(wishContainer, []);
        }

        // Update the counter showing number of visited volcanoes
        const visitedEl = document.getElementById('visited-value');
        if (visitedEl) visitedEl.textContent = visitedIds.length;
    }

    document.addEventListener('click', e => {
        const removeBtn = e.target.closest('.remove-btn');
        if (!removeBtn) return;
        
        const type = removeBtn.dataset.type;
        const id = removeBtn.dataset.id;
        const key = type === 'visited' ? VISITED_KEY : WISHLIST_KEY;
        
        const list = JSON.parse(localStorage.getItem(key) || '[]');
        const index = list.indexOf(id);
        if (index > -1) {
            list.splice(index, 1);
            localStorage.setItem(key, JSON.stringify(list));
            updatePanels();
        }
    });

    window.addEventListener('storage', updatePanels);
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', updatePanels);
    } else {
        updatePanels();
    }
})();