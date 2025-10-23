const panels = ["visited", "wish"];
let currentIndex = 0;

function nextPanel() {
    // hide current
    document.getElementById(panels[currentIndex]).style.display = "none";

    // move to next (loop back if at end)
    currentIndex = (currentIndex + 1) % panels.length;

    // show next
    document.getElementById(panels[currentIndex]).style.display = "block";
}

// expose to global so inline onclick works with Vite/bundled modules
window.nextPanel = nextPanel;