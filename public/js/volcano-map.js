let volcanoMap = null;
let volcanoMarkers = [];
let mapVisible = false;

// Initialize button styling when page loads
document.addEventListener('DOMContentLoaded', function() {
    updateMapButtonStyling();
});

function adjustBrightness(color, amount) {
    const usePound = color[0] === "#";
    const col = usePound ? color.slice(1) : color;
    const num = parseInt(col, 16);
    let r = (num >> 16) + amount;
    let g = (num >> 8 & 0x00FF) + amount;
    let b = (num & 0x0000FF) + amount;
    r = r > 255 ? 255 : r < 0 ? 0 : r;
    g = g > 255 ? 255 : g < 0 ? 0 : g;
    b = b > 255 ? 255 : b < 0 ? 0 : b;
    return (usePound ? "#" : "") + String("000000" + (r << 16 | g << 8 | b).toString(16)).slice(-6);
}

function initializeVolcanoMap() {
    volcanoMap = L.map('volcano-map', {
        zoomControl: true,
        scrollWheelZoom: true,
        doubleClickZoom: true,
        touchZoom: true,
        boxZoom: true,
        keyboard: true,
        zoomAnimation: true,
        fadeAnimation: true,
        markerZoomAnimation: true
    }).setView([20, 0], 2);
/*
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors',
        maxZoom: 18,
        minZoom: 2,
        tileSize: 256,
        detectRetina: true,
        className: 'map-tiles'
    }).addTo(volcanoMap);*/

    loadVolcanoMarkers();

    volcanoMap.on('zoomstart', function() {
        volcanoMap.getContainer().style.cursor = 'grabbing';
    });

    volcanoMap.on('zoomend', function() {
        volcanoMap.getContainer().style.cursor = '';
    });

    volcanoMap.on('movestart', function() {
        volcanoMap.getContainer().style.cursor = 'grabbing';
    });

    volcanoMap.on('moveend', function() {
        volcanoMap.getContainer().style.cursor = '';
    });
}

function toggleMap() {
    const mapContainer = document.getElementById('volcano-map');
    const mapDescription = document.getElementById('map-description');
    const pillText = document.getElementById('map-pill-text');
    const mapTogglePill = document.getElementById('map-toggle-pill');
    
    if (mapVisible) {
        // Hiding the map
        mapContainer.style.display = 'none';
        if (mapDescription) mapDescription.style.display = 'none';
        if (pillText) pillText.textContent = 'View Map';
        mapVisible = false;
    } else {
        // Showing the map
        mapContainer.style.display = 'block';
        if (mapDescription) mapDescription.style.display = 'block';
        if (pillText) pillText.textContent = 'Hide Map';
        mapVisible = true;
        
        // Initialize map if it hasn't been initialized yet
        if (!volcanoMap) {
            console.log('Map not initialized, initializing now...');
            initializeVolcanoMap();
        } else {
            setTimeout(() => volcanoMap.invalidateSize(), 100);
        }
    }
    
    // Update button styling based on current state
    updateMapButtonStyling();
}

function updateMapButtonStyling() {
    const mapTogglePill = document.getElementById('map-toggle-pill');
    
    if (!mapTogglePill) return;
    
    if (mapVisible) {
        // Map is visible, button says "Hide Map" - blue background
        mapTogglePill.style.borderColor = '#3498db';
        mapTogglePill.style.color = 'white';
        mapTogglePill.style.backgroundColor = '#3498db';
    } else {
        // Map is hidden, button says "View Map" - white background
        mapTogglePill.style.borderColor = '#3498db';
        mapTogglePill.style.color = '#3498db';
        mapTogglePill.style.backgroundColor = 'white';
    }
}

async function loadVolcanoMarkers() {

    try {
        const response = await fetch('/api/volcanoes');
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success && data.data && Array.isArray(data.data)) {
            if (data.count > 0) {
                addVolcanoMarkersToMap(data.data);
            } else {
                showMapError('No volcano data available');
            }
        } else {
            showMapError('Failed to load volcano data: ' + (data.error || 'Unknown error'));
        }
    } catch (error) {
        showMapError(`Error loading volcano locations: ${error.message}`);
    }
}

function addVolcanoMarkersToMap(volcanoes) {
    volcanoes.forEach(volcano => {
        // Skip volcanoes without valid coordinates
        if (!volcano.latitude || !volcano.longitude) {
            console.warn(`Skipping volcano ${volcano.name} - missing coordinates`);
            return;
        }

        // Convert coordinates to numbers in case they come as strings
        const lat = parseFloat(volcano.latitude);
        const lng = parseFloat(volcano.longitude);
        
        // Validate coordinates are valid numbers
        if (isNaN(lat) || isNaN(lng)) {
            console.warn(`Skipping volcano ${volcano.name} - invalid coordinates: lat=${volcano.latitude}, lng=${volcano.longitude}`);
            return;
        }

        // 📌 UNCOMMENT THE LINES BELOW TO DISABLE MAP MARKERS
         console.log(`Skipping marker creation for ${volcano.name} - markers disabled`);
         return;

        const icon = createVolcanoIcon(volcano.activity);
        const marker = L.marker([lat, lng], { icon })
            .addTo(volcanoMap);

        const popupContent = createVolcanoPopup(volcano);
        marker.bindPopup(popupContent, {
            closeButton: false,
            className: 'custom-popup',
            autoPan: false
        });
        volcanoMarkers.push(marker);
    });

    console.log(`Added ${volcanoMarkers.length} volcano markers to the map`);
}

