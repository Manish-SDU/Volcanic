let volcanoMap = null;
let allMarkers = [];  // Store all markers for easy access
let visitedCount = 0;    // Make counts accessible globally
let wishlistCount = 0;
let notVisitedCount = 0;
window.allMarkers = allMarkers;  // Make accessible globally
window.toggleMap = toggleMap;

function toggleMap() {
    console.log('Toggle map clicked.');

    // Get the map container
    const mapContainer = document.getElementById('interactive-map');
    const legendDiv = document.getElementById('map-legend');
    const buttonText = document.getElementById('map-pill-text');

    if (mapContainer.style.display === 'none') {
        mapContainer.style.display = 'block';
        if (window.userAuth.isAuthenticated && legendDiv) { // Show legend if user is authenticated
            legendDiv.style.display = 'block';
        }
        buttonText.textContent = 'Hide Map';

        // IMPORTANT: Tell Leaflet to recalculate size
        if (volcanoMap) {
            volcanoMap.invalidateSize();
        }

        console.log('Map is now visible');
    } else {
        mapContainer.style.display = 'none';
        if (legendDiv) {
            legendDiv.style.display = 'none';
        }
        buttonText.textContent = 'View Map';

        console.log('Map is now hidden');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    console.log('Page ready.'); // confirms page is fully loaded before initializing the map
    initializeMap();
    loadVolcanoes(); // Load volcanoes from API
});

function initializeMap() {
    console.log('Initializing map...');

    volcanoMap = L.map('interactive-map', {
        center: [20, 0],
        zoom: 0,
        minZoom: 2,
        maxZoom: 18,
        maxBounds: [
            [-90, -180],
            [90, 180]
        ],
        maxBoundsViscosity: 1.0
    });

    // Add the tile layer (the actual map images) using OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 18,
    }).addTo(volcanoMap);

    console.log('Map initialized successfully.');
}

function loadVolcanoes() {
    console.log('Fetching volcanoes from API...');

    let userStatusPromise;

    if (window.userAuth.isAuthenticated) {
        console.log('User is logged in, fetching their lists...');
        userStatusPromise = fetchUserLists();
    } else {
        console.log('User is a guest, no lists to fetch');
        userStatusPromise = Promise.resolve({});
    }

    Promise.all([
        fetch('/api/volcanoes').then(r => r.json()),
        userStatusPromise
    ])
        .then(([volcanoData, userStatusMap]) => {
            console.log('Received volcanoes:', volcanoData);

            const volcanoes = volcanoData.data;
            console.log(`Loaded ${volcanoData.count} volcanoes`);

            addMarkers(volcanoes, userStatusMap);
            updateLegend(userStatusMap, volcanoData.count);
        })
        .catch(error => {
            console.error('Error loading volcanoes:', error);
            alert('Could not load volcanoes. Please refresh the page.');
        });
}

function updateLegend(userStatusMap, totalVolcanoes) {
    const legendDiv = document.getElementById('map-legend');

    if (!window.userAuth.isAuthenticated) {
        legendDiv.style.display = 'none';
        return;
    }

    visitedCount = 0;
    wishlistCount = 0;

    Object.values(userStatusMap).forEach(status => {
        if (status === 'visited') visitedCount++;
        if (status === 'wishlist') wishlistCount++;
    });

    notVisitedCount = totalVolcanoes - visitedCount - wishlistCount;
    legendDiv.style.display = 'none';
    updateLegendHTML();
}

function updateLegendHTML() {
    const legendDiv = document.getElementById('map-legend');
    const legendText = document.getElementById('legend-text');
    legendText.innerHTML = `
      <strong>${window.userAuth.userName}</strong>, you have 
      <span style="color: #1e8449; font-weight: bold;">${visitedCount} visited volcano${visitedCount !== 1 ? 's' : ''}</span> 
      <span style="display: inline-block; width: 20px; height: 20px; background: #27ae60; border-radius: 50%; vertical-align: middle; margin: 0 4px;"></span>, 
      <span style="color: #d68910; font-weight: bold;">${wishlistCount} volcano${wishlistCount !== 1 ? 's' : ''} on your wish to visit list</span> 
      <span style="display: inline-block; width: 20px; height: 20px; background: #f39c12; border-radius: 50%; vertical-align: middle; margin: 0 4px;"></span>, 
      and <span style="color: #c0392b; font-weight: bold;">${notVisitedCount} volcano${notVisitedCount !== 1 ? 's' : ''} yet to discover!</span> 
      <span style="display: inline-block; width: 20px; height: 20px; background: #e74c3c; border-radius: 50%; vertical-align: middle; margin: 0 4px;"></span>
      <br>
      <span style="font-size: 14px; color: #7f8c8d; margin-top: 8px; display: inline-block;">
          💡 Click a volcano to access its switch status button.
      </span>
  `;
}

