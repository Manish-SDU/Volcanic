/**
 * MAP CORE - Foundation and Initialization
 * 
 * This file contains:
 * - Global variables shared across all map modules
 * - Map initialization
 * - Map toggle functionality
 * - Data loading (volcanoes and user lists)
 */

// ============================================
// GLOBAL VARIABLES
// ============================================

let volcanoMap = null;
let allMarkers = [];
let visitedCount = 0;
let wishlistCount = 0;
let notVisitedCount = 0;

// Make accessible to other files and globally
window.visitedCount = 0;
window.wishlistCount = 0;
window.notVisitedCount = 0;
window.allMarkers = allMarkers;
window.toggleMap = toggleMap;
window.initializeMap = initializeMap;
window.loadVolcanoes = loadVolcanoes;


// ============================================
// MAP INITIALIZATION
// ============================================

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
    window.volcanoMap = volcanoMap; // Make accessible to other files and globally

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 18,
    }).addTo(volcanoMap);

    console.log('Map initialized successfully.');
}


// ============================================
// MAP TOGGLE
// ============================================

function toggleMap() {
    console.log('Toggle map clicked.');

    const mapContainer = document.getElementById('interactive-map');
    const legendDiv = document.getElementById('map-legend');
    const buttonText = document.getElementById('map-pill-text');

    if (mapContainer.style.display === 'none') {
        mapContainer.style.display = 'block';
        if (window.userAuth.isAuthenticated && legendDiv) {
            legendDiv.style.display = 'block';
        }
        buttonText.textContent = 'Hide Map';

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


// ============================================
// DATA LOADING
// ============================================

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