function createVolcanoIcon(activity) {
    let color = '#e74c3c';
    let size = [28, 28];
    let borderColor = '#fff';
    let shadowClass = '';

    switch (activity?.toLowerCase()) {
        case 'active':
        case 'erupting':
            color = '#e74c3c';
            size = [32, 32];
            borderColor = '#fff';
            shadowClass = 'volcano-active-glow';
            break;
        case 'inactive':
        case 'dormant':
        case 'sleeping':
            color = '#ff8c00';
            size = [26, 26];
            borderColor = '#fff';
            break;
        case 'extinct':
            color = '#95a5a6';
            size = [22, 22];
            borderColor = '#ecf0f1';
            break;
        default:
            color = '#f39c12';
    }

    return L.divIcon({
        className: `volcano-marker ${shadowClass}`,
        html: `<div style="
            background: linear-gradient(135deg, ${color} 0%, ${adjustBrightness(color, -20)} 100%);
            width: ${size[0]}px;
            height: ${size[1]}px;
            border-radius: 50%;
            border: 3px solid ${borderColor};
            box-shadow: 0 4px 12px rgba(0,0,0,0.3), 0 2px 6px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: white;
            font-weight: bold;
            text-shadow: 0 1px 2px rgba(0,0,0,0.5);
            cursor: pointer;
            transition: all 0.3s ease;
        ">🌋</div>`,
        iconSize: size,
        iconAnchor: [size[0] / 2, size[1] / 2],
        popupAnchor: [0, -size[1] / 2]
    });
}

/**
 * @param {Object} volcano
 * @returns {string} HTML content for popup
 */
function createVolcanoPopup(volcano) {
    const elevation = volcano.elevation ? `${volcano.elevation.toLocaleString()}m` : 'Unknown';
    const activityColor = getActivityColor(volcano.activity);
    const latitude = volcano.latitude ? parseFloat(volcano.latitude).toFixed(4) : 'Unknown';
    const longitude = volcano.longitude ? parseFloat(volcano.longitude).toFixed(4) : 'Unknown';
    
    return `
        <div class="volcano-popup">
            <div class="volcano-popup-header" style="background: ${activityColor};">
                <h3>${volcano.name}</h3>
            </div>
            <div class="volcano-popup-content">
                <div class="info-row">
                    <span class="icon">📍</span>
                    <span class="label">Location</span>
                    <span class="value">${volcano.country}</span>
                </div>
                <div class="info-row">
                    <span class="icon">🌍</span>
                    <span class="label">Latitude</span>
                    <span class="value">${latitude}°</span>
                </div>
                <div class="info-row">
                    <span class="icon">🌍</span>
                    <span class="label">Longitude</span>
                    <span class="value">${longitude}°</span>
                </div>
                <div class="info-row">
                    <span class="icon">🌋</span>
                    <span class="label">Type</span>
                    <span class="value">${volcano.type || 'Unknown'}</span>
                </div>
                <div class="info-row">
                    <span class="icon">🔺</span>
                    <span class="label">Elevation</span>
                    <span class="value">${elevation}</span>
                </div>
                <div class="info-row">
                    <span class="icon">⚡</span>
                    <span class="label">Activity</span>
                    <span class="value">${volcano.activity || 'Unknown'}</span>
                </div>
            </div>
        </div>
    `;
}

/**
 * @param {string} activity - Volcano activity level
 * @returns {string}
 */
function getActivityColor(activity) {
    switch (activity?.toLowerCase()) {
        case 'active':
        case 'erupting':
            return '#e74c3c';
        case 'inactive':
        case 'dormant':
        case 'sleeping':
            return '#ff8c00';
        case 'extinct':
            return '#95a5a6';
        default:
            return '#f39c12';
    }
}

/**
 * error message on map
 * @param {string} message
 */
function showMapError(message) {
    if (volcanoMap) {
        const errorPopup = L.popup()
            .setLatLng([20, 0])
            .setContent(`<div style="color: red; text-align: center;">${message}</div>`)
            .openOn(volcanoMap);
    }
}

/**
 * DOM is loaded
 */
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('volcano-map')) {
        console.log('Map container found, ready to initialize when shown...');
        document.getElementById('volcano-map').style.display = 'none';
        mapVisible = false;
        // Don't initialize map immediately - wait for user to click "View Map"
    }
});

window.addEventListener('resize', function() {
    if (volcanoMap) {
        setTimeout(() => volcanoMap.invalidateSize(), 300);
    }
});