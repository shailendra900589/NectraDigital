

<?php
if (!function_exists('get_services_data')) {
    require_once __DIR__ . '/seo-data.php';
}
require_once __DIR__ . '/site-contact.php';
$footer_services = array_slice(get_services_data(), 0, 8, true);
$footer_cities = array_slice(get_cities_data(), 0, 8, true);
?>

<footer class="pt-5 pb-3 border-top border-secondary bg-black mt-auto position-relative" style="background: #050505;">
    <div class="container position-relative z-1">
        <div class="row g-4">
            
            <div class="col-lg-3">
                <div class="mb-3">
                    <a href="/" class="text-decoration-none">
                        <span class="text-white fw-bold fs-5">NECTRA</span><span class="text-neon fw-bold fs-5">DIGITAL</span>
                    </a>
                </div>
                <p class="text-white-50 small" style="line-height: 1.6;">
                    Best SEO company in India — search engine optimization, AI automation, digital marketing, and software development engineered for ROI.
                </p>
                <?php render_nap_block('footer'); ?>
                <div class="d-flex gap-3 mt-3">
                    <a href="<?php echo NECTRA_FACEBOOK_URL; ?>" target="_blank" rel="noopener noreferrer" class="text-white-50 hover-neon transition" aria-label="Facebook"><i class="fab fa-facebook fa-lg"></i></a>
                    <a href="https://www.linkedin.com/company/nectradigital" target="_blank" rel="noopener noreferrer" class="text-white-50 hover-neon transition" aria-label="LinkedIn"><i class="fab fa-linkedin fa-lg"></i></a>
                    <a href="https://twitter.com/nectradigital" target="_blank" rel="noopener noreferrer" class="text-white-50 hover-neon transition" aria-label="Twitter"><i class="fab fa-twitter fa-lg"></i></a>
                    <a href="https://www.instagram.com/nectradigital" target="_blank" rel="noopener noreferrer" class="text-white-50 hover-neon transition" aria-label="Instagram"><i class="fab fa-instagram fa-lg"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <h6 class="text-neon text-uppercase mb-3 small fw-bold">Services</h6>
                <ul class="list-unstyled">
                    <?php foreach ($footer_services as $slug => $svc): ?>
                    <li class="mb-2"><a href="/<?php echo $slug; ?>" class="text-white-50 text-decoration-none small hover-neon"><?php echo htmlspecialchars($svc['h1']); ?></a></li>
                    <?php endforeach; ?>
                    <li class="mb-2"><a href="/services" class="text-neon text-decoration-none small">All Services →</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-6">
                <h6 class="text-neon text-uppercase mb-3 small fw-bold">Company</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="/about" class="text-white-50 text-decoration-none small hover-neon">About & Founder</a></li>
                    <li class="mb-2"><a href="/portfolio" class="text-white-50 text-decoration-none small hover-neon">Portfolio</a></li>
                    <li class="mb-2"><a href="/insights" class="text-white-50 text-decoration-none small hover-neon">Intel / Blog</a></li>
                    <li class="mb-2"><a href="/content-strategy" class="text-white-50 text-decoration-none small hover-neon">Content Strategy</a></li>
                    <li class="mb-2"><a href="/aeo" class="text-white-50 text-decoration-none small hover-neon">AEO Answers</a></li>
                    <li class="mb-2"><a href="/careers" class="text-white-50 text-decoration-none small hover-neon">Careers</a></li>
                    <li class="mb-2"><a href="/contact" class="text-white-50 text-decoration-none small hover-neon">Contact</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-6">
                <h6 class="text-neon text-uppercase mb-3 small fw-bold">Locations</h6>
                <ul class="list-unstyled">
                    <?php foreach ($footer_cities as $slug => $city): ?>
                    <li class="mb-2"><a href="/digital-agency-<?php echo $slug; ?>" class="text-white-50 text-decoration-none small hover-neon"><?php echo htmlspecialchars($city['name']); ?></a></li>
                    <?php endforeach; ?>
                    <li class="mb-2"><a href="/services#locations" class="text-neon text-decoration-none small">All Cities →</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-6">
                <h6 class="text-neon text-uppercase mb-3 small fw-bold">Legal</h6>
                <ul class="list-unstyled text-white-50 small">
                    <li class="mb-2"><a href="/privacy" class="text-white-50 text-decoration-none hover-neon">Privacy Policy</a></li>
                    <li class="mb-2"><a href="/terms" class="text-white-50 text-decoration-none hover-neon">Terms</a></li>
                    <li class="mb-2"><a href="/disclaimer" class="text-white-50 text-decoration-none hover-neon">Disclaimer</a></li>
                    <li class="mb-2"><a href="/editorial-guidelines" class="text-white-50 text-decoration-none hover-neon">Editorial Guidelines</a></li>
                </ul>
            </div>
        </div>

        <hr class="border-secondary my-4" style="opacity: 0.2;">

        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <p class="text-white-50 x-small mb-0">&copy; <?php echo date("Y"); ?> Nectra Digital. SEO Company India. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-center text-md-end mt-3 mt-md-0">
                <a href="/contact?service=SEO+Audit" class="btn btn-nectra btn-sm">Get Free SEO Audit</a>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
<script src="<?php echo SITE_URL; ?>/assets/js/main.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js" defer></script>
<script defer>
document.addEventListener('DOMContentLoaded', function() {
    const nav = document.querySelector('.navbar');
    if (nav) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                nav.style.padding = '10px 0';
                nav.style.background = 'rgba(5,5,5,0.95)';
            } else {
                nav.style.padding = '15px 0';
                nav.style.background = 'rgba(5,5,5,0.8)';
            }
        }, {passive: true});
    }
    if (document.getElementById('particles-js') && typeof particlesJS === 'function') {
        particlesJS('particles-js', {
          "particles": {
            "number": { "value": 35, "density": { "enable": true, "value_area": 900 } },
            "color": { "value": "#00E5FF" },
            "shape": { "type": "circle" },
            "opacity": { "value": 0.25 },
            "size": { "value": 2, "random": true },
            "line_linked": { "enable": true, "distance": 140, "color": "#00E5FF", "opacity": 0.15, "width": 1 },
            "move": { "enable": true, "speed": 1 }
          },
          "interactivity": { "detect_on": "canvas", "events": { "onhover": { "enable": true, "mode": "grab" } } }
        });
    }
});
</script>

<style>
.hover-neon:hover { color: var(--nectra-neon) !important; text-shadow: 0 0 8px rgba(0,229,255,0.3); }
.transition { transition: all 0.3s ease; }
.hover-effect:hover { transform: translateY(-3px); border-color: var(--nectra-neon) !important; transition: 0.3s; }
</style>
<?php
$nectraChatbotEnabled = false;
try {
    if (is_file(__DIR__ . '/db.local.php') && file_exists(__DIR__ . '/growth/bootstrap.php')) {
        require_once __DIR__ . '/growth/bootstrap.php';
        $nectraChatbotEnabled = ge_setting('chatbot_enabled', '0') === '1';
    }
} catch (\Throwable $e) {
    $nectraChatbotEnabled = false;
}
?>
<link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/floating-contact.css?v=4">
<?php if ($nectraChatbotEnabled): ?>
<script>window.NECTRA_CHATBOT = { apiUrl: '<?php echo SITE_URL; ?>/api/chatbot' };</script>
<link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/growth-chatbot.css?v=4">
<script src="<?php echo SITE_URL; ?>/assets/js/growth-chatbot.js?v=4" defer></script>
<?php endif; ?>
<script src="<?php echo SITE_URL; ?>/assets/js/floating-contact.js?v=4" defer></script>
<script defer>
(function(c,l,a,r,i,t,y){
    c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
    t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
    y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
})(window, document, "clarity", "script", "vjpbvxww01");
</script>
</body>
</html>
