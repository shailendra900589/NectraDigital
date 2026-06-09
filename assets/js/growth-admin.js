document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('themeToggle');
    const html = document.documentElement;
    const saved = localStorage.getItem('ge-theme') || 'dark';
    html.setAttribute('data-theme', saved);
    updateIcon(saved);

    if (toggle) {
        toggle.addEventListener('click', function() {
            const next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            localStorage.setItem('ge-theme', next);
            updateIcon(next);
        });
    }

    function updateIcon(theme) {
        if (!toggle) return;
        toggle.innerHTML = theme === 'dark' ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
    }
});