function updateVolcanoCard(volcanoId, newStatus) {
    // Find the card on the page
    const card = document.querySelector(`.volcano-card[data-volcano-id="${volcanoId}"]`);
    if (!card) return; // Card not visible/doesn't exist

    const visitedBtn = card.querySelector('.visited-btn');
    const wishlistBtn = card.querySelector('.wishlist-btn');

    if (!visitedBtn || !wishlistBtn) return; // Buttons don't exist (guest user)

    // Reset all states first
    visitedBtn.classList.remove('active');
    wishlistBtn.classList.remove('active');
    visitedBtn.disabled = false;
    wishlistBtn.disabled = false;

    // Set new state
    if (newStatus === 'visited') {
        visitedBtn.classList.add('active');
        wishlistBtn.disabled = true;
    } else if (newStatus === 'wishlist') {
        wishlistBtn.classList.add('active');
        visitedBtn.disabled = true;
    }
    // else newStatus === null, both inactive and enabled (already set above)
}

function updateVolcanoMarker(volcanoId, newStatus) {
    const marker = window.allMarkers?.find(m => m.volcanoData?.id === volcanoId);
    if (!marker) return; // Marker not found

    const volcano = marker.volcanoData;
    const oldStatus = marker.volcanoStatus;
    const newIcon = createVolcanoIcon(newStatus, true);
    marker.setIcon(newIcon);
    marker.volcanoStatus = newStatus;
    const newPopupHTML = createPopupHTML(volcano, newStatus, true);
    marker.setPopupContent(newPopupHTML);
    updateLegendCounts(oldStatus, newStatus);

    console.log(`✓ Marker updated: ${volcano.name} is now ${newStatus || 'not in any list'}`);
}
// Make it globally accessible so volcano-actions.js can call it
  window.updateVolcanoMarker = updateVolcanoMarker;

function createPopupHTML(volcano, userStatus, isAuthenticated) {
    let userStatusHTML = '';

    if (isAuthenticated) {
        if (userStatus === 'visited') {
            userStatusHTML = `
                  <div style="
                      margin-bottom: 12px; 
                      padding: 8px 12px; 
                      background: #d5f4e6; 
                      border-left: 4px solid #27ae60;
                      border-radius: 4px;
                  ">
                      <strong style="color: #1e8449;">You've visited this volcano!</strong>
                  </div>
              `;
        } else if (userStatus === 'wishlist') {
            userStatusHTML = `
                  <div style="
                      margin-bottom: 12px; 
                      padding: 8px 12px; 
                      background: #fef5e7; 
                      border-left: 4px solid #f39c12;
                      border-radius: 4px;
                  ">
                      <strong style="color: #d68910;">On your wishlist</strong>
                  </div>
              `;
        } else {
            userStatusHTML = `
                  <div style="
                      margin-bottom: 12px; 
                      padding: 8px 12px; 
                      background: #fadbd8; 
                      border-left: 4px solid #e74c3c;
                      border-radius: 4px;
                  ">
                      <strong style="color: #c0392b;">Waiting to be explored!</strong>
                  </div>
              `;
        }
    } else {
        userStatusHTML = `
          <div style="
              margin-bottom: 12px; 
              padding: 8px 12px; 
              background: #d6eaf8; 
              border-left: 4px solid #3498db;
              border-radius: 4px;
              text-align: center;
          ">
              <strong style="color: #2874a6;">🔐 Sign in or sign up to save!</strong>
          </div>
      `;
    }

    let cycleButtonHTML = '';

    if (isAuthenticated) {
        let buttonText;
        let nextStatus;

        if (userStatus === null) {
            buttonText = '🔄 Add to Wishlist';
            nextStatus = 'wishlist';
        } else if (userStatus === 'wishlist') {
            buttonText = '🔄 Mark as Visited';
            nextStatus = 'visited';
        } else if (userStatus === 'visited') {
            buttonText = '🔄 Unsave';
            nextStatus = 'null';
        }

        cycleButtonHTML = `
            <button 
                class="cycle-status-btn" 
                data-volcano-id="${volcano.id}"
                data-current-status="${userStatus}"
                data-next-status="${nextStatus}"
                style="
                    width: 100%;
                    background: transparent;
                    color: #2c3e50;
                    border: none;
                    border-radius: 6px;
                    font-weight: bold;
                    cursor: pointer;
                    transition: opacity 0.2s;
                "
                onmouseover="this.style.opacity='0.7'"
                onmouseout="this.style.opacity='1'"
            >
                ${buttonText}
            </button>
        `;
    }

    return `
      <div style="min-width: 200px; max-width: 250px; padding: 12px; text-align: center;">
          
          <h3 style="margin: 0 0 12px 0; color: #2c3e50; font-size: 18px;">
              ${volcano.name}
          </h3>
          
          <div style="margin-bottom: 8px;">
              <strong>Elevation:</strong> ${volcano.elevation}m
          </div>
          <div style="margin-bottom: 8px;">
              <strong>Type:</strong> ${volcano.type}
          </div>
          <div style="margin-bottom: 8px;">
              <strong>Status:</strong> 
              <span style="
                  padding: 2px 8px; 
                  border-radius: 4px; 
                  background: ${volcano.activity === 'Active' ? '#e74c3c' : volcano.activity === 'Dormant' ? '#f39c12' : '#95a5a6'};
                  color: white;
                  font-weight: bold;
                  font-size: 12px;
              ">
                  ${volcano.activity}
              </span>
          </div>

          ${userStatusHTML}
          ${cycleButtonHTML}
      </div>
  `;
}

