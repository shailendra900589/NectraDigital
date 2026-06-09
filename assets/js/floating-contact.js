(function () {
    'use strict';

    function cleanupFloatingWidgets() {
        document.querySelectorAll('#nectra-chatbot-toggle').forEach(function (el) {
            el.remove();
        });
        var wraps = document.querySelectorAll('.nectra-floating-wrap');
        for (var i = 1; i < wraps.length; i++) {
            wraps[i].remove();
        }
    }

    function isChatbotIntegrated() {
        return window.NECTRA_CHATBOT_INTEGRATED === true
            || typeof window.openNectraChatbot === 'function'
            || !!document.getElementById('nectra-chatbot-panel');
    }

    function buildMenuHtml(chatbotIntegrated) {
        var currentPageTitle = document.title.split('|')[0].trim();
        var waMessage = encodeURIComponent('Hi Nectra Digital, I am interested in: ' + currentPageTitle + '. Can we connect?');
        var whatsappNumber = '+917678387759';
        var phoneNumber = '+917678387759';
        var emailAddress = 'contact@nectradigital.com';

        var html = '';
        if (chatbotIntegrated) {
            html += '<button type="button" class="nectra-contact-item item-ai" id="nectraOpenChatbot" data-tooltip="AI Assistant" aria-label="Open AI Assistant" role="menuitem">' +
                '<i class="fas fa-robot"></i></button>';
        }
        html += '<a href="https://wa.me/' + whatsappNumber + '?text=' + waMessage + '" target="_blank" rel="noopener" class="nectra-contact-item item-whatsapp track-click" data-network="whatsapp" data-tooltip="WhatsApp Us" aria-label="Contact on WhatsApp" role="menuitem">' +
            '<i class="fab fa-whatsapp"></i></a>';
        html += '<a href="tel:' + phoneNumber + '" class="nectra-contact-item item-call track-click" data-network="phone" data-tooltip="Direct Call" aria-label="Make a Phone Call" role="menuitem">' +
            '<i class="fas fa-phone-alt"></i></a>';
        html += '<a href="mailto:' + emailAddress + '" class="nectra-contact-item item-mail track-click" data-network="email" data-tooltip="Send Email" aria-label="Send an Email" role="menuitem">' +
            '<i class="fas fa-envelope"></i></a>';
        return html;
    }

    function trackClick(network) {
        if (typeof gtag === 'function') {
            gtag('event', 'generate_lead', {
                event_category: 'Contact Widget',
                event_label: network,
                value: 1
            });
        }
        if (typeof dataLayer !== 'undefined') {
            dataLayer.push({ event: 'contact_click', network: network });
        }
    }

    function initFloatingContact() {
        cleanupFloatingWidgets();

        var existing = document.querySelector('.nectra-floating-wrap');
        if (existing) {
            existing.remove();
        }

        var chatbotIntegrated = isChatbotIntegrated();

        var wrapper = document.createElement('div');
        wrapper.className = 'nectra-floating-wrap';
        wrapper.innerHTML =
            '<div class="nectra-contact-menu" id="nectraFloatingMenu" role="menu" aria-hidden="true"></div>' +
            '<button type="button" class="nectra-main-btn" id="nectraFloatingToggle" aria-label="Open Support Menu" aria-expanded="false">' +
            '<i class="fas fa-comment-dots" id="nectraToggleIcon"></i></button>';

        document.body.appendChild(wrapper);

        var toggleBtn = document.getElementById('nectraFloatingToggle');
        var menu = document.getElementById('nectraFloatingMenu');
        var icon = document.getElementById('nectraToggleIcon');

        function bindMenuActions() {
            var aiBtn = document.getElementById('nectraOpenChatbot');
            if (aiBtn) {
                aiBtn.addEventListener('click', function () {
                    closeContactMenu();
                    if (typeof window.openNectraChatbot === 'function') {
                        window.openNectraChatbot();
                    }
                });
            }
            menu.querySelectorAll('.track-click').forEach(function (link) {
                link.addEventListener('click', function () {
                    trackClick(this.getAttribute('data-network'));
                });
            });
        }

        function openContactMenu() {
            if (!menu.innerHTML) {
                menu.innerHTML = buildMenuHtml(isChatbotIntegrated());
                bindMenuActions();
            }
            menu.classList.add('active');
            menu.setAttribute('aria-hidden', 'false');
            toggleBtn.setAttribute('aria-expanded', 'true');

            icon.style.transform = 'scale(0) rotate(180deg)';
            setTimeout(function () {
                icon.classList.remove('fa-comment-dots');
                icon.classList.add('fa-times');
                icon.style.transform = 'scale(1) rotate(360deg)';
            }, 200);
        }

        function closeContactMenu() {
            menu.classList.remove('active');
            menu.setAttribute('aria-hidden', 'true');
            menu.innerHTML = '';
            toggleBtn.setAttribute('aria-expanded', 'false');
            icon.classList.remove('fa-times');
            icon.classList.add('fa-comment-dots');
            icon.style.transform = 'scale(1) rotate(0deg)';
        }

        toggleBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            if (menu.classList.contains('active')) {
                closeContactMenu();
            } else {
                openContactMenu();
            }
        });

        document.addEventListener('click', function (e) {
            if (!wrapper.contains(e.target)) {
                closeContactMenu();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeContactMenu();
                if (typeof window.closeNectraChatbot === 'function') {
                    window.closeNectraChatbot();
                }
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFloatingContact);
    } else {
        initFloatingContact();
    }

    window.addEventListener('load', cleanupFloatingWidgets);
})();
