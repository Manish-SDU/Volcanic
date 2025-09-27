/* ===========================================
   🌋 VOLCANIC - COMMON JAVASCRIPT LOGIC 🌋 
   =========================================== */
/**
 *
 * Year updating for the footer
 */
function updateYear() {
    const currentYearElement = document.getElementById('currentYear');
    if (currentYearElement) {
        const currentYear = new Date().getFullYear();
        currentYearElement.textContent = currentYear;
    }
}

function initializeCommonFeatures() {
    updateYear();
}

document.addEventListener('DOMContentLoaded', function() {
    initializeCommonFeatures();
});

window.VolcanicCommon = {
    updateYear: updateYear,
    initializeCommonFeatures: initializeCommonFeatures
};