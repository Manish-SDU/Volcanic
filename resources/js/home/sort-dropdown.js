// Sort Dropdown Functionality
function initializeSortDropdown() {
    const sortBtn = document.getElementById('sort-dropdown-btn');
    const sortMenu = document.getElementById('sort-dropdown-menu');
    const sortLabel = document.getElementById('sort-label');
    const sortOptions = document.querySelectorAll('.sort-option');
    const sortContainer = document.getElementById('sort-dropdown-container');

    if (!sortBtn || !sortMenu || !sortLabel) {
        return;
    }

    // Get initial sort from URL or default to alphabetical
    const urlParams = new URLSearchParams(window.location.search);
    const currentSort = urlParams.get('sort') || 'alphabetical';

    // Set initial active state
    sortOptions.forEach(option => {
        if (option.dataset.sort === currentSort) {
            option.classList.add('active');
            sortLabel.textContent = option.querySelector('span').textContent;
        } else {
            option.classList.remove('active');
        }
    });

    // Check if filters or search are active
    function hasActiveFiltersOrSearch() {
        const urlParams = new URLSearchParams(window.location.search);
        const hasFilters = urlParams.has('country') || 
                          urlParams.has('continent') || 
                          urlParams.has('activity') || 
                          urlParams.has('type') || 
                          urlParams.has('elevation_min') || 
                          urlParams.has('elevation_max');
        
        // Check if search is active by looking for search input value
        const searchInput = document.querySelector('#heroSearchBar input');
        const hasSearch = searchInput && searchInput.value.trim().length > 0;
        
        return hasFilters || hasSearch;
    }

    // Update dropdown state based on filters/search
    function updateDropdownState() {
        const isDisabled = hasActiveFiltersOrSearch();
        
        if (isDisabled) {
            sortBtn.classList.add('disabled');
            sortBtn.style.opacity = '0.5';
            sortBtn.style.cursor = 'not-allowed';
            sortBtn.title = 'Sorting is disabled when filters or search are active';
        } else {
            sortBtn.classList.remove('disabled');
            sortBtn.style.opacity = '1';
            sortBtn.style.cursor = 'pointer';
            sortBtn.title = 'Sort volcanoes';
        }
    }

    // Initial state check
    updateDropdownState();

    // Toggle dropdown menu
    sortBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        
        if (hasActiveFiltersOrSearch()) {
            return; // Don't open if disabled
        }
        
        const isVisible = sortMenu.style.display === 'block';
        sortMenu.style.display = isVisible ? 'none' : 'block';
        
        // Rotate chevron icon
        const chevron = sortBtn.querySelector('.fa-chevron-down');
        if (chevron) {
            chevron.style.transform = isVisible ? 'rotate(0deg)' : 'rotate(180deg)';
        }
    });

    // Handle sort option selection
    sortOptions.forEach(option => {
        option.addEventListener('click', function(e) {
            e.stopPropagation();
            
            const selectedSort = this.dataset.sort;
            
            // Update active state
            sortOptions.forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');
            
            // Update label
            sortLabel.textContent = this.querySelector('span').textContent;
            
            // Close dropdown
            sortMenu.style.display = 'none';
            const chevron = sortBtn.querySelector('.fa-chevron-down');
            if (chevron) {
                chevron.style.transform = 'rotate(0deg)';
            }
            
            // Reload page with new sort parameter
            const url = new URL(window.location);
            url.searchParams.set('sort', selectedSort);
            window.location.href = url.toString();
        });
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!sortContainer.contains(e.target)) {
            sortMenu.style.display = 'none';
            const chevron = sortBtn.querySelector('.fa-chevron-down');
            if (chevron) {
                chevron.style.transform = 'rotate(0deg)';
            }
        }
    });

    // Listen for search input changes to update dropdown state
    const searchInput = document.querySelector('#heroSearchBar input');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            setTimeout(updateDropdownState, 100);
        });
    }

    // Listen for filter changes (when filter modal closes)
    const filterModal = document.getElementById('filterModal');
    if (filterModal) {
        // Observe when filters are applied (page reload will happen)
        const observer = new MutationObserver(function(mutations) {
            updateDropdownState();
        });
        observer.observe(filterModal, { attributes: true, attributeFilter: ['style'] });
    }
}

// Initialize on page load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeSortDropdown);
} else {
    initializeSortDropdown();
}
