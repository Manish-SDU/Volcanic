let volcanoMap = null;
window.toggleMap = toggleMap;

document.addEventListener('DOMContentLoaded', function () {
    console.log('Page ready.'); // confirms page is fully loaded before initializing the map
    initializeMap();
    loadVolcanoes(); // Load volcanoes from API
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

function loadVolcanoes() {
    console.log('Fetching volcanoes from API...');

    // Fetch data from Laravel API
    fetch('/api/volcanoes')
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch volcanoes');
            }
            // Parse JSON response
            return response.json();
        })

        .then(data => {
            console.log('Received volcanoes:', data);

            const volcanoes = data.data;
            console.log(`Loaded ${data.count} volcanoes`);

            addMarkers(volcanoes);
        })

        .catch(error => {
            console.error('Error loading volcanoes:', error);
            alert('Could not load volcanoes. Please refresh the page.');
        });
}

function createPopupHTML(volcano) {
    return `
          <div style="min-width: 50px; padding: 8px; text-align: center ">   
              <h3 style="margin: 0 0 10px 0; color: #2c3e50;">
                  ${volcano.name}
              </h3>
              <div style="margin-bottom: 8px;">
                  <strong>🏔️ Elevation:</strong> ${volcano.elevation}m
              </div>
              <div style="margin-bottom: 8px;">
                  <strong>🌋 Type:</strong> ${volcano.type}
              </div>
              <div style="margin-bottom: 8px;">
                  <strong>⚡ Status:</strong> 
                  <span style="
                      padding: 2px 8px; 
                      border-radius: 4px; 
                      background: ${volcano.activity === 'Active' ? '#e74c3c' : volcano.activity === 'Dormant' ? '#f39c12' : '#95a5a6'};
                      color: white;
                      font-weight: bold;
                  ">
                      ${volcano.activity}
                  </span>
              </div>
          </div>
      `;
}

function addMarkers(volcanoes) {
      console.log(`Adding ${volcanoes.length} markers to map...`);

      const markers = L.markerClusterGroup({
          maxClusterRadius: 60,  
          spiderfyOnMaxZoom: true,  
          showCoverageOnHover: false,  
          zoomToBoundsOnClick: true  
      });

      volcanoes.forEach(volcano => {
          const marker = L.marker([volcano.latitude, volcano.longitude]);
          const popupHTML = createPopupHTML(volcano);
          marker.bindPopup(popupHTML);
          markers.addLayer(marker);
      });

      volcanoMap.addLayer(markers);
      console.log('All markers added with clustering.');
  }