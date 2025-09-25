/* ===========================================
   🌋 VOLCANIC - COMMON JAVASCRIPT 🌋 
   Shared functionality across all pages
   =========================================== */

/**
 * Dynamic year updating for footer copyright
 */
function updateYear() {
    const currentYearElement = document.getElementById('currentYear');
    if (currentYearElement) {
        const currentYear = new Date().getFullYear();
        currentYearElement.textContent = currentYear;
    }
}

/**
 * Initialize common functionality when DOM loads
 * This should be called on every page that uses common features
 */
function initializeCommonFeatures() {
    updateYear();
    
    // Add any other common functionality here in the future
    // For example: common navigation behavior, global event listeners, etc.
}

// Auto-initialize common features when DOM loads
document.addEventListener('DOMContentLoaded', function() {
    initializeCommonFeatures();
});

// Export functions for manual initialization if needed
window.VolcanicCommon = {
    updateYear: updateYear,
    initializeCommonFeatures: initializeCommonFeatures
};