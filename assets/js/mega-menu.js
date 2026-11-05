(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var root = document.querySelector('.nd-dropdown-mega');
        if (!root) return;

        var toggle = root.querySelector('.dropdown-toggle');
        var panel = root.querySelector('.nd-mega-menu');
        if (!toggle || !panel) return;

        var openClass = 'show';
        var hoverCapable = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

        function openMenu() {
            root.classList.add(openClass);
            toggle.setAttribute('aria-expanded', 'true');
        }

        function closeMenu() {
            root.classList.remove(openClass);
            toggle.setAttribute('aria-expanded', 'false');
        }

        function isOpen() {
            return root.classList.contains(openClass);
        }

        toggle.addEventListener('click', function (e) {
            if (hoverCapable) return;
            e.preventDefault();
            if (isOpen()) closeMenu();
            else openMenu();
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && isOpen()) closeMenu();
        });

        document.addEventListener('click', function (e) {
            if (!isOpen()) return;
            if (!root.contains(e.target)) closeMenu();
        });

        if (hoverCapable) {
            root.addEventListener('mouseenter', openMenu);
            root.addEventListener('mouseleave', closeMenu);
            toggle.addEventListener('focus', openMenu);
            root.addEventListener('focusout', function (e) {
                if (!root.contains(e.relatedTarget)) closeMenu();
            });
        }
    });
})();
