document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('volcano-modal');
    
    // Check if modal exists
    if (!modal) {
        console.error('Volcano modal not found');
        return;
    }

    const modalOverlay = modal.querySelector('.volcano-modal-overlay');
    const closeBtn = modal.querySelector('.volcano-modal-close');

    console.log('Volcano modal script loaded');

    // ONLY handle Learn More buttons - be more specific
    document.addEventListener('click', function(e) {
        // ONLY target Learn More buttons, not visited/wishlist buttons
        if (e.target.closest('.learn-more-btn')) {
            e.preventDefault();
            
            console.log('Learn more button clicked');
            
            const button = e.target.closest('.learn-more-btn');
            const volcanoId = button.getAttribute('data-volcano');
            
            console.log('Volcano ID:', volcanoId);
            
            if (volcanoId) {
                openVolcanoModal(volcanoId);
            }
            
            return false;
        }
    }, true);

    // Close modal events
    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }
    
    if (modalOverlay) {
        modalOverlay.addEventListener('click', closeModal);
    }
    
    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });

    async function openVolcanoModal(volcanoId) {
        try {
            console.log('Opening modal for volcano:', volcanoId);
            
            // Show loading state
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            // Reset content
            const descElement = document.getElementById('modal-volcano-description');
            if (descElement) {
                descElement.textContent = 'Loading volcano details...';
            }

            // Fetch volcano data
            const response = await fetch(`/api/volcanoes/${volcanoId}`);
            const data = await response.json();

            console.log('API Response:', data);

            if (data.success) {
                populateModal(data.volcano);
            } else {
                showError('Failed to load volcano details');
            }
        } catch (error) {
            console.error('Error fetching volcano data:', error);
            showError('Error loading volcano information');
        }
    }

    function populateModal(volcano) {
    console.log('Populating modal with:', volcano);
    
    // Basic info - Updated to show different data
    const elements = {
        'modal-volcano-name': volcano.name,
        'modal-volcano-description': volcano.description || 'No description available for this volcano.'
    };

    // Set text content for basic elements
    Object.keys(elements).forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = elements[id];
        }
    });

    // Update the meta information with new data
    const latitudeElement = document.getElementById('modal-volcano-latitude');
    const longitudeElement = document.getElementById('modal-volcano-longitude');
    const continentElement = document.getElementById('modal-volcano-continent');

    if (latitudeElement) {
        latitudeElement.textContent = volcano.latitude || 'Unknown';
    }
    if (longitudeElement) {
        longitudeElement.textContent = volcano.longitude || 'Unknown';
    }
    if (continentElement) {
        continentElement.textContent = volcano.continent || 'Unknown';
    }
    
    // Image handling (keep this the same)
    const modalImage = document.getElementById('modal-volcano-image');
    if (modalImage) {
        let imageUrl = '';
        
        console.log('Volcano image data:', {
            safe_image_url: volcano.safe_image_url,
            image: volcano.image
        });
        
        if (volcano.safe_image_url) {
            imageUrl = volcano.safe_image_url;
        } else if (volcano.image) {
            if (volcano.image.startsWith('http') || volcano.image.startsWith('/')) {
                imageUrl = volcano.image;
            } else {
                imageUrl = `/images/volcanoes/${volcano.image}`;
            }
        } else {
            const cleanName = volcano.name.toLowerCase()
                .replace(/\s+/g, '-')
                .replace(/[^\w-]/g, '');
            imageUrl = `/images/volcanoes/${cleanName}.jpg`;
        }
        
        console.log('Final image URL:', imageUrl);
        modalImage.style.opacity = '0.5';
        modalImage.src = imageUrl;
        modalImage.alt = `${volcano.name} volcano`;
        
        modalImage.onload = function() {
            console.log('Image loaded successfully');
            this.style.opacity = '1';
        };
        
        modalImage.onerror = function() {
            console.log('Image failed to load, using placeholder');
            this.src = '/images/volcanoes/placeholder.png';
            this.style.opacity = '1';
            this.onerror = null;
        };
    }
}

    function getActivityIcon(activity) {
        switch (activity) {
            case 'active':
                return 'fa-fire';
            case 'dormant':
                return 'fa-clock';
            case 'inactive':
            case 'extinct':
                return 'fa-moon';
            default:
                return 'fa-question';
        }
    }

    function closeModal() {
        console.log('Closing modal');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    function showError(message) {
        const descElement = document.getElementById('modal-volcano-description');
        if (descElement) {
            descElement.textContent = message;
        }
    }
});