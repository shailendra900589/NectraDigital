<?php 
$page_title = "Contact Nectra Digital | Lucknow HQ";
$page_desc = "Contact Nectra Digital — SEO company HQ in Lucknow, India. Phone, address & project inquiry form. Search engine optimization & digital marketing.";
$page_keys = "Contact Nectra Digital, SEO company contact India, digital marketing consultation";
include 'includes/header.php';
require_once 'includes/site-contact.php';
?>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ContactPage",
  "name": "Contact Nectra Digital",
  "description": "Global HQ and Project Initialization Center",
  "mainEntity": {
    "@type": "ProfessionalService",
    "name": "Nectra Digital",
    "telephone": "<?php echo NECTRA_PHONE_E164; ?>",
    "email": "<?php echo nectra_schema_email(); ?>",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "Lucknow",
      "addressLocality": "Lucknow",
      "addressRegion": "UP",
      "postalCode": "226001",
      "addressCountry": "IN"
    },
    "openingHoursSpecification": {
      "@type": "OpeningHoursSpecification",
      "dayOfWeek": [
        "Monday", "Tuesday", "Wednesday", "Thursday", "Friday"
      ],
      "opens": "09:00",
      "closes": "18:00"
    }
  }
}
</script>

<main>
    <header class="d-flex align-items-center justify-content-center text-center" style="min-height: 40vh; background: linear-gradient(to bottom, #050505 0%, #0a1518 100%);">
        <div class="container">
            <h1 class="h6 text-neon text-uppercase mb-3" style="letter-spacing: 3px;">Communication Channels</h1>
            <p class="display-4 fw-bold text-white mb-2">INITIALIZE <span class="text-neon">PROTOCOL</span></p>
            <p class="lead text-white-50 mx-auto" style="max-width: 600px;">Ready to engineer your digital dominance? Secure connection established.</p>
        </div>
    </header>

    <section class="py-5 border-top border-secondary" style="border-color: #222 !important;">
        <div class="container">
            <div class="row g-5">
                
                <div class="col-lg-5">
                    <div class="p-4 bg-glass border border-secondary rounded h-100">
                        <h2 class="text-white h4 mb-4">GLOBAL <span class="text-neon">HQ</span></h2>
                        
                        <address class="mb-4">
                            <div class="d-flex align-items-start mb-4">
                                <div class="me-3 mt-1" aria-hidden="true"><i class="fas fa-map-marker-alt fa-lg text-neon"></i></div>
                                <div>
                                    <h3 class="text-white h6 mb-1">Base of Operations</h3>
                                    <p class="text-white-50 small mb-0">Lucknow, Uttar Pradesh, India.<br>Operating Globally.</p>
                                </div>
                            </div>

                            <div class="d-flex align-items-start mb-4">
                                <div class="me-3 mt-1" aria-hidden="true"><i class="fas fa-phone fa-lg text-neon"></i></div>
                                <div>
                                    <h3 class="text-white h6 mb-1">Phone</h3>
                                    <p class="text-white-50 small mb-0"><a href="tel:<?php echo NECTRA_PHONE_E164; ?>" class="text-neon text-decoration-none"><?php echo NECTRA_PHONE_DISPLAY; ?></a></p>
                                </div>
                            </div>

                            <div class="d-flex align-items-start mb-4">
                                <div class="me-3 mt-1" aria-hidden="true"><i class="fas fa-envelope fa-lg text-neon"></i></div>
                                <div>
                                    <h3 class="text-white h6 mb-1">Email</h3>
                                    <p class="text-white-50 small mb-0"><?php echo nectra_email_html_link('text-neon text-decoration-none'); ?></p>
                                </div>
                            </div>
                        </address>

                        <div class="border-top border-secondary pt-4 mt-4">
                            <h3 class="text-white-50 text-uppercase h6 small mb-3">Live Operations</h3>
                            <div class="row g-2">
                                <div class="col-4">
                                    <div class="p-2 bg-dark rounded text-center border border-secondary">
                                        <span class="d-block text-white-50" style="font-size: 10px;">INDIA (IST)</span>
                                        <span class="text-white fw-bold small" id="time-ist">Loading...</span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2 bg-dark rounded text-center border border-secondary">
                                        <span class="d-block text-white-50" style="font-size: 10px;">NEW YORK</span>
                                        <span class="text-neon fw-bold small" id="time-est">Loading...</span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2 bg-dark rounded text-center border border-secondary">
                                        <span class="d-block text-white-50" style="font-size: 10px;">LONDON</span>
                                        <span class="text-white fw-bold small" id="time-gmt">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <nav class="mt-4 pt-4 border-top border-secondary" aria-label="Social Media Links">
                            <h3 class="text-white-50 text-uppercase h6 small mb-3">Secure Networks</h3>
                            <div class="d-flex gap-3">
                                <a href="#" class="btn btn-outline-light btn-sm" aria-label="Connect on LinkedIn"><i class="fab fa-linkedin" aria-hidden="true"></i> LinkedIn</a>
                                <a href="#" class="btn btn-outline-light btn-sm" aria-label="Follow on Instagram"><i class="fab fa-instagram" aria-hidden="true"></i> Instagram</a>
                                <a href="#" class="btn btn-outline-light btn-sm" aria-label="Follow on Twitter"><i class="fab fa-twitter" aria-hidden="true"></i> X</a>
                            </div>
                        </nav>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="p-4 border border-secondary rounded h-100" style="background: rgba(0,229,255,0.02);">
                        <h2 class="text-white h4 mb-2">DEPLOY <span class="text-neon">REQUEST</span></h2>
                        <p class="text-white-50 small mb-4">Fill out the intel below. The Architect will review your project.</p>
                        
                        <form id="contactPageForm" novalidate>
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            
                            <div style="display:none; opacity:0; visibility:hidden;">
                                <label for="website_url_hp">Keep this blank</label>
                                <input type="text" name="website_url" id="website_url_hp" tabindex="-1" autocomplete="off">
                            </div>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label text-white-50 small">Full Name</label>
                                    <input type="text" name="name" id="name" class="form-control" required autocomplete="name">
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label text-white-50 small">Business Email</label>
                                    <input type="email" name="email" id="email" class="form-control" required autocomplete="email">
                                </div>

                                <div class="col-12">
                                    <label for="service" class="form-label text-white-50 small">Project Category</label>
                                    <select name="service" id="service" class="form-select text-white" required>
                                        <option value="" disabled selected>Select Mission Type...</option>
                                        <option value="Web Development">Web Architecture (Next.js/WP)</option>
                                        <option value="App Development">Mobile App (React Native)</option>
                                        <option value="AI Automation">AI & Automation</option>
                                        <option value="Growth Marketing">Growth & SEO Marketing</option>
                                        <option value="Full Suite">Full Digital Transformation</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="budget" class="form-label text-white-50 small">Estimated Budget (USD)</label>
                                    <select name="budget" id="budget" class="form-select" required>
                                        <option value="$1k - $5k">$1,000 - $5,000</option>
                                        <option value="$5k - $10k">$5,000 - $10,000</option>
                                        <option value="$10k+">$10,000+ (Enterprise)</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="timeline" class="form-label text-white-50 small">Desired Timeline</label>
                                    <select name="timeline" id="timeline" class="form-select" required>
                                        <option value="ASAP">Urgent (ASAP)</option>
                                        <option value="1-3 Months">1-3 Months</option>
                                        <option value="Flexible">Flexible</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label for="message" class="form-label text-white-50 small">Project Briefing</label>
                                    <textarea name="message" id="message" class="form-control" rows="4" placeholder="Describe your vision..." required></textarea>
                                </div>

                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-nectra w-100 py-3">
                                        TRANSMIT DATA <i class="fas fa-paper-plane ms-2" aria-hidden="true"></i>
                                    </button>
                                    <div id="pageFormResponse" class="mt-3 text-center fw-bold" aria-live="polite"></div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="border-top border-secondary" aria-label="Office Location Map">
        <div style="width: 100%; height: 400px; filter: invert(90%) hue-rotate(180deg) contrast(90%); overflow: hidden; background: #222;">
            <iframe 
                title="Nectra Digital HQ Map"
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d227822.5503903933!2d80.80242469608678!3d26.84862299412656!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x399bfd991f32b16b%3A0x93ccba8909978be7!2sLucknow%2C%20Uttar%20Pradesh!5e0!3m2!1sen!2sin!4v1700000000000!5m2!1sen!2sin" 
                width="100%" 
                height="100%" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
        <div style="margin-top: -400px; height: 400px; width: 100%; background: rgba(0, 229, 255, 0.05); pointer-events: none; position: relative;"></div>
    </section>
</main>

<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // 1. CLOCK FUNCTION (Efficient Update)
    function updateTime() {
        const now = new Date();
        const options = { hour: '2-digit', minute:'2-digit', hour12: true };
        
        // India
        const timeIst = document.getElementById('time-ist');
        if(timeIst) timeIst.textContent = now.toLocaleTimeString("en-US", { ...options, timeZone: "Asia/Kolkata" });
        
        // New York
        const timeEst = document.getElementById('time-est');
        if(timeEst) timeEst.textContent = now.toLocaleTimeString("en-US", { ...options, timeZone: "America/New_York" });
        
        // London
        const timeGmt = document.getElementById('time-gmt');
        if(timeGmt) timeGmt.textContent = now.toLocaleTimeString("en-US", { ...options, timeZone: "Europe/London" });
    }
    
    updateTime();
    setInterval(updateTime, 60000); 

    // 2. FORM HANDLING
    const form = document.getElementById('contactPageForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Basic HTML5 Validation Check
            if (!form.checkValidity()) {
                e.stopPropagation();
                form.classList.add('was-validated');
                return;
            }

            const btn = form.querySelector('button');
            const resp = document.getElementById('pageFormResponse');
            const originalText = btn.innerHTML;
            
            // Visual Feedback
            btn.innerHTML = 'ENCRYPTING & SENDING...';
            btn.disabled = true;

            // Prepare Data
            const formData = new FormData(this);

            // AJAX Request
            fetch('/process', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    // Redirect to Thank You Page on Success
                    window.location.href = "thank-you.php";
                } else {
                    resp.innerHTML = `<span class="text-danger"><i class="fas fa-exclamation-triangle"></i> ${data.message}</span>`;
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            })
            .catch(error => {
                resp.innerHTML = '<span class="text-danger">Transmission Error. Try again.</span>';
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>