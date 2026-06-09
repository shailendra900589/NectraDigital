document.addEventListener("DOMContentLoaded", function () {
    if (document.querySelector('.nectra-floating-wrap')) {
        return;
    }

    // Remove legacy duplicate chatbot toggle (cached scripts / old deploy)
    const orphanToggle = document.getElementById('nectra-chatbot-toggle');
    if (orphanToggle) {
        orphanToggle.remove();
    }

    const hasChatbotPanel = !!document.getElementById('nectra-chatbot-panel');
    const chatbotIntegrated = window.NECTRA_CHATBOT_INTEGRATED === true
        || typeof window.openNectraChatbot === 'function'
        || hasChatbotPanel;

    let currentPageTitle = document.title.split('|')[0].trim();
    let waMessage = encodeURIComponent(`Hi Nectra Digital, I am interested in: ${currentPageTitle}. Can we connect?`);

    const whatsappNumber = "+917678387759";
    const phoneNumber = "+917678387759";
    const emailAddress = "contact@nectradigital.com";

    const aiMenuItem = chatbotIntegrated ? `
            <button type="button" class="nectra-contact-item item-ai" id="nectraOpenChatbot" data-tooltip="AI Assistant" aria-label="Open AI Assistant" role="menuitem">
                <i class="fas fa-robot"></i>
            </button>` : '';

    const wrapper = document.createElement('div');
    wrapper.className = 'nectra-floating-wrap';
    wrapper.innerHTML = `
        <div class="nectra-contact-menu" id="nectraFloatingMenu" role="menu">
            ${aiMenuItem}
            <a href="https://wa.me/${whatsappNumber}?text=${waMessage}" target="_blank" rel="noopener" class="nectra-contact-item item-whatsapp track-click" data-network="whatsapp" data-tooltip="WhatsApp Us" aria-label="Contact on WhatsApp" role="menuitem">
                <i class="fab fa-whatsapp"></i>
            </a>
            <a href="tel:${phoneNumber}" class="nectra-contact-item item-call track-click" data-network="phone" data-tooltip="Direct Call" aria-label="Make a Phone Call" role="menuitem">
                <i class="fas fa-phone-alt"></i>
            </a>
            <a href="mailto:${emailAddress}" class="nectra-contact-item item-mail track-click" data-network="email" data-tooltip="Send Email" aria-label="Send an Email" role="menuitem">
                <i class="fas fa-envelope"></i>
            </a>
        </div>
        <button type="button" class="nectra-main-btn" id="nectraFloatingToggle" aria-label="Open Support Menu" aria-expanded="false">
            <i class="fas fa-comment-dots" id="nectraToggleIcon"></i>
        </button>
    `;
    document.body.appendChild(wrapper);

    const toggleBtn = document.getElementById('nectraFloatingToggle');
    const menu = document.getElementById('nectraFloatingMenu');
    const icon = document.getElementById('nectraToggleIcon');

    function closeContactMenu() {
        menu.classList.remove('active');
        toggleBtn.setAttribute('aria-expanded', 'false');
        icon.classList.remove('fa-times');
        icon.classList.add('fa-comment-dots');
        icon.style.transform = 'scale(1) rotate(0deg)';
    }

    if (chatbotIntegrated) {
        const aiBtn = document.getElementById('nectraOpenChatbot');
        if (aiBtn) {
            aiBtn.addEventListener('click', () => {
                closeContactMenu();
                if (typeof window.openNectraChatbot === 'function') {
                    window.openNectraChatbot();
                }
            });
        }
    }

    toggleBtn.addEventListener('click', () => {
        const isActive = menu.classList.toggle('active');
        toggleBtn.setAttribute('aria-expanded', isActive ? 'true' : 'false');

        icon.style.transform = 'scale(0) rotate(180deg)';
        setTimeout(() => {
            if (isActive) {
                icon.classList.remove('fa-comment-dots');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-comment-dots');
            }
            icon.style.transform = 'scale(1) rotate(360deg)';
        }, 200);
    });

    document.addEventListener('click', (e) => {
        if (!wrapper.contains(e.target)) {
            closeContactMenu();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeContactMenu();
            if (typeof window.closeNectraChatbot === 'function') {
                window.closeNectraChatbot();
            }
        }
    });

    const trackingLinks = document.querySelectorAll('.track-click');
    trackingLinks.forEach(link => {
        link.addEventListener('click', function () {
            let network = this.getAttribute('data-network');
            if (typeof gtag === 'function') {
                gtag('event', 'generate_lead', {
                    'event_category': 'Contact Widget',
                    'event_label': network,
                    'value': 1
                });
            }
            if (typeof dataLayer !== 'undefined') {
                dataLayer.push({
                    'event': 'contact_click',
                    'network': network
                });
            }
        });
    });
});
