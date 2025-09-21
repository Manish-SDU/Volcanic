// Show all Achievements Button
const togglebtn = document.getElementById("toggleAchievements");
const achievementsList = document.getElementById("lockedBadges");

togglebtn.addEventListener("click", () => {
    const expanded = achievementsList.classList.toggle("expanded");

    togglebtn.setAttribute("aria-expanded", expanded);
    togglebtn.textContent = expanded
    ? "Hide Locked Achievements"
    : "Show All Achievements";
});