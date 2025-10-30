const panels = ["visited", "wish"];
let currentIndex = 0;

function nextPanel() {
    // Hide current
    document.getElementById(panels[currentIndex]).style.display = "none";

    // Move to next
    currentIndex = (currentIndex + 1) % panels.length;

    // Show next
    document.getElementById(panels[currentIndex]).style.display = "block";
}

function prevPanel() {
    // Hide current
    document.getElementById(panels[currentIndex]).style.display = "none";

    // Move to previous
    currentIndex = (currentIndex - 1 + panels.length) % panels.length;

    // Show previous
    document.getElementById(panels[currentIndex]).style.display = "block";
}

// Expose to global so onclick works
window.nextPanel = nextPanel;
window.prevPanel = prevPanel;