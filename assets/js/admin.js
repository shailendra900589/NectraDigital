/**
 * Nectra Growth Engine — Admin Application Script
 * Enterprise SaaS Control Center
 */

document.addEventListener("DOMContentLoaded", function() {
    "use strict";

    // 1. Mobile Sidebar Toggle
    const sidebar = document.getElementById('adminSidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    const backdrop = document.getElementById('sidebarBackdrop');

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('ge-sidebar-open');
            if (backdrop) backdrop.classList.toggle('show');
        });
    }

    if (backdrop && sidebar) {
        backdrop.addEventListener('click', function() {
            sidebar.classList.remove('ge-sidebar-open');
            backdrop.classList.remove('show');
        });
    }

    // 2. Dark / Light Theme Toggle
    const themeBtn = document.getElementById('themeToggle');
    if (themeBtn) {
        const savedTheme = localStorage.getItem('nd_admin_theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
        updateThemeIcon(savedTheme);

        themeBtn.addEventListener('click', function() {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            const nextTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', nextTheme);
            localStorage.setItem('nd_admin_theme', nextTheme);
            updateThemeIcon(nextTheme);
        });
    }

    function updateThemeIcon(theme) {
        if (!themeBtn) return;
        const icon = themeBtn.querySelector('i');
        if (icon) {
            icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        }
    }

    // 3. Destructive Action Confirmation Guard (Item 151)
    document.querySelectorAll('[data-confirm]').forEach(function(el) {
        el.addEventListener('click', function(e) {
            const msg = this.getAttribute('data-confirm') || 'Are you sure you want to proceed with this action?';
            if (!confirm(msg)) {
                e.preventDefault();
                return false;
            }
        });
    });

    // 4. Quick Copy Button Support
    document.querySelectorAll('[data-copy]').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const textToCopy = this.getAttribute('data-copy');
            if (navigator.clipboard && textToCopy) {
                navigator.clipboard.writeText(textToCopy).then(function() {
                    const originalText = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-check text-success"></i> Copied!';
                    setTimeout(function() {
                        btn.innerHTML = originalText;
                    }, 2000);
                });
            }
        });
    });
});
