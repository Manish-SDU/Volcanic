  document.addEventListener('DOMContentLoaded', function() {
      const loadMoreBtn = document.getElementById('load-more-btn');
      const allLoadedMessage = document.getElementById('all-loaded-message');

      // Get how many cards to load per click (default 9)
      const loadCount = parseInt(loadMoreBtn.dataset.loadCount) || 9;

      loadMoreBtn.addEventListener('click', function() {
          // Find all hidden volcano cards
          const hiddenCards = document.querySelectorAll('.volcano-card.homepage-hidden');

          // If no more hidden cards, hide button and show message
          if (hiddenCards.length === 0) {
              loadMoreBtn.style.display = 'none';
              allLoadedMessage.style.display = 'block';
              return;
          }

          // Show the next batch of cards (e.g., 10 cards)
          for (let i = 0; i < Math.min(loadCount, hiddenCards.length); i++) {
              hiddenCards[i].classList.remove('homepage-hidden');
          }

          // Check if there are still hidden cards left
          const remainingHidden = document.querySelectorAll('.volcano-card.homepage-hidden');
          if (remainingHidden.length === 0) {
              loadMoreBtn.style.display = 'none';
              allLoadedMessage.style.display = 'block';
          }
      });
  });