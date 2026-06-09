

<script>
document.addEventListener("DOMContentLoaded", function() {
    if (!document.getElementById('nectra-canvas')) {
        var canvas = document.createElement('canvas');
        canvas.id = 'nectra-canvas';
        canvas.style.position = 'fixed';
        canvas.style.top = '0';
        canvas.style.left = '0';
        canvas.style.width = '100vw';
        canvas.style.height = '100vh';
        canvas.style.zIndex = '0';
        canvas.style.pointerEvents = 'none';
        document.body.appendChild(canvas);

        const ctx = canvas.getContext('2d');
        let particles = [];
        function resize() { canvas.width = window.innerWidth; canvas.height = window.innerHeight; }
        window.addEventListener('resize', resize);
        resize();

        class Particle {
            constructor() {
                this.x = Math.random() * canvas.width;
                this.y = Math.random() * canvas.height;
                this.vx = (Math.random() - 0.5) * 0.5;
                this.vy = (Math.random() - 0.5) * 0.5;
                this.size = Math.random() * 2;
            }
            update() {
                this.x += this.vx; this.y += this.vy;
                if(this.x < 0 || this.x > canvas.width) this.vx *= -1;
                if(this.y < 0 || this.y > canvas.height) this.vy *= -1;
            }
            draw() {
                ctx.fillStyle = 'rgba(0, 242, 255, 0.5)';
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fill();
            }
        }
        for(let i=0; i<100; i++) particles.push(new Particle());
        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            for(let i=0; i<particles.length; i++) {
                particles[i].update(); particles[i].draw();
                for(let j=i; j<particles.length; j++) {
                    const dx = particles[i].x - particles[j].x;
                    const dy = particles[i].y - particles[j].y;
                    const distance = Math.sqrt(dx*dx + dy*dy);
                    if(distance < 100) {
                        ctx.strokeStyle = `rgba(0, 242, 255, ${0.1 - distance/1000})`;
                        ctx.lineWidth = 0.5;
                        ctx.beginPath();
                        ctx.moveTo(particles[i].x, particles[i].y);
                        ctx.lineTo(particles[j].x, particles[j].y);
                        ctx.stroke();
                    }
                }
            }
            requestAnimationFrame(animate);
        }
        animate();
    }
});
</script>

<?php
if (!function_exists('get_services_data')) {
    require_once __DIR__ . '/seo-data.php';
}
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
                    Best SEO company in India. Search engine optimization, AI automation, digital marketing, and software development — engineered for ROI.
                </p>
                <div class="d-flex gap-3 mt-3">
                    <a href="https://www.linkedin.com/company/nectradigital" target="_blank" rel="noopener" class="text-white-50 hover-neon transition" aria-label="LinkedIn"><i class="fab fa-linkedin fa-lg"></i></a>
                    <a href="https://twitter.com/nectradigital" target="_blank" rel="noopener" class="text-white-50 hover-neon transition" aria-label="Twitter"><i class="fab fa-twitter fa-lg"></i></a>
                    <a href="https://www.instagram.com/nectradigital" target="_blank" rel="noopener" class="text-white-50 hover-neon transition" aria-label="Instagram"><i class="fab fa-instagram fa-lg"></i></a>
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
                </ul>
            </div>

            <div class="col-lg-2 col-6">
                <h6 class="text-neon text-uppercase mb-3 small fw-bold">Legal</h6>
                <ul class="list-unstyled text-white-50 small">
                    <li class="mb-2"><i class="fas fa-map-marker-alt me-2 text-secondary"></i> Lucknow, UP, India</li>
                    <li class="mb-2"><i class="fas fa-envelope me-2 text-secondary"></i> contact@nectradigital.com</li>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
<script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
<script>
    window.addEventListener('scroll', function() {
        const nav = document.querySelector('.navbar');
        if(nav) {
            if (window.scrollY > 50) {
                nav.style.padding = '10px 0';
                nav.style.background = 'rgba(5,5,5,0.95)';
            } else {
                nav.style.padding = '15px 0';
                nav.style.background = 'rgba(5,5,5,0.8)';
            }
        }
    });
    if(document.getElementById('particles-js')) {
        particlesJS('particles-js', {
          "particles": {
            "number": { "value": 60, "density": { "enable": true, "value_area": 800 } },
            "color": { "value": "#00E5FF" },
            "shape": { "type": "circle" },
            "opacity": { "value": 0.3 },
            "size": { "value": 3, "random": true },
            "line_linked": { "enable": true, "distance": 150, "color": "#00E5FF", "opacity": 0.2, "width": 1 },
            "move": { "enable": true, "speed": 1.5 }
          },
          "interactivity": { "detect_on": "canvas", "events": { "onhover": { "enable": true, "mode": "grab" } } }
        });
    }
</script>

<style>
.hover-neon:hover { color: var(--nectra-neon) !important; text-shadow: 0 0 8px rgba(0,229,255,0.3); }
.transition { transition: all 0.3s ease; }
.hover-effect:hover { transform: translateY(-3px); border-color: var(--nectra-neon) !important; transition: 0.3s; }
</style>
<script src="<?php echo SITE_URL; ?>/assets/js/floating-contact.js"></script>
</body>
</html>
