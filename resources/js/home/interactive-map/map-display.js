/**
  * MAP DISPLAY - Visual Elements
  * 
  * This file contains:
  * - Marker creation and styling
  * - Popup HTML generation
  * - Legend updates
  * - Adding markers to the map
  */

// Make functions globally accessible
window.updateLegend = updateLegend;
window.updateLegendHTML = updateLegendHTML;
window.updateLegendCounts = updateLegendCounts;
window.createVolcanoIcon = createVolcanoIcon;
window.createPopupHTML = createPopupHTML;
window.addMarkers = addMarkers;

// ============================================
// LEGEND MANAGEMENT
// ============================================

function updateLegend(userStatusMap, totalVolcanoes) {
    const legendDiv = document.getElementById('map-legend');

    if (!window.userAuth.isAuthenticated) {
        legendDiv.style.display = 'none';
        return;
    }

    window.visitedCount = 0;
    window.wishlistCount = 0;

    Object.values(userStatusMap).forEach(status => {
        if (status === 'visited') window.visitedCount++;
        if (status === 'wishlist') window.wishlistCount++;
    });

    window.notVisitedCount = totalVolcanoes - window.visitedCount - window.wishlistCount;

    updateLegendHTML();
    legendDiv.style.display = 'none';
}

function updateLegendHTML() {
    const legendDiv = document.getElementById('map-legend');
    const legendText = document.getElementById('legend-text');

    legendText.innerHTML = `
        <strong>${window.userAuth.userName}</strong>, you have 
        <span style="color: #1e8449; font-weight: bold;">${window.visitedCount} visited volcano${window.visitedCount !== 1 ? 's' : ''}</span> 
        <span style="display: inline-block; width: 20px; height: 20px; background: #27ae60; border-radius: 50%; vertical-align: middle; margin: 0 4px;"></span>, 
        <span style="color: #d68910; font-weight: bold;">${window.wishlistCount} volcano${window.wishlistCount !== 1 ? 's' : ''} on your wish to visit list</span> 
        <span style="display: inline-block; width: 20px; height: 20px; background: #f39c12; border-radius: 50%; vertical-align: middle; margin: 0 4px;"></span>, 
        and <span style="color: #c0392b; font-weight: bold;">${window.notVisitedCount} volcano${window.notVisitedCount !== 1 ? 's' : ''} yet to discover!</span> 
        <span style="display: inline-block; width: 20px; height: 20px; background: #e74c3c; border-radius: 50%; vertical-align: middle; margin: 0 4px;"></span>
        <br>
        <span style="font-size: 14px; color: #7f8c8d; margin-top: 8px; display: inline-block;">
            💡 Click a volcano to access its switch status button.
        </span>
    `;
}

function updateLegendCounts(oldStatus, newStatus) {
    if (!window.userAuth.isAuthenticated) {
        return;
    }

    // Subtract from old status
    if (oldStatus === 'visited') {
        window.visitedCount--;
    } else if (oldStatus === 'wishlist') {
        window.wishlistCount--;
    } else {
        window.notVisitedCount--;
    }

    // Add to new status
    if (newStatus === 'visited') {
        window.visitedCount++;
    } else if (newStatus === 'wishlist') {
        window.wishlistCount++;
    } else {
        window.notVisitedCount++;
    }

    updateLegendHTML();
}


// ============================================
// MARKER CREATION
// ============================================

function createVolcanoIcon(userStatus, isAuthenticated) {
    let filter;

    if (!isAuthenticated) {
        filter = 'brightness(0) saturate(100%) invert(53%) sepia(89%) saturate(1750%) hue-rotate(177deg) brightness(96%) contrast(91%)';
    } else if (userStatus === 'visited') {
        filter = 'brightness(0) saturate(100%) invert(43%) sepia(96%) saturate(638%) hue-rotate(92deg) brightness(91%) contrast(88%)';
    } else if (userStatus === 'wishlist') {
        filter = 'brightness(0) saturate(100%) invert(68%) sepia(89%) saturate(1531%) hue-rotate(359deg) brightness(99%) contrast(95%)';
    } else {
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


// ============================================
// POPUP CREATION
// ============================================

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


// ============================================
// ADD MARKERS TO MAP
// ============================================

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

        marker.volcanoStatus = userStatus;
        marker.volcanoData = volcano;

        window.allMarkers.push(marker);

        const popupHTML = createPopupHTML(volcano, userStatus, isAuthenticated);
        marker.bindPopup(popupHTML);
        markers.addLayer(marker);
    });

    window.volcanoMap.addLayer(markers);

    console.log('All markers added with custom icons.');
}