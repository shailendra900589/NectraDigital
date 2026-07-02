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

    const sidebar = document.getElementById('adminSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const backdrop = document.getElementById('sidebarBackdrop');

    function closeSidebar() {
        if (!sidebar) return;
        sidebar.classList.remove('ge-sidebar-open');
        if (backdrop) backdrop.classList.remove('show');
        document.body.style.overflow = '';
    }

    function openSidebar() {
        if (!sidebar) return;
        sidebar.classList.add('ge-sidebar-open');
        if (backdrop) backdrop.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            if (sidebar.classList.contains('ge-sidebar-open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    }

    if (backdrop) {
        backdrop.addEventListener('click', closeSidebar);
    }

    if (sidebar) {
        sidebar.querySelectorAll('.ge-nav-link').forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth < 768) closeSidebar();
            });
        });
    }
});

function toggleDetails(id) {
    var row = document.getElementById('details-' + id);
    if (!row) return;
    row.style.display = (row.style.display === 'none' || row.style.display === '') ? 'table-row' : 'none';
}

function toggleAdType(val) {
    var imgGroup = document.querySelector('.ad-image-group');
    var codeGroup = document.querySelector('.ad-code-group');
    if (!imgGroup || !codeGroup) return;
    if (val === 'code') {
        imgGroup.style.display = 'none';
        codeGroup.style.display = 'block';
    } else {
        imgGroup.style.display = 'block';
        codeGroup.style.display = 'none';
    }
}
