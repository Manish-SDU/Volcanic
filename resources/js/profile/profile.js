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

// Delete Account Confirmation - Custom Modal
  const deleteForm = document.querySelector('.delete-form');
  const deleteModal = document.getElementById('deleteModal');
  const cancelBtn = document.getElementById('cancelDelete');
  const confirmBtn = document.getElementById('confirmDelete');

  if (deleteForm && deleteModal) {
      // Show modal when delete button is clicked
      deleteForm.addEventListener('submit', function(event) {
          event.preventDefault();
          deleteModal.classList.add('active');
      });

      // Close modal on cancel
      cancelBtn.addEventListener('click', function() {
          deleteModal.classList.remove('active');
      });

      // Submit form on confirm
      confirmBtn.addEventListener('click', function() {
          deleteForm.submit();
      });

      // Close modal when clicking outside
      deleteModal.addEventListener('click', function(event) {
          if (event.target === deleteModal) {
              deleteModal.classList.remove('active');
          }
      });
  }