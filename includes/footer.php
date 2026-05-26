

<footer class="pt-5 pb-3 border-top border-secondary mt-auto position-relative" style="background: rgba(5,5,5,0.9);">
    <div class="container position-relative z-1">
        <div class="row g-4">
            
            <div class="col-lg-4">
                <div class="mb-4">
                    <img src="assets/images/logo.png" alt="Nectra Digital" style="height: 60px; width: auto; max-width: 200px; display: block;">
                </div>
                <p class="text-white-50 small" style="max-width: 300px; line-height: 1.6;">
                    Engineered in India for the World. We build high-performance digital assets that bridge the gap between complex code and business ROI.
                </p>
                <div class="d-flex gap-3 mt-4">
                    <a href="#" class="text-white-50 hover-neon transition"><i class="fab fa-linkedin fa-lg"></i></a>
                    <a href="#" class="text-white-50 hover-neon transition"><i class="fab fa-twitter fa-lg"></i></a>
                    <a href="#" class="text-white-50 hover-neon transition"><i class="fab fa-instagram fa-lg"></i></a>
                    <a href="#" class="text-white-50 hover-neon transition"><i class="fab fa-github fa-lg"></i></a>
                </div>
            </div>

            <div class="col-lg-2 col-6">
                <h6 class="text-neon text-uppercase mb-3 small fw-bold" style="letter-spacing: 1px;">Capabilities</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="services#web" class="text-white-50 text-decoration-none small hover-neon">Web Architecture</a></li>
                    <li class="mb-2"><a href="services#app" class="text-white-50 text-decoration-none small hover-neon">App Ecosystems</a></li>
                    <li class="mb-2"><a href="services#ai" class="text-white-50 text-decoration-none small hover-neon">AI Automation</a></li>
                    <li class="mb-2"><a href="services#growth" class="text-white-50 text-decoration-none small hover-neon">Global Growth</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-6">
                <h6 class="text-neon text-uppercase mb-3 small fw-bold" style="letter-spacing: 1px;">Company</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="portfolio" class="text-white-50 text-decoration-none small hover-neon">Assets</a></li>
                    <li class="mb-2"><a href="insights" class="text-white-50 text-decoration-none small hover-neon">Intel</a></li>
                    <li class="mb-2"><a href="careers" class="text-white-50 text-decoration-none small hover-neon">Recruitment</a></li>
                    <li class="mb-2"><a href="contact" class="text-white-50 text-decoration-none small hover-neon">Initialize</a></li>
                </ul>
            </div>

            <div class="col-lg-4">
                <h6 class="text-neon text-uppercase mb-3 small fw-bold" style="letter-spacing: 1px;">Global Ops</h6>
                <ul class="list-unstyled text-white-50 small">
                    <li class="mb-2"><i class="fas fa-map-marker-alt me-2 text-secondary"></i> Lucknow, UP, India (HQ)</li>
                    <li class="mb-2"><i class="fas fa-envelope me-2 text-secondary"></i> contact@nectradigital.com</li>
                    <li class="mb-2"><i class="fas fa-clock me-2 text-secondary"></i> Mon - Fri: 09:00 - 18:00 IST</li>
                </ul>
            </div>
        </div>

        <hr class="border-secondary my-4" style="opacity: 0.2;">

        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <p class="text-white-50 x-small mb-0">
                    &copy; <?php echo date("Y"); ?> Nectra Digital. All Systems Operational.
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end mt-3 mt-md-0">
                <a href="privacy" class="text-white-50 text-decoration-none x-small me-3 hover-neon">Privacy Protocol</a>
                <a href="terms" class="text-white-50 text-decoration-none x-small hover-neon">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="assets/js/main.js"></script>

<script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
<script>
    // Navbar Scroll Effect
    window.addEventListener('scroll', function() {
        const nav = document.querySelector('.navbar');
        if(nav) {
            if (window.scrollY > 50) {
                nav.style.padding = '10px 0';
                nav.style.background = 'rgba(5,5,5,0.9)';
            } else {
                nav.style.padding = '15px 0';
                nav.style.background = 'rgba(5,5,5,0.75)';
            }
        }
    });

    // Particles JS with full mouse interactivity
    if(document.getElementById('particles-js')) {
        particlesJS('particles-js', {
          "particles": {
            "number": {
              "value": 80,
              "density": { "enable": true, "value_area": 900 }
            },
            "color": { "value": ["#00E5FF", "#00BCD4", "#4DD0E1"] },
            "shape": {
              "type": ["circle", "triangle"],
              "stroke": { "width": 0, "color": "#000000" }
            },
            "opacity": {
              "value": 0.4,
              "random": true,
              "anim": { "enable": true, "speed": 1, "opacity_min": 0.1, "sync": false }
            },
            "size": {
              "value": 3,
              "random": true,
              "anim": { "enable": true, "speed": 2, "size_min": 0.5, "sync": false }
            },
            "line_linked": {
              "enable": true,
              "distance": 150,
              "color": "#00E5FF",
              "opacity": 0.25,
              "width": 1
            },
            "move": {
              "enable": true,
              "speed": 1.8,
              "direction": "none",
              "random": true,
              "straight": false,
              "out_mode": "out",
              "bounce": false,
              "attract": { "enable": true, "rotateX": 600, "rotateY": 1200 }
            }
          },
          "interactivity": {
            "detect_on": "window",
            "events": {
              "onhover": {
                "enable": true,
                "mode": ["grab", "bubble"]
              },
              "onclick": {
                "enable": true,
                "mode": "push"
              },
              "resize": true
            },
            "modes": {
              "grab": {
                "distance": 200,
                "line_linked": { "opacity": 0.6 }
              },
              "bubble": {
                "distance": 250,
                "size": 6,
                "duration": 2,
                "opacity": 0.8,
                "speed": 3
              },
              "repulse": {
                "distance": 150,
                "duration": 0.4
              },
              "push": {
                "particles_nb": 4
              },
              "remove": {
                "particles_nb": 2
              }
            }
          },
          "retina_detect": true
        });
    }
</script>

<script src="<?php echo SITE_URL; ?>/assets/js/floating-contact.js"></script>
<script>
(function(){
    if(!('serviceWorker' in navigator) || !('PushManager' in window)) return;
    
    navigator.serviceWorker.register('<?php echo SITE_URL; ?>/sw.js').then(function(reg){
        if(Notification.permission === 'granted') {
            subscribeUser(reg);
        } else if(Notification.permission !== 'denied') {
            setTimeout(function(){
                Notification.requestPermission().then(function(perm){
                    if(perm === 'granted') subscribeUser(reg);
                });
            }, 8000);
        }
    });

    function subscribeUser(reg){
        reg.pushManager.getSubscription().then(function(sub){
            if(sub) return;
            reg.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array('BEl62iUYgUivxIkv69yViEuiBIa-Ib9-SkvMeAtA3LNgDmSAumU7F2nGPDFY4rN8U4o0xKGwHN7a32yCzZlQmg0')
            }).then(function(subscription){
                fetch('<?php echo SITE_URL; ?>/push-subscribe.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(subscription.toJSON())
                });
            });
        });
    }

    function urlBase64ToUint8Array(base64String){
        var padding = '='.repeat((4 - base64String.length % 4) % 4);
        var base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        var rawData = window.atob(base64);
        var outputArray = new Uint8Array(rawData.length);
        for(var i = 0; i < rawData.length; ++i) outputArray[i] = rawData.charCodeAt(i);
        return outputArray;
    }
})();
</script>
</body>
</html>