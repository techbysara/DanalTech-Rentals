// Main JavaScript File

// ── Theme Switcher ──
function applySavedTheme() {
    var savedTheme = localStorage.getItem('dtrTheme');
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-theme');
        document.getElementById('themeToggleBtn').textContent = 'Light';
    } else {
        document.body.classList.remove('dark-theme');
        document.getElementById('themeToggleBtn').textContent = 'Dark';
    }
}

function toggleTheme() {
    if (document.body.classList.contains('dark-theme')) {
        document.body.classList.remove('dark-theme');
        localStorage.setItem('dtrTheme', 'light');
        document.getElementById('themeToggleBtn').textContent = 'Dark';
    } else {
        document.body.classList.add('dark-theme');
        localStorage.setItem('dtrTheme', 'dark');
        document.getElementById('themeToggleBtn').textContent = 'Light';
    }
}

// Run on page load
applySavedTheme();