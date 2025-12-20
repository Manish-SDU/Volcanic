/**
 * MAP SYNC - User Interactions & Bi-directional Sync
 * 
 * This file contains:
 * - Cycling volcano status from map markers
 * - Updating volcano cards when map changes
 * - Updating map markers when cards change
 * - Event listeners and initialization
 */

// Make globally accessible for volcano-actions.js
window.updateVolcanoMarker = updateVolcanoMarker;

// ============================================
// INITIALIZATION
// ============================================

document.addEventListener('DOMContentLoaded', function () {
    console.log('Page ready.');
    window.initializeMap();
    window.loadVolcanoes();
});


// ============================================
// MAP → CARD SYNC
// ============================================

function cycleVolcanoStatus(volcanoId, currentStatus, marker, volcano) {
    if (!window.userAuth.isAuthenticated) {
        alert('Please log in to track volcanoes.');
        return;
    }

    let nextStatus;
    let apiEndpoint;

    if (currentStatus === null) {
        nextStatus = 'wishlist';
        apiEndpoint = `/user/volcanoes/${volcanoId}/wishlist`;
    } else if (currentStatus === 'wishlist') {
        nextStatus = 'visited';
        apiEndpoint = `/user/volcanoes/${volcanoId}/visited`;
    } else if (currentStatus === 'visited') {
        nextStatus = null;
        apiEndpoint = `/user/volcanoes/${volcanoId}/visited`;
    }

    console.log(`Cycling ${volcano.name}: ${currentStatus} -> ${nextStatus}`);

    const newIcon = createVolcanoIcon(nextStatus, true);
    marker.setIcon(newIcon);

    const newPopupHTML = createPopupHTML(volcano, nextStatus, true);
    marker.setPopupContent(newPopupHTML);

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
            updateLegendCounts(currentStatus, nextStatus);
            updateVolcanoCard(volcanoId, nextStatus);

            console.log(`✓ ${volcano.name} is now: ${nextStatus || 'not in any list'}`);
        })
        .catch(error => {
            console.error('Error updating status:', error);

            marker.setIcon(createVolcanoIcon(currentStatus, true));
            marker.setPopupContent(createPopupHTML(volcano, currentStatus, true));

            alert('Failed to update volcano status. Please try again.');
        });
}

function updateVolcanoCard(volcanoId, newStatus) {
    const card = document.querySelector(`.volcano-card[data-volcano-id="${volcanoId}"]`);
    if (!card) return;

    const visitedBtn = card.querySelector('.visited-btn');
    const wishlistBtn = card.querySelector('.wishlist-btn');

    if (!visitedBtn || !wishlistBtn) return;

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
}


// ============================================
// CARD → MAP SYNC
// ============================================

function updateVolcanoMarker(volcanoId, newStatus) {
    const marker = window.allMarkers?.find(m => m.volcanoData?.id === volcanoId);
    if (!marker) return;

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


// ============================================
// EVENT LISTENERS
// ============================================

document.addEventListener('click', function (e) {
    if (e.target.classList.contains('cycle-status-btn')) {
        const button = e.target;
        const volcanoId = parseInt(button.dataset.volcanoId);
        const currentStatus = button.dataset.currentStatus === 'null' ? null : button.dataset.currentStatus;

        const marker = window.allMarkers?.find(m => m.volcanoData?.id === volcanoId);

        if (marker) {
            cycleVolcanoStatus(volcanoId, currentStatus, marker, marker.volcanoData);
        }
    }
});