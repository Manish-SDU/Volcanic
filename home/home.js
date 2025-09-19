// Texts for search functionality
const textStates = {
    initial: "Discover extraordinary volcanic destinations or revisit your favorite eruptions",
    searching: "What volcanic wonder are you seeking today?",
    typing: "Exploring volcanic destinations..."
};

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

// Search functionality
function initializeSearch() {
    const searchToggle = document.getElementById('searchToggle');
    const heroSearchBar = document.getElementById('heroSearchBar');
    const searchInput = heroSearchBar.querySelector('input');
    let hasStartedTyping = false;

    searchToggle.addEventListener('click', function() {
        heroSearchBar.classList.toggle('hidden');
        
        if (!heroSearchBar.classList.contains('hidden')) {
            animateTextChange(textStates.searching);
            setTimeout(() => {
                heroSearchBar.querySelector('input').focus();
            }, 300);
        } else {
            animateTextChange(textStates.initial);
        }
    });

    // Hide search bar when clicking outside
    document.addEventListener('click', function(event) {
        const isClickInsideSearch = heroSearchBar.contains(event.target);
        const isClickOnToggle = searchToggle.contains(event.target);
        
        if (!isClickInsideSearch && !isClickOnToggle && !heroSearchBar.classList.contains('hidden')) {
            heroSearchBar.classList.add('hidden');
            animateTextChange(textStates.initial);
        }
    });

    // Handle input changes
    searchInput.addEventListener('input', function() {
        if (!hasStartedTyping && this.value.length > 0) {
            hasStartedTyping = true;
            animateTextChange(textStates.typing);
        } else if (hasStartedTyping && this.value.length === 0) {
            hasStartedTyping = false;
            animateTextChange(textStates.searching);
        }
    });

    // Reset typing state when search bar is hidden
    searchToggle.addEventListener('click', function() {
        if (heroSearchBar.classList.contains('hidden')) {
            hasStartedTyping = false;
            searchInput.value = '';
        }
    });

    // Search submissions
    function performSearch() {
        const searchTerm = searchInput.value.trim();
        if (searchTerm) {
            console.log('Searching for:', searchTerm);
            alert('Searching for: ' + searchTerm);
        }
    }

    searchInput.addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            performSearch();
        }
    });
}

// IMsage courasel functionality
function initializeCarousel() {
    const carouselBgs = document.querySelectorAll('.carousel-bg');
    const indicators = document.querySelectorAll('.indicator');
    let currentSlide = 0;
    let autoSlideInterval;

    // Firsdt background image as default when loading for the first time
    carouselBgs.forEach((bg, index) => {
        const bgUrl = bg.dataset.bg;
        bg.style.backgroundImage = bgUrl;
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

    // Manual small circle indicators to control imgs flow
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
});