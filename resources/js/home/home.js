// Search functionality - Text states for hero description
const textStates = {
    initial: "Discover extraordinary volcanic destinations or revisit your favorite eruptions",
    searching: "What volcanic wonder are you seeking today?",
    typing: "Searching...",
    noResults: "No matches found in our database",
};

// Animate text change for hero description
function animateTextChange(newText) {
    const heroDescription = document.getElementById('heroDescription');
    if (heroDescription.textContent === newText) return;
    
    heroDescription.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
    heroDescription.style.opacity = '0';
    heroDescription.style.transform = 'translateY(-10px)';
    
    setTimeout(() => {
        heroDescription.textContent = newText;
        heroDescription.style.opacity = '1';
        heroDescription.style.transform = 'translateY(0)';
    }, 200);
}

function updateSearchStatus(newText) {
    const searchStatus = document.getElementById('search-status');
    if (!searchStatus) return;
    
    // Check if content is the same (comparing stripped HTML for detection)
    const currentContent = searchStatus.innerHTML;
    if (currentContent === newText) return;
    
    searchStatus.style.transition = 'opacity 0.3s ease';
    searchStatus.style.opacity = '0';
    
    setTimeout(() => {
        searchStatus.innerHTML = newText;
        searchStatus.style.opacity = '1';
    }, 150);
}

function initializeSearch() {
    const searchToggle = document.getElementById('searchToggle');
    const heroSearchBar = document.getElementById('heroSearchBar');
    const searchInput = heroSearchBar.querySelector('input');
    const volcanoGrid = document.querySelector('.volcano-grid');
    const noResultsMessage = document.getElementById('no-results-message');
    const searchTermSpan = document.getElementById('search-term');
    
    let hasStartedTyping = false;
    let searchTimeout = null;

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

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.trim();
        
        if (searchTimeout) {
            clearTimeout(searchTimeout);
            searchTimeout = null;
        }
        
        // Handle empty search immediately
        if (searchTerm.length === 0) {
            console.log('Empty search detected - resetting to homepage view');
            
            hasStartedTyping = false;
            animateTextChange(textStates.searching);
            
            resetSearch();
            return;
        }
        
        if (!hasStartedTyping && searchTerm.length > 0) {
            hasStartedTyping = true;
            animateTextChange(textStates.typing);
            updateSearchStatus("Filtering volcanoes as you type...");
        }
        
        searchTimeout = setTimeout(() => {
            const currentTerm = searchInput.value.trim();
            if (currentTerm.length > 0) {
                performSearch(currentTerm);
            } else {
                resetSearch();
            }
        }, 300);
    });

    searchToggle.addEventListener('click', function() {
        if (heroSearchBar.classList.contains('hidden')) {
            hasStartedTyping = false;
            searchInput.value = '';
            resetSearch();
        }
    });

    searchInput.addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            const searchTerm = this.value.trim();
            if (searchTerm) {
                performSearch(searchTerm);
            }
        }
    });
    
    function resetSearch() {
        console.log('Search reset triggered');
        
        volcanoGrid.classList.remove('search-active');
        
        const cards = document.querySelectorAll('.volcano-card');
        cards.forEach((card, index) => {
            if (index < 12) {
                card.style.display = '';
                card.classList.remove('homepage-hidden');
            } else {
                card.style.display = 'none';
                card.classList.add('homepage-hidden');
            }
        });
        
        noResultsMessage.classList.add('hidden');
        volcanoGrid.classList.remove('no-results');
        
        setTimeout(() => {
            updateSearchStatus(defaultSearchStatus);
            animateTextChange(textStates.initial);
            
            searchTermSpan.textContent = '';
            
            volcanoGrid.style.opacity = '0.99';
            setTimeout(() => {
                volcanoGrid.style.opacity = '1';
                console.log('Search reset complete - First 6 volcanoes should be visible');
            }, 10);
        }, 20);
    }
    
    function performSearch(searchTerm) {
        if (!searchTerm) {
            resetSearch();
            return;
        }
        
        volcanoGrid.classList.add('search-active');
        
        // Show searching status with styled term
        const styledTerm = `<span style="color: #ff6b35; font-weight: 600;">"${searchTerm}"</span>`;
        updateSearchStatus(`Searching for ${styledTerm}...`);
        searchTermSpan.textContent = searchTerm;
        
        // Always use API search
        console.log('Using API search for:', searchTerm);
        
        fetch(`/api/volcanoes/search?query=${encodeURIComponent(searchTerm)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Server returned ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('API search results:', data);
                if (data.success) {
                    displaySearchResults(data.data, searchTerm);
                } else {
                    throw new Error('Invalid response format');
                }
            })
            .catch(error => {
                console.error('Error performing API search:', error);
                updateSearchStatus('Error searching. Please try again.');
                noResultsMessage.classList.remove('hidden');
            });
    }
    
    // Results from API
    function displaySearchResults(volcanoes, searchTerm) {
        const cards = document.querySelectorAll('.volcano-card');
        let visibleCount = 0;
        
        // Make search active for all cards including the ones hidden in database
        volcanoGrid.classList.add('search-active');
        
        // Create a map for volcanoes IDs that should be visible
        const volcanoIdMap = new Map();
        volcanoes.forEach(volcano => {
            volcanoIdMap.set(volcano.id.toString(), true);
        });
        
        cards.forEach(card => {
            const volcanoId = card.dataset.volcanoId;
            
            if (volcanoIdMap.has(volcanoId)) {
                card.style.display = '';
                card.classList.remove('homepage-hidden');
                visibleCount++;
            } else {
                card.style.display = 'none';
                card.classList.add('homepage-hidden');
            }
        });
        
        updateResultsUI(visibleCount, searchTerm);
    }
    
    // Update UI based on search results count
    function updateResultsUI(visibleCount, searchTerm) {
        console.log(`Updating results UI for ${visibleCount} visible volcanoes`);
        
        if (visibleCount === 0) {
            noResultsMessage.classList.remove('hidden');
            volcanoGrid.classList.add('no-results');
            updateSearchStatus(textStates.noResults);
        } else {
            noResultsMessage.classList.add('hidden');
            volcanoGrid.classList.remove('no-results');
            
            // Create styled search term
            const styledTerm = `<span style="color: #ff6b35; font-weight: 600;">"${searchTerm}"</span>`;
            
            if (visibleCount === 1) { // IN case there is a singular count
                updateSearchStatus(`1 volcano found for ${styledTerm}`);
                console.log('Single volcano result - using singular form');
            } else {
                // Plural form "volcanoes"
                updateSearchStatus(`${visibleCount} volcanoes found for ${styledTerm}`);
            }
        }
    }
}

// Carousel with preloading
function initializeCarousel() {
    const carouselBgs = document.querySelectorAll('.carousel-bg');
    const indicators = document.querySelectorAll('.indicator');
    let currentSlide = 0;
    let autoSlideInterval;
    carouselBgs.forEach((bg, index) => {
        const bgUrl = bg.dataset.bg;
        
        // Extract the actual image URL from the CSS url() format
        const imageUrl = bgUrl.replace(/url\(['"]?/, '').replace(/['"]?\)$/, '');
        
        // Preload the image
        const preloadImg = new Image();
        preloadImg.onload = () => {
            bg.style.backgroundImage = bgUrl;
            
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

document.addEventListener('DOMContentLoaded', function() {
    initializeSearch();
    initializeCarousel();
    initializeSmoothScroll();
    // Year updating is handled by common.js
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