function createVolcanoIcon(userStatus, isAuthenticated) {
    let filter;

    if (!isAuthenticated) {
        // Guest users see blue volcanoes
        filter = 'brightness(0) saturate(100%) invert(53%) sepia(89%) saturate(1750%) hue-rotate(177deg) brightness(96%) contrast(91%)';
    } else if (userStatus === 'visited') {
        // Green for visited
        filter = 'brightness(0) saturate(100%) invert(43%) sepia(96%) saturate(638%) hue-rotate(92deg) brightness(91%) contrast(88%)';
    } else if (userStatus === 'wishlist') {
        // Orange for wishlist
        filter = 'brightness(0) saturate(100%) invert(68%) sepia(89%) saturate(1531%) hue-rotate(359deg) brightness(99%) contrast(95%)';
    } else {
        // Red for not visited/wishlist (logged-in user)
        filter = 'brightness(0) saturate(100%) invert(30%) sepia(95%) saturate(2198%) hue-rotate(344deg) brightness(94%) contrast(92%)';
    }

    const iconHTML = `
          <div style="
              width: 60px;
              height: 60px;
              display: flex;
              align-items: center;
              justify-content: center;
          ">
              <img 
                  src="/images/volcanoes/volcano-marker.png" 
                  alt="volcano marker"
                  style="
                      width: 100%;
                      height: 100%;
                      filter: ${filter};
                      drop-shadow(0 2px 4px rgba(0,0,0,0.3));
                  "
              />
          </div>
      `;

    return L.divIcon({
        html: iconHTML,
        className: 'custom-volcano-marker',
        iconSize: [60, 60],
        iconAnchor: [30, 30],
        popupAnchor: [0, -5]
    });
}

function addMarkers(volcanoes, userStatusMap) {
    console.log(`Adding ${volcanoes.length} markers to map...`);

    const markers = L.markerClusterGroup({
        maxClusterRadius: 60,
        spiderfyOnMaxZoom: true,
        showCoverageOnHover: false,
        zoomToBoundsOnClick: true
    });

    const isAuthenticated = window.userAuth.isAuthenticated;

    volcanoes.forEach(volcano => {
        const userStatus = userStatusMap[volcano.id] || null;

        const customIcon = createVolcanoIcon(userStatus, isAuthenticated);

        const marker = L.marker(
            [volcano.latitude, volcano.longitude],
            { icon: customIcon }
        );

        // Store status on marker for easy access
        marker.volcanoStatus = userStatus;
        marker.volcanoData = volcano;

        // Store marker globally
        allMarkers.push(marker);

        const popupHTML = createPopupHTML(volcano, userStatus, isAuthenticated);
        marker.bindPopup(popupHTML);
        markers.addLayer(marker);
    });

    volcanoMap.addLayer(markers);

    console.log('All markers added with custom icons.');
}

