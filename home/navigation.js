// Navigation functionality for tab system
document.addEventListener('DOMContentLoaded', function() {


    const navTabs = document.querySelectorAll('.nav-tab');
    
    navTabs.forEach((tab, index) => {
        tab.addEventListener('click', function() {
            // Small delay for the animation navigation
            setTimeout(() => {
                switch(index) {
                    case 0:
                        window.location.href = 'index.html';
                        break;
                    case 1:
                        window.location.href = 'my-volcanoes.html';
                        break;
                    case 2:
                        window.location.href = 'profile.html';
                        break;
                }
            }, 250);
        });
    });
});