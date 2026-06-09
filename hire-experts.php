<?php 
// 1. INITIALIZATION & SEO
ob_start();
session_start();
require_once 'includes/db.php';

// Define SEO Meta Tags for Header
$page_title = "Hire Top Digital Experts | Nectra Digital";
$page_desc  = "Hire elite Web developers, SEO specialists, and AI automation experts. We don't just build websites; we engineer digital dominance for your brand.";
$page_keys  = "Hire Developers, Hire SEO Expert, Digital Marketing Agency, Nectra Digital Experts, AI Automation Services";

// 2. FORM PROCESSING LOGIC
$msg = "";
$msg_type = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_hire'])) {
    
    // Sanitize Inputs
    $full_name = $conn->real_escape_string(trim($_POST['full_name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $phone = $conn->real_escape_string(trim($_POST['phone']));
    $service = $conn->real_escape_string(trim($_POST['service_needed']));
    $budget = $conn->real_escape_string(trim($_POST['budget']));
    $details = $conn->real_escape_string(trim(htmlspecialchars($_POST['project_details'])));
    
    // Bot Trap (Honeypot)
    if (!empty($_POST['website_url'])) {
        die("Spam detected.");
    }

    // Insert into Database
    $sql = "INSERT INTO hire_requests (full_name, email, phone, service_needed, budget, project_details) 
            VALUES ('$full_name', '$email', '$phone', '$service', '$budget', '$details')";
            
    if ($conn->query($sql) === TRUE) {
        $msg = "System Updated: Your request has been securely transmitted. An expert will initialize contact shortly.";
        $msg_type = "success";
    } else {
        $msg = "System Error: Transmission failed. Please try the WhatsApp protocol below.";
        $msg_type = "danger";
    }
}

// 3. INJECT HEADER
include 'includes/header.php'; 
?>

<canvas id="nectra-canvas"></canvas>

<style>
    /* Global Page Fixes */
    html, body { margin: 0; padding: 0; min-height: 100vh; background-color: #050505 !important; color: #e0e0e0 !important; overflow-x: hidden; }
    #nectra-canvas { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 0; pointer-events: none; }
    main { position: relative; z-index: 2; font-family: 'Inter', sans-serif; }
    
    /* Neon Text & Borders */
    .text-neon { color: #00f2ff !important; text-shadow: 0 0 10px rgba(0,242,255,0.5); }
    .border-neon { border: 1px solid rgba(0, 242, 255, 0.3) !important; }
    .border-neon:focus, .border-neon:hover { border-color: #00f2ff !important; box-shadow: 0 0 10px rgba(0, 242, 255, 0.2); }
    
    /* Hero Section */
    .hero-section { padding: 140px 0 80px; text-align: center; position: relative; }
    .hero-title { font-family: 'Orbitron', sans-serif; font-weight: 700; color: #fff; text-transform: uppercase; letter-spacing: 2px; }
    
    /* Glassmorphism Cards */
    .glass-card {
        background: rgba(15, 15, 15, 0.7);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 12px;
        padding: 30px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
    }
    .glass-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0, 242, 255, 0.1); border-color: rgba(0, 242, 255, 0.3); }
    
    /* Icon styling */
    .feature-icon {
        width: 60px; height: 60px;
        background: rgba(0, 242, 255, 0.1);
        color: #00f2ff;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 24px; margin-bottom: 20px;
    }

    /* Process Steps */
    .process-step { position: relative; padding-left: 50px; margin-bottom: 30px; }
    .process-step::before {
        content: attr(data-step);
        position: absolute; left: 0; top: 0;
        width: 35px; height: 35px;
        background: #00f2ff; color: #000;
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        font-weight: bold; font-family: 'Orbitron', sans-serif;
    }
    .process-line { border-left: 2px dashed rgba(0,242,255,0.3); position: absolute; left: 16px; top: 35px; bottom: -30px; }
    .process-step:last-child .process-line { display: none; }

    /* Form Inputs */
    .form-control, .form-select { background-color: rgba(0,0,0,0.5) !important; border: 1px solid #333; color: #fff !important; padding: 12px 15px; }
    .form-control:focus, .form-select:focus { background-color: rgba(10,10,10,0.8) !important; border-color: #00f2ff; box-shadow: 0 0 10px rgba(0,242,255,0.2); }
    .form-label { color: #aaa; font-size: 0.9rem; letter-spacing: 1px; text-transform: uppercase; }
    
    /* WhatsApp Button Pulse */
    .btn-whatsapp { background: #25D366; color: #fff; font-weight: 600; padding: 12px 25px; border-radius: 8px; transition: 0.3s; display: inline-flex; align-items: center; gap: 10px; text-decoration: none; }
    .btn-whatsapp:hover { background: #1ebe57; color: #fff; transform: scale(1.05); box-shadow: 0 0 15px rgba(37, 211, 102, 0.5); }
</style>

<main>
    <section class="hero-section">
        <div class="container">
            <span class="badge border border-neon text-neon mb-3 px-3 py-2 text-uppercase tracking-widest">Elite Digital Taskforce</span>
            <h1 class="display-3 hero-title mb-4">Hire Top <span class="text-neon">Experts</span></h1>
            <p class="lead text-white-50 mx-auto" style="max-width: 700px;">
                Stop settling for average. Partner with Nectra Digital to build high-performance assets, dominate search engines, and automate your workflows with AI.
            </p>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="h1 text-white fw-bold">Why Deploy <span class="text-neon">Nectra Digital?</span></h2>
                <div class="mt-2 mx-auto" style="width: 50px; height: 3px; background: #00f2ff;"></div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="glass-card text-center">
                        <div class="feature-icon mx-auto"><i class="fas fa-rocket"></i></div>
                        <h4 class="text-white mb-3">ROI-Driven Strategy</h4>
                        <p class="text-white-50 small mb-0">We don't just write code or post content. Every strategy is engineered to generate measurable revenue and scale your business rapidly.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="glass-card text-center">
                        <div class="feature-icon mx-auto"><i class="fas fa-brain"></i></div>
                        <h4 class="text-white mb-3">AI & Automation Logic</h4>
                        <p class="text-white-50 small mb-0">Stay ahead of the curve. We integrate cutting-edge AI chatbots and automation systems to reduce your manual workload and increase efficiency.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="glass-card text-center">
                        <div class="feature-icon mx-auto"><i class="fas fa-code"></i></div>
                        <h4 class="text-white mb-3">Modern Tech Stack</h4>
                        <p class="text-white-50 small mb-0">From lightning-fast Next.js React applications to robust PHP/Python backends, we build digital infrastructure that doesn't break.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5" style="background: rgba(10,10,10,0.5);">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0 pe-lg-5">
                    <h2 class="h1 text-white fw-bold mb-4">How We Scale Your Business</h2>
                    <p class="text-white-50 mb-5">A systematic, data-backed approach to turning your brand into a digital authority.</p>
                    
                    <div class="process-wrapper">
                        <div class="process-step" data-step="1">
                            <div class="process-line"></div>
                            <h5 class="text-white">Deep Intelligence Gathering</h5>
                            <p class="text-white-50 small">We audit your current digital footprint, analyze competitors, and find hidden market gaps.</p>
                        </div>
                        <div class="process-step" data-step="2">
                            <div class="process-line"></div>
                            <h5 class="text-white">Strategic Architecture</h5>
                            <p class="text-white-50 small">Designing the exact roadmap—whether it's a high-converting landing page, an SEO content plan, or a custom app.</p>
                        </div>
                        <div class="process-step" data-step="3">
                            <div class="process-line"></div>
                            <h5 class="text-white">Deployment & Optimization</h5>
                            <p class="text-white-50 small">We execute the build and continuously optimize based on real-time analytics and user behavior.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="glass-card border-neon p-lg-5">
                        <h3 class="text-white mb-4">Ready to bypass the competition?</h3>
                        <p class="text-white-50 mb-4">Connect directly with our lead strategist via secure line.</p>
                        
                        <?php 
                            $waNumber = "917678387759"; // REPLACE WITH YOUR NUMBER
                            $waText = urlencode("System Ping: I am looking to hire an expert from Nectra Digital. Let's discuss my project.");
                        ?>
                        <a href="https://wa.me/<?php echo $waNumber; ?>?text=<?php echo $waText; ?>" target="_blank" class="btn-whatsapp w-100 justify-content-center mb-3">
                            <i class="fab fa-whatsapp fs-4"></i> INITIATE WHATSAPP CHAT
                        </a>
                        
                        <div class="d-flex align-items-center my-4">
                            <div class="flex-grow-1" style="height:1px; background: rgba(255,255,255,0.1);"></div>
                            <span class="px-3 text-white-50 small">OR TRANSMIT DATA BELOW</span>
                            <div class="flex-grow-1" style="height:1px; background: rgba(255,255,255,0.1);"></div>
                        </div>
                        
                        <a href="/contact" class="btn btn-outline-info w-100">
                            <i class="fas fa-envelope me-2"></i> Contact via form
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 mb-5" id="hire-form">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="h1 text-white fw-bold">Submit Project <span class="text-neon">Directives</span></h2>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="glass-card p-4 p-md-5 position-relative">
                        
                        <?php if($msg): ?>
                            <div class="alert alert-<?php echo $msg_type; ?> bg-dark border-<?php echo $msg_type; ?> text-<?php echo ($msg_type=='success')?'success':'danger'; ?> mb-4">
                                <?php echo $msg; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="hire-experts.php#hire-form">
                            <input type="text" name="website_url" style="display:none;" autocomplete="off">
                            
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="full_name" class="form-control" required placeholder="John Doe">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" name="email" class="form-control" required placeholder="john@company.com">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone / WhatsApp</label>
                                    <input type="text" name="phone" class="form-control" required placeholder="+91 98765 43210">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Service Required</label>
                                    <select name="service_needed" class="form-select" required>
                                        <option value="" disabled selected>Select Module...</option>
                                        <option value="Web Development (React/PHP)">Web Development</option>
                                        <option value="Advanced SEO & Ranking">Advanced SEO & Ranking</option>
                                        <option value="AI Automation & Chatbots">AI Automation & Chatbots</option>
                                        <option value="Full Digital Marketing">Full Digital Marketing Strategy</option>
                                        <option value="Custom Software/App">Custom Software / App</option>
                                        <option value="Other">Other Query</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Estimated Budget</label>
                                    <select name="budget" class="form-select" required>
                                        <option value="Under $500 (Basic)">Under ₹50,000 / $500 (Basic)</option>
                                        <option value="$500 - $2000 (Standard)">₹50k - ₹1.5L / $500 - $2000 (Standard)</option>
                                        <option value="$2000+ (Enterprise)">₹1.5L+ / $2000+ (Enterprise)</option>
                                        <option value="Not Sure Yet">Not Sure Yet / Let's Discuss</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Project Directives / Details</label>
                                    <textarea name="project_details" class="form-control" rows="4" required placeholder="Tell us about your business goals and what you want to achieve..."></textarea>
                                </div>
                                <div class="col-12 mt-5">
                                    <button type="submit" name="submit_hire" class="btn btn-info w-100 py-3 fw-bold tracking-widest text-dark" style="background: #00f2ff; border:none; text-transform:uppercase;">
                                        INITIATE PROJECT <i class="fas fa-paper-plane ms-2"></i>
                                    </button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<script>
const canvas = document.getElementById('nectra-canvas');
const ctx = canvas.getContext('2d');
let particles = [];

function resize() {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
}
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
        this.x += this.vx;
        this.y += this.vy;
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
        particles[i].update();
        particles[i].draw();
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
</script>

<?php include 'includes/footer.php'; ?>