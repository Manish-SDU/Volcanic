let volcanoMap = null;
window.toggleMap = toggleMap;

document.addEventListener('DOMContentLoaded', function () {
    console.log('Interactive map script loaded.'); // confirms page is fully loaded before initializing the map
        initializeMap();
    });

    function initializeMap() {
        console.log('Initializing map...');

        // Create the map and set its view to show the whole world
        // [0, 0] = center of world (latitude, longitude)
        // 2 = zoom level (1=far, 18=close)
        volcanoMap = L.map('interactive-map').setView([0, 0], 2);

        // Add the tile layer (the actual map images) using OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 18,
        }).addTo(volcanoMap);

        console.log('Map initialized successfully.');
    }

      function toggleMap() {
      console.log('Toggle map clicked.');

      // Get the map container
      const mapContainer = document.getElementById('interactive-map');
      const buttonText = document.getElementById('map-pill-text');

      // Check if map is currently visible
      if (mapContainer.style.display === 'none') {
          // Show the map
          mapContainer.style.display = 'block';
          buttonText.textContent = 'Hide Map';

          // IMPORTANT: Tell Leaflet to recalculate size
          if (volcanoMap) {
              volcanoMap.invalidateSize();
          }

          console.log('Map is now visible');
      } else {
          // Hide the map
          mapContainer.style.display = 'none';
          buttonText.textContent = 'View Map';

          console.log('Map is now hidden');
      }
  }