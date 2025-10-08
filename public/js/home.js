// Texts for search functionality
const textStates = {
    initial: "Discover extraordinary volcanic destinations or revisit your favorite eruptions",
    searching: "What volcanic wonder are you seeking today?",
    typing: "Searching...",
    searchingName: "Showing volcanoes named",
    searchingLocation: "Showing volcanoes in",
    noResults: "No matches found in our database",
    resultsFound: "volcanoes found"
};

// Country acronym mapping for client-side search
const countryAcronymMap = {
    // North America
    'usa': 'United States',
    'us': 'United States',
    'america': 'United States',
    'states': 'United States',
    'canada': 'Canada',
    'ca': 'Canada',
    'mexico': 'Mexico',
    'mx': 'Mexico',
    'guatemala': 'Guatemala',
    'gt': 'Guatemala',
    
    // Europe
    'uk': 'United Kingdom',
    'britain': 'United Kingdom',
    'england': 'United Kingdom',
    'gb': 'United Kingdom',
    'italy': 'Italy',
    'it': 'Italy',
    'france': 'France',
    'fr': 'France',
    'germany': 'Germany',
    'de': 'Germany',
    'spain': 'Spain',
    'es': 'Spain',
    'iceland': 'Iceland',
    'is': 'Iceland',
    'turkey': 'Turkey',
    'tr': 'Turkey',
    'russia': 'Russia',
    'ru': 'Russia',
    
    // Asia
    'japan': 'Japan',
    'jp': 'Japan',
    'china': 'China',
    'cn': 'China',
    'india': 'India',
    'in': 'India',
    'indonesia': 'Indonesia',
    'id': 'Indonesia',
    'philippines': 'Philippines',
    'ph': 'Philippines',
    'south korea': 'South Korea',
    'korea': 'South Korea',
    'kr': 'South Korea',
    'thailand': 'Thailand',
    'th': 'Thailand',
    'vietnam': 'Vietnam',
    'vn': 'Vietnam',
    
    // South America
    'chile': 'Chile',
    'cl': 'Chile',
    'brazil': 'Brazil',
    'br': 'Brazil',
    'argentina': 'Argentina',
    'ar': 'Argentina',
    'peru': 'Peru',
    'pe': 'Peru',
    'colombia': 'Colombia',
    'co': 'Colombia',
    'ecuador': 'Ecuador',
    'ec': 'Ecuador',
    'bolivia': 'Bolivia',
    'bo': 'Bolivia',
    
    // Oceania
    'australia': 'Australia',
    'au': 'Australia',
    'new zealand': 'New Zealand',
    'nz': 'New Zealand',
    
    // Africa
    'south africa': 'South Africa',
    'za': 'South Africa',
    'kenya': 'Kenya',
    'ke': 'Kenya',
    'tanzania': 'Tanzania',
    'tz': 'Tanzania',
    'ethiopia': 'Ethiopia',
    'et': 'Ethiopia',
    
    // Special cases
    'pacific': 'Pacific Ocean',
    'ocean': 'Pacific Ocean',
    'pacific ocean': 'Pacific Ocean',
    'atlantic': 'Atlantic Ocean',
    'atlantic ocean': 'Atlantic Ocean',
};

// Helper function to get country matches for a search term
function getCountryMatches(searchTerm) {
    const lowerTerm = searchTerm.toLowerCase().trim();
    const matches = [];
    
    // Direct acronym match
    if (countryAcronymMap[lowerTerm]) {
        matches.push(countryAcronymMap[lowerTerm]);
    }
    
    // Partial matches for flexibility
    for (const [acronym, country] of Object.entries(countryAcronymMap)) {
        if (acronym.includes(lowerTerm) || lowerTerm.includes(acronym)) {
            if (!matches.includes(country)) {
                matches.push(country);
            }
        }
    }
    
    // Also include the original search term
    matches.push(lowerTerm);
    
    return matches;
}

// FChange from one text to another from the above functionality
function animateTextChange(newText) {
    const heroDescription = document.getElementById('heroDescription');
    if (heroDescription.textContent === newText) return; // Avoid unnecessary animations
    
    heroDescription.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
    heroDescription.style.opacity = '0';
    heroDescription.style.transform = 'translateY(-10px)';
    
    setTimeout(() => {
        heroDescription.textContent = newText;
        heroDescription.style.opacity = '1';
        heroDescription.style.transform = 'translateY(0)';
    }, 200);
}

// Update search status text in the section description
function updateSearchStatus(newText) {
    const searchStatus = document.getElementById('search-status');
    if (!searchStatus || searchStatus.textContent === newText) return;
    
    searchStatus.style.transition = 'opacity 0.3s ease';
    searchStatus.style.opacity = '0';
    
    setTimeout(() => {
        searchStatus.textContent = newText;
        searchStatus.style.opacity = '1';
    }, 150);
}

// Search functionality
function initializeSearch() {
    const searchToggle = document.getElementById('searchToggle');
    const heroSearchBar = document.getElementById('heroSearchBar');
    const searchInput = heroSearchBar.querySelector('input');
    const volcanoGrid = document.querySelector('.volcano-grid');
    const noResultsMessage = document.getElementById('no-results-message');
    const searchTermSpan = document.getElementById('search-term');
    
    let hasStartedTyping = false;
    let searchTimeout = null;
    let allVolcanoes = [];
    let isDataLoaded = false;
    
    // Function to load volcano data with timeout and retry
    function loadVolcanoData() {
        return new Promise((resolve, reject) => {
            const timeoutDuration = 5000; // 5 second timeout
            
            // Create a timeout that will reject the promise
            const timeoutPromise = new Promise((_, reject) => {
                setTimeout(() => reject(new Error('Request timed out')), timeoutDuration);
            });
            
            // Create the fetch promise
            const fetchPromise = fetch('/api/volcanoes')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Network response was not ok: ${response.status}`);
                    }
                    return response.json();
                });
            
            // Race between fetch and timeout
            Promise.race([fetchPromise, timeoutPromise])
                .then(data => {
                    if (data.success) {
                        isDataLoaded = true;
                        resolve(data.data);
                    } else {
                        reject(new Error('Invalid data format'));
                    }
                })
                .catch(error => {
                    console.error('Error loading volcanoes:', error);
                    reject(error);
                });
        });
    }
    
    // Try loading volcanoes with retry logic for better reliability
    function loadVolcanoesWithRetry(maxRetries = 2) {
        let retryCount = 0;
        
        function tryLoad() {
            loadVolcanoData()
                .then(data => {
                    allVolcanoes = data;
                    isDataLoaded = true;
                    console.log('Volcano data loaded successfully');
                })
                .catch(error => {
                    if (retryCount < maxRetries) {
                        retryCount++;
                        console.log(`Retry attempt ${retryCount}...`);
                        setTimeout(tryLoad, 1000 * retryCount); // Progressive backoff
                    } else {
                        console.error('Failed to load volcano data after retries');
                        // Continue without data - search will use API calls
                    }
                });
        }
        
        tryLoad();
    }
    
    // Start loading volcano data
    loadVolcanoesWithRetry();

    // Default search status text
    const defaultSearchStatus = document.getElementById('search-status').textContent;
        
    searchToggle.addEventListener('click', function() {
        heroSearchBar.classList.toggle('hidden');
        
        if (!heroSearchBar.classList.contains('hidden')) {
            animateTextChange(textStates.searching);
            setTimeout(() => {
                heroSearchBar.querySelector('input').focus();
            }, 300);
        } else {
            animateTextChange(textStates.initial);
            // Reset search when closing
            if (searchInput.value) {
                searchInput.value = '';
                resetSearch();
            }
        }
    });

    // Hide search bar when clicking outside
    document.addEventListener('click', function(event) {
        const isClickInsideSearch = heroSearchBar.contains(event.target);
        const isClickOnToggle = searchToggle.contains(event.target);
        
        if (!isClickInsideSearch && !isClickOnToggle && !heroSearchBar.classList.contains('hidden')) {
            heroSearchBar.classList.add('hidden');
            animateTextChange(textStates.initial);
            updateSearchStatus(defaultSearchStatus);
        }
    });

    // Handle input changes with debounce for real-time search
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.trim();
        
        // Clear previous timeout
        if (searchTimeout) {
            clearTimeout(searchTimeout);
            searchTimeout = null;
        }
        
        // Handle empty search immediately
        if (searchTerm.length === 0) {
            console.log('Empty search detected - resetting to homepage view');
            
            // Reset typing state
            hasStartedTyping = false;
            animateTextChange(textStates.searching);
            
            // Complete the reset process
            resetSearch();
            return;
        }
        
        // Update typing state UI
        if (!hasStartedTyping && searchTerm.length > 0) {
            hasStartedTyping = true;
            animateTextChange(textStates.typing);
            updateSearchStatus("Filtering volcanoes as you type...");
        }
        
        // Set new timeout for debounce (300ms)
        searchTimeout = setTimeout(() => {
            const currentTerm = searchInput.value.trim();
            if (currentTerm.length > 0) {
                performSearch(currentTerm);
            } else {
                // Double-check if the field is really empty (user might have cleared it after the timeout was set)
                resetSearch();
            }
        }, 300);
    });

    // Reset typing state when search bar is hidden
    searchToggle.addEventListener('click', function() {
        if (heroSearchBar.classList.contains('hidden')) {
            hasStartedTyping = false;
            searchInput.value = '';
            resetSearch();
        }
    });

    // Search submissions on Enter key
    searchInput.addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            const searchTerm = this.value.trim();
            if (searchTerm) {
                performSearch(searchTerm);
            }
        }
    });
    
    // Reset search and show only first 6 volcanoes (homepage default)
    function resetSearch() {
        console.log('Search reset triggered');
        
        // Remove search-active class from volcano grid
        volcanoGrid.classList.remove('search-active');
        
        // Show only the first 6 cards (homepage default)
        const cards = document.querySelectorAll('.volcano-card');
        cards.forEach((card, index) => {
            if (index < 6) {
                card.style.display = '';
                card.classList.remove('homepage-hidden');
            } else {
                card.style.display = 'none';
                card.classList.add('homepage-hidden');
            }
        });
        
        // Reset UI elements immediately
        noResultsMessage.classList.add('hidden');
        volcanoGrid.classList.remove('no-results');
        
        // Add a small delay before updating text to ensure visual changes are applied
        setTimeout(() => {
            updateSearchStatus(defaultSearchStatus);
            animateTextChange(textStates.initial);
            
            // Clear search term
            searchTermSpan.textContent = '';
            
            // Force browser to redraw cards if needed
            volcanoGrid.style.opacity = '0.99';
            setTimeout(() => {
                volcanoGrid.style.opacity = '1';
                console.log('Search reset complete - First 6 volcanoes should be visible');
            }, 10);
        }, 20);
    }
    
    // Perform the search against the API
    function performSearch(searchTerm) {
        if (!searchTerm) {
            resetSearch();
            return;
        }
        
        // Enable search mode - this allows all cards to be visible for searching
        volcanoGrid.classList.add('search-active');
        
        // First check if it looks more like a location or a name
        const isLikelyLocation = isLocationSearch(searchTerm);
        
        // Update search status text
        if (isLikelyLocation) {
            updateSearchStatus(`${textStates.searchingLocation} "${searchTerm}"...`);
        } else {
            updateSearchStatus(`${textStates.searchingName} "${searchTerm}"...`);
        }
        
        // Set the search term in the no-results message
        searchTermSpan.textContent = searchTerm;
        
        // If data is loaded, use client-side filtering for better performance
        if (isDataLoaded && allVolcanoes.length > 0) {
            console.log('Using client-side filtering');
            filterVolcanoes(searchTerm);
        } else {
            console.log('Using API search');
            // Show searching status
            updateSearchStatus(`Searching for "${searchTerm}"...`);
            
            // Fall back to API search if local data isn't available yet
            fetch(`/api/volcanoes/search?query=${encodeURIComponent(searchTerm)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Server returned ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        displaySearchResults(data.data, searchTerm);
                    } else {
                        throw new Error('Invalid response format');
                    }
                })
                .catch(error => {
                    console.error('Error performing search:', error);
                    // Fall back to client-side filtering when API fails
                    if (document.querySelectorAll('.volcano-card').length > 0) {
                        filterVolcanoes(searchTerm);
                    } else {
                        updateSearchStatus('Error searching. Please try again.');
                        noResultsMessage.classList.remove('hidden');
                    }
                });
        }
    }
    
    // Results from API
    function displaySearchResults(volcanoes, searchTerm) {
        const cards = document.querySelectorAll('.volcano-card');
        let visibleCount = 0;
        
        // Make search active for all crads including the ones hidden in database
        volcanoGrid.classList.add('search-active');
        
        // map for volcanoes IDs
        const volcanoIdMap = new Map();
        volcanoes.forEach(volcano => volcanoIdMap.set(volcano.id.toString(), true));
        
        cards.forEach(card => {
            const volcanoId = card.dataset.volcanoId;
            if (volcanoIdMap.has(volcanoId)) {
                card.style.display = '';
                card.classList.remove('homepage-hidden');
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        updateResultsUI(visibleCount, searchTerm);
    }
    
    // Trial use for cards filtering on the client side
    function filterVolcanoes(searchTerm) {
        const cards = document.querySelectorAll('.volcano-card');
        let visibleCount = 0;
        
        searchTerm = searchTerm.toLowerCase();
        
        // Potential results from searches
        const countryMatches = getCountryMatches(searchTerm);
        
        volcanoGrid.classList.add('search-active');
        
        cards.forEach(card => {
            // Get the volcano name and country from the card
            const name = card.querySelector('.volcano-title').textContent.toLowerCase();
            const countryElement = card.querySelector('.detail-simple:nth-child(2)');
            const country = countryElement ? countryElement.textContent.toLowerCase() : '';
            
            let isMatch = false;
            
            // Check if search matches the name or country
            if (name.includes(searchTerm)) {
                isMatch = true;
            }
            if (country.includes(searchTerm)) {
                isMatch = true;
            }
            
            // Country acronym check
            for (const countryMatch of countryMatches) {
                if (country.includes(countryMatch.toLowerCase())) {
                    isMatch = true;
                    break;
                }
            }
            
            if (isMatch) {
                card.style.display = '';
                // Remove homepage-hidden class so card shows even if it was initially hidden
                card.classList.remove('homepage-hidden');
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        updateResultsUI(visibleCount, searchTerm);
    }
    
    // Update UI based on search results count
    function updateResultsUI(visibleCount, searchTerm) {
        console.log(`Updating results UI for ${visibleCount} visible volcanoes`);
        
        // Show no results message if needed
        if (visibleCount === 0) {
            noResultsMessage.classList.remove('hidden');
            volcanoGrid.classList.add('no-results');
            updateSearchStatus(textStates.noResults);
        } else {
            noResultsMessage.classList.add('hidden');
            volcanoGrid.classList.remove('no-results');
            
            if (visibleCount === 1) { // IN case there is a singular count
                updateSearchStatus(`1 volcano found`);
                console.log('Single volcano result - using singular form');
            } else {
                // Plural form "volcanoes"
                updateSearchStatus(`${visibleCount} ${textStates.resultsFound}`);
            }
        }
    }
    
    // Try to determine if search is more likely for location or name
    function isLocationSearch(term) {
        // Common location indicators
        const locationKeywords = ['in', 'at', 'near', 'around', 'country', 'region', 'continent'];
        term = term.toLowerCase();
        
        // Check if any location keywords are in the search
        if (locationKeywords.some(keyword => term.includes(keyword))) {
            return true;
        }
        
        // Check if the term is a country acronym
        if (countryAcronymMap[term.toLowerCase()]) {
            return true;
        }
        
        // Check if it's a short term that might be a country acronym
        if (term.length <= 3 && /^[a-z]+$/.test(term)) {
            return true;
        }
        
        return false;
    }
}

// Image carousel with preloading
function initializeCarousel() {
    const carouselBgs = document.querySelectorAll('.carousel-bg');
    const indicators = document.querySelectorAll('.indicator');
    let currentSlide = 0;
    let autoSlideInterval;

    // Preload all carousel images immediately for instant display
    carouselBgs.forEach((bg, index) => {
        const bgUrl = bg.dataset.bg;
        
        // Extract the actual image URL from the CSS url() format
        const imageUrl = bgUrl.replace(/url\(['"]?/, '').replace(/['"]?\)$/, '');
        
        // Preload the image
        const preloadImg = new Image();
        preloadImg.onload = () => {
            // Set background image immediately after preload
            bg.style.backgroundImage = bgUrl;
            
            // Show first image immediately if it's the first one
            if (index === 0) {
                bg.classList.add('active');
            }
        };
        preloadImg.src = imageUrl;
    });

    function showSlide(index) {
        carouselBgs.forEach(bg => bg.classList.remove('active'));
        indicators.forEach(indicator => indicator.classList.remove('active'));
        
        carouselBgs[index].classList.add('active');
        indicators[index].classList.add('active');
        
        currentSlide = index;
    }

    function nextSlide() {
        const next = (currentSlide + 1) % carouselBgs.length;
        showSlide(next);
    }

    function startAutoSlide() {
        autoSlideInterval = setInterval(nextSlide, 4000);
    }

    function stopAutoSlide() {
        clearInterval(autoSlideInterval);
    }

    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => {
            showSlide(index);
            stopAutoSlide();
            setTimeout(startAutoSlide, 8000);
        });
    });
    startAutoSlide();
}

// Initialize on DOM load
document.addEventListener('DOMContentLoaded', function() {
    initializeSearch();
    initializeCarousel();
    initializeSmoothScroll();
    // Note: Year updating is handled by common.js
});

// Smooth scroll functionality for search link
function initializeSmoothScroll() {
    const searchLink = document.querySelector('.search-link');
    if (searchLink) {
        searchLink.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
                
                // Also focus on the search toggle button to highlight the search functionality
                setTimeout(() => {
                    const searchToggle = document.getElementById('searchToggle');
                    if (searchToggle) {
                        searchToggle.focus();
                    }
                }, 800);
            }
        });
    }
}