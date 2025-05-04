// Dark mode functionality
document.addEventListener('DOMContentLoaded', function() {
    // Find the existing dark mode toggle
    const darkModeToggle = document.querySelector('.dark-mode-toggle');
    if (!darkModeToggle) return;
    
    // Check for saved dark mode preference
    const darkModeEnabled = localStorage.getItem('darkModeEnabled') === 'true';
    
    // Apply dark mode if enabled
    if (darkModeEnabled) {
        document.body.classList.add('dark-mode');
        updateDarkModeIcon(true);
    }
    
    // Add toggle event
    darkModeToggle.addEventListener('click', function() {
        const isDarkMode = document.body.classList.toggle('dark-mode');
        localStorage.setItem('darkModeEnabled', isDarkMode);
        updateDarkModeIcon(isDarkMode);
    });
});

// Update icon based on dark mode state
function updateDarkModeIcon(isDarkMode) {
    const icon = document.querySelector('.dark-mode-toggle i');
    if (icon) {
        icon.className = isDarkMode ? 'bi bi-moon-fill' : 'bi bi-sun-fill';
    }
}
