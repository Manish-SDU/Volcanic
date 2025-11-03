  document.addEventListener('DOMContentLoaded', function() {
      // Lazy load images when they come into view
      const lazyImages = document.querySelectorAll('.lazy-image');

      const imageObserver = new IntersectionObserver((entries, observer) => {
          entries.forEach(entry => {
              if (entry.isIntersecting) {
                  const img = entry.target;
                  img.src = img.dataset.src;
                  img.classList.add('loaded');
                  observer.unobserve(img);
              }
          });
      });

      lazyImages.forEach(img => imageObserver.observe(img));
  });