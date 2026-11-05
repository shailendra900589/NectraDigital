document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    var html = document.documentElement;
    var themeBtn = document.getElementById('themeToggle');
    var savedTheme = localStorage.getItem('nd_admin_theme');
    if (!savedTheme && localStorage.getItem('ge-theme')) {
        savedTheme = localStorage.getItem('ge-theme');
        localStorage.setItem('nd_admin_theme', savedTheme);
    }
    if (!savedTheme) {
        savedTheme = 'light';
    }
    html.setAttribute('data-theme', savedTheme);
    updateThemeIcon(savedTheme);

    if (themeBtn) {
        themeBtn.addEventListener('click', function () {
            var next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            localStorage.setItem('nd_admin_theme', next);
            updateThemeIcon(next);
        });
    }

    function updateThemeIcon(theme) {
        if (!themeBtn) return;
        themeBtn.innerHTML = theme === 'dark' ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
    }

    var sidebar = document.getElementById('adminSidebar');
    var sidebarToggle = document.getElementById('sidebarToggle');
    var backdrop = document.getElementById('sidebarBackdrop');

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
        sidebarToggle.addEventListener('click', function () {
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
        sidebar.querySelectorAll('.ge-nav-link').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth < 992) closeSidebar();
            });
        });
    }

    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            var msg = this.getAttribute('data-confirm') || 'Are you sure you want to proceed?';
            if (!confirm(msg)) {
                e.preventDefault();
            }
        });
    });

    document.querySelectorAll('[data-copy]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            var textToCopy = this.getAttribute('data-copy');
            if (!navigator.clipboard || !textToCopy) return;
            navigator.clipboard.writeText(textToCopy).then(function () {
                var original = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check text-success"></i> Copied';
                setTimeout(function () {
                    btn.innerHTML = original;
                }, 2000);
            });
        });
    });
});

function toggleDetails(id) {
    var row = document.getElementById('details-' + id);
    if (!row) return;
    row.style.display = row.style.display === 'none' || row.style.display === '' ? 'table-row' : 'none';
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