function fetchUserLists() {
    console.log('Fetching user lists...');

    return fetch('/user/volcanoes/lists')
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch user lists');
            }
            return response.json();
        })
        .then(lists => {
            console.log('User lists received:', lists);

            // Create a lookup object: { volcanoId: status }
            // Example: { 1: "visited", 5: "wishlist", 12: "visited" }
            const statusMap = {};

            if (lists.visited && Array.isArray(lists.visited)) {
                lists.visited.forEach(volcano => {
                    statusMap[volcano.volcanoes_id] = 'visited';
                });
            }

            if (lists.wishlist && Array.isArray(lists.wishlist)) {
                lists.wishlist.forEach(volcano => {
                    statusMap[volcano.volcanoes_id] = 'wishlist';
                });
            }

            console.log(`User has ${Object.keys(statusMap).length} volcanoes in their lists`);
            console.log('Status map:', statusMap);
            return statusMap;
        })
        .catch(error => {
            console.error('Error fetching user lists:', error);
            return {};
        });
}

function cycleVolcanoStatus(volcanoId, currentStatus, marker, volcano) {
    // Only logged-in users can cycle status
    if (!window.userAuth.isAuthenticated) {
        alert('Please log in to track volcanoes.');
        return;
    }

    // Determine next status
    let nextStatus;
    let apiEndpoint;

    if (currentStatus === null) {
        // Red -> Orange (add to wishlist)
        nextStatus = 'wishlist';
        apiEndpoint = `/user/volcanoes/${volcanoId}/wishlist`;
    } else if (currentStatus === 'wishlist') {
        // Orange -> Green (mark as visited)
        nextStatus = 'visited';
        apiEndpoint = `/user/volcanoes/${volcanoId}/visited`;
    } else if (currentStatus === 'visited') {
        // Green -> Red (remove from lists)
        nextStatus = null;
        apiEndpoint = `/user/volcanoes/${volcanoId}/visited`;
    }

    console.log(`Cycling ${volcano.name}: ${currentStatus} -> ${nextStatus}`);

    // Update marker icon immediately 
    const newIcon = createVolcanoIcon(nextStatus, true);
    marker.setIcon(newIcon);

    // Update popup content
    const newPopupHTML = createPopupHTML(volcano, nextStatus, true);
    marker.setPopupContent(newPopupHTML);

    // Make API call to update database
    fetch(apiEndpoint, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to update volcano status');
            }
            return response.json();
        })
        .then(data => {
            console.log('Status updated successfully:', data);
            marker.volcanoStatus = nextStatus;
            // Update numbers showed in the Legend
            updateLegendCounts(currentStatus, nextStatus);
            updateVolcanoCard(volcanoId, nextStatus); 
            console.log(`✓ ${volcano.name} is now: ${nextStatus || 'not in any list'}`);
        })
        .catch(error => {
            console.error('Error updating status:', error);

            // Revert the marker if API call failed
            marker.setIcon(createVolcanoIcon(currentStatus, true));
            marker.setPopupContent(createPopupHTML(volcano, currentStatus, true));

            alert('Failed to update volcano status. Please try again.');
        });
}

function updateLegendCounts(oldStatus, newStatus) {
    if (!window.userAuth.isAuthenticated) {
        return;
    }

    // Calculate changes (subtract from old status)
    if (oldStatus === 'visited') {
        visitedCount--;
    } else if (oldStatus === 'wishlist') {
        wishlistCount--;
    } else {  // oldStatus === null
        notVisitedCount--;
    }

    // Add to new status
    if (newStatus === 'visited') {
        visitedCount++;
    } else if (newStatus === 'wishlist') {
        wishlistCount++;
    } else {  // newStatus === null
        notVisitedCount++;
    }

    updateLegendHTML();
}

document.addEventListener('click', function (e) {
    // Check if clicked element is a cycle status button
    if (e.target.classList.contains('cycle-status-btn')) {
        const button = e.target;
        const volcanoId = parseInt(button.dataset.volcanoId);
        const currentStatus = button.dataset.currentStatus === 'null' ? null : button.dataset.currentStatus;

        // Find the marker for this volcano
        const marker = window.allMarkers?.find(m => m.volcanoData?.id === volcanoId);

        if (marker) {
            cycleVolcanoStatus(volcanoId, currentStatus, marker, marker.volcanoData);
        }
    }
});

// TODO: check if there is interference with the search feature
// TODO: refactore/ split code more clearly?
// TODO: update Readme
