class NavCarousel {
    constructor() {
        this.currentSlide = 0;
        this.totalSlides = 4;
        this.carousel = document.querySelector('[data-carousel="static"]');
        this.slides = document.querySelectorAll('[data-carousel-item]');
        this.indicators = document.querySelectorAll('[data-carousel-slide-to]');
        
        this.init();
    }
    
    init() {
        if (!this.carousel) return;
        
        //click listeners to indicators
        this.indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => this.goToSlide(index));
        });
        
        // first slide
        this.showSlide(0);
    }
    
    goToSlide(slideIndex) {
        this.currentSlide = slideIndex;
        this.showSlide(slideIndex);
    }
    
    showSlide(slideIndex) {
        // hide all slides
        this.slides.forEach((slide, index) => {
            if (index === slideIndex) {
                slide.classList.add('active');
                slide.setAttribute('data-carousel-item', 'active');
            } else {
                slide.classList.remove('active');
                slide.setAttribute('data-carousel-item', '');
            }
        });
        
        // Update
        this.indicators.forEach((indicator, index) => {
            if (index === slideIndex) {
                indicator.classList.add('active');
            } else {
                indicator.classList.remove('active');
            }
        });
    }
}


document.addEventListener('DOMContentLoaded', () => {
    new NavCarousel();
});