document.addEventListener("DOMContentLoaded", function() {
    // 1. INJECT CUSTOM CSS (With Tooltips & Animations)
    const style = document.createElement('style');
    style.innerHTML = `
        /* Wrapper positioning */
        .nectra-floating-wrap {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 9999;
            display: flex;
            flex-direction: column-reverse;
            align-items: center;
            gap: 15px;
            font-family: 'Inter', sans-serif;
        }

        /* Main Toggle Button */
        .nectra-main-btn {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #00f2ff;
            color: #050505;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 26px;
            cursor: pointer;
            border: none;
            box-shadow: 0 0 15px rgba(0, 242, 255, 0.4), inset 0 0 10px rgba(255,255,255,0.5);
            transition: all 0.3s ease;
            animation: nectra-pulse 2s infinite;
        }
        
        .nectra-main-btn:hover {
            transform: scale(1.1);
            animation: none;
            box-shadow: 0 0 25px rgba(0, 242, 255, 0.8);
        }

        @keyframes nectra-pulse {
            0% { box-shadow: 0 0 0 0 rgba(0, 242, 255, 0.7); }
            70% { box-shadow: 0 0 0 15px rgba(0, 242, 255, 0); }
            100% { box-shadow: 0 0 0 0 rgba(0, 242, 255, 0); }
        }

        .nectra-main-btn i {
            transition: transform 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        /* Menu Container */
        .nectra-contact-menu {
            display: flex;
            flex-direction: column;
            gap: 12px;
            pointer-events: none;
        }

        /* Individual Social Buttons */
        .nectra-contact-item {
            position: relative;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
            font-size: 20px;
            text-decoration: none;
            box-shadow: 0 4px 10px rgba(0,0,0,0.5);
            border: 1px solid rgba(255,255,255,0.1);
            
            /* Hidden State */
            opacity: 0;
            transform: translateY(30px) scale(0.3) rotate(-90deg);
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        /* Tooltip Setup (SEO Friendly & UX Booster) */
        .nectra-contact-item::before {
            content: attr(data-tooltip);
            position: absolute;
            right: 65px;
            background: rgba(10, 10, 10, 0.9);
            color: #fff;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transform: translateX(10px);
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 242, 255, 0.3);
            box-shadow: 0 2px 8px rgba(0,0,0,0.5);
        }

        .nectra-contact-item:hover::before {
            opacity: 1;
            transform: translateX(0);
        }

        /* Active State */
        .nectra-contact-menu.active .nectra-contact-item {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0) scale(1) rotate(0);
        }

        .nectra-contact-item:hover {
            color: #fff;
            transform: scale(1.15) !important;
        }

        /* Brand Colors */
        .item-whatsapp { background: #25D366; }
        .item-call     { background: #00f2ff; color: #000; }
        .item-mail     { background: #ea4335; }
        .item-call:hover { color: #000; }
    `;
    document.head.appendChild(style);

    // 2. GET DYNAMIC PAGE INFO FOR WHATSAPP
    // This reads the page title to customize the user's message!
    let currentPageTitle = document.title.split('|')[0].trim();
    let waMessage = encodeURIComponent(`Hi Nectra Digital, I am interested in: ${currentPageTitle}. Can we connect?`);
    
    // UPDATE THESE WITH YOUR DETAILS
    const whatsappNumber = "+917678387759"; 
    const phoneNumber = "+917678387759";   
    const emailAddress = "contact@nectradigital.com";

    // 3. CREATE HTML WITH ARIA ACCESSIBILITY TAGS (Good for Google)
    const wrapper = document.createElement('div');
    wrapper.className = 'nectra-floating-wrap';
    wrapper.innerHTML = `
        <button class="nectra-main-btn" id="nectraFloatingToggle" aria-label="Open Support Menu" aria-expanded="false">
            <i class="fas fa-comment-dots" id="nectraToggleIcon"></i>
        </button>
        <div class="nectra-contact-menu" id="nectraFloatingMenu" role="menu">
            <a href="https://wa.me/${whatsappNumber}?text=${waMessage}" target="_blank" class="nectra-contact-item item-whatsapp track-click" data-network="whatsapp" data-tooltip="WhatsApp Us" aria-label="Contact on WhatsApp" role="menuitem">
                <i class="fab fa-whatsapp"></i>
            </a>
            <a href="tel:${phoneNumber}" class="nectra-contact-item item-call track-click" data-network="phone" data-tooltip="Direct Call" aria-label="Make a Phone Call" role="menuitem">
                <i class="fas fa-phone-alt"></i>
            </a>
            <a href="mailto:${emailAddress}" class="nectra-contact-item item-mail track-click" data-network="email" data-tooltip="Send Email" aria-label="Send an Email" role="menuitem">
                <i class="fas fa-envelope"></i>
            </a>
        </div>
    `;
    document.body.appendChild(wrapper);

    // 4. CLICK LOGIC & ANIMATIONS
    const toggleBtn = document.getElementById('nectraFloatingToggle');
    const menu = document.getElementById('nectraFloatingMenu');
    const icon = document.getElementById('nectraToggleIcon');

    toggleBtn.addEventListener('click', () => {
        const isActive = menu.classList.toggle('active');
        toggleBtn.setAttribute('aria-expanded', isActive);
        
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

    // 5. GOOGLE ANALYTICS (GA4/GTM) AUTO-TRACKING
    // Pushes event to GA4 when someone clicks a contact button
    const trackingLinks = document.querySelectorAll('.track-click');
    trackingLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            let network = this.getAttribute('data-network');
            
            // If GTAG is active, send the event
            if (typeof gtag === 'function') {
                gtag('event', 'generate_lead', {
                    'event_category': 'Contact Widget',
                    'event_label': network,
                    'value': 1
                });
            }
            
            // If GTM DataLayer is active, push the event
            if (typeof dataLayer !== 'undefined') {
                dataLayer.push({
                    'event': 'contact_click',
                    'network': network
                });
            }
        });
    });
});