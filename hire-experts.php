<?php 
require_once 'includes/db.php';

$page_title = "Hire Expert Developers, SEO Specialists & AI Engineers | Lucknow";
$page_desc = "Hire dedicated web developers, SEO experts, digital marketing specialists, AI engineers, and mobile app developers from Nectra Digital Lucknow. Flexible hiring — hourly, monthly, or project-based.";
$page_keys = "Hire Web Developer Lucknow, Hire SEO Expert India, Hire React Developer, Hire Digital Marketing Expert, Dedicated Development Team, Hire AI Engineer, Hire Mobile App Developer, Freelance Developer Lucknow, Hire PHP Developer, Hire Python Developer";

$msg = "";
$msg_type = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_hire'])) {
    if (!empty($_POST['website_url'])) {
        die("Spam detected.");
    }

    $full_name = $conn->real_escape_string(trim($_POST['full_name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $phone = $conn->real_escape_string(trim($_POST['phone']));
    $service = $conn->real_escape_string(trim($_POST['service_needed']));
    $budget = $conn->real_escape_string(trim($_POST['budget']));
    $details = $conn->real_escape_string(trim(htmlspecialchars($_POST['project_details'])));

    $sql = "INSERT INTO hire_requests (full_name, email, phone, service_needed, budget, project_details) 
            VALUES ('$full_name', '$email', '$phone', '$service', '$budget', '$details')";
            
    if ($conn->query($sql) === TRUE) {
        $msg = "Request submitted successfully! Our team will contact you within 2 hours.";
        $msg_type = "success";
    } else {
        $msg = "Something went wrong. Please try WhatsApp or email us directly.";
        $msg_type = "danger";
    }
}

include 'includes/header.php'; 
?>

<!-- HERO -->
<header class="hire-hero d-flex align-items-center justify-content-center text-center position-relative" style="min-height: 55vh; padding-top: 100px;">
    <div class="container position-relative z-1">
        <div class="d-inline-block border border-neon rounded-pill px-3 py-1 mb-4 bg-dark">
            <small class="text-neon text-uppercase" style="letter-spacing: 2px;"><i class="fas fa-user-tie me-2"></i> Dedicated Experts On-Demand</small>
        </div>
        <h1 class="display-4 fw-bold text-white mb-4">Hire Top Digital <span class="text-neon">Experts</span></h1>
        <p class="lead text-white-50 mx-auto mb-5" style="max-width: 750px;">
            <strong class="text-white">Hire dedicated developers, SEO specialists, and AI engineers</strong> from Nectra Digital. Scale your team instantly without the overhead of full-time hiring. Flexible engagement — hourly, monthly, or project-based.
        </p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="#hire-form" class="btn btn-nectra">HIRE NOW <i class="fas fa-arrow-right ms-2"></i></a>
            <a href="#experts" class="btn btn-outline-light">VIEW EXPERTS</a>
        </div>
    </div>
</header>

<!-- EXPERTS AVAILABLE -->
<section id="experts" class="py-5 border-top border-secondary" style="background: linear-gradient(180deg, rgba(10,13,15,0.85) 0%, rgba(5,5,5,0.8) 100%);">
    <div class="container py-4">
        <div class="text-center mb-5">
            <span class="badge border border-neon text-neon mb-3 px-3 py-2 text-uppercase">Available Experts</span>
            <h2 class="display-6 text-white fw-bold">Experts You Can <span class="text-neon">Hire</span></h2>
            <p class="text-white-50 mt-3">Pre-vetted professionals ready to join your project within 24-48 hours.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="hire-expert-card p-4 border border-secondary rounded h-100">
                    <div class="hire-expert-icon mb-3">
                        <i class="fas fa-code"></i>
                    </div>
                    <h3 class="h5 text-white fw-bold">Full-Stack Web Developer</h3>
                    <p class="text-white-50 small mb-3">React.js, Next.js, Node.js, PHP/Laravel, Python/Django. Build scalable web apps, SaaS platforms, and custom dashboards.</p>
                    <div class="d-flex flex-wrap gap-1 mb-3">
                        <span class="badge bg-dark border border-secondary small">React</span>
                        <span class="badge bg-dark border border-secondary small">Next.js</span>
                        <span class="badge bg-dark border border-secondary small">Node.js</span>
                        <span class="badge bg-dark border border-secondary small">PHP</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top border-secondary">
                        <small class="text-neon fw-bold">From ₹800/hr</small>
                        <a href="#hire-form" class="btn btn-sm btn-outline-light">Hire</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="hire-expert-card p-4 border border-secondary rounded h-100">
                    <div class="hire-expert-icon mb-3">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3 class="h5 text-white fw-bold">SEO Specialist</h3>
                    <p class="text-white-50 small mb-3">Technical SEO, on-page/off-page optimization, local SEO, keyword research, link building, and Google Analytics expert.</p>
                    <div class="d-flex flex-wrap gap-1 mb-3">
                        <span class="badge bg-dark border border-secondary small">Technical SEO</span>
                        <span class="badge bg-dark border border-secondary small">Link Building</span>
                        <span class="badge bg-dark border border-secondary small">Local SEO</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top border-secondary">
                        <small class="text-neon fw-bold">From ₹600/hr</small>
                        <a href="#hire-form" class="btn btn-sm btn-outline-light">Hire</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="hire-expert-card p-4 border border-secondary rounded h-100">
                    <div class="hire-expert-icon mb-3">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <h3 class="h5 text-white fw-bold">Digital Marketing Expert</h3>
                    <p class="text-white-50 small mb-3">Google Ads, Meta Ads, LinkedIn campaigns, content marketing, email marketing, and conversion rate optimization.</p>
                    <div class="d-flex flex-wrap gap-1 mb-3">
                        <span class="badge bg-dark border border-secondary small">Google Ads</span>
                        <span class="badge bg-dark border border-secondary small">Meta Ads</span>
                        <span class="badge bg-dark border border-secondary small">CRO</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top border-secondary">
                        <small class="text-neon fw-bold">From ₹700/hr</small>
                        <a href="#hire-form" class="btn btn-sm btn-outline-light">Hire</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="hire-expert-card p-4 border border-secondary rounded h-100">
                    <div class="hire-expert-icon mb-3">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3 class="h5 text-white fw-bold">Mobile App Developer</h3>
                    <p class="text-white-50 small mb-3">React Native & Flutter cross-platform apps. iOS & Android deployment, push notifications, Firebase integration, and app store optimization.</p>
                    <div class="d-flex flex-wrap gap-1 mb-3">
                        <span class="badge bg-dark border border-secondary small">React Native</span>
                        <span class="badge bg-dark border border-secondary small">Flutter</span>
                        <span class="badge bg-dark border border-secondary small">Firebase</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top border-secondary">
                        <small class="text-neon fw-bold">From ₹900/hr</small>
                        <a href="#hire-form" class="btn btn-sm btn-outline-light">Hire</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="hire-expert-card p-4 border border-secondary rounded h-100">
                    <div class="hire-expert-icon mb-3">
                        <i class="fas fa-robot"></i>
                    </div>
                    <h3 class="h5 text-white fw-bold">AI & Automation Engineer</h3>
                    <p class="text-white-50 small mb-3">Custom AI chatbots (GPT), workflow automation, LangChain agents, data pipelines, and machine learning model deployment.</p>
                    <div class="d-flex flex-wrap gap-1 mb-3">
                        <span class="badge bg-dark border border-secondary small">OpenAI</span>
                        <span class="badge bg-dark border border-secondary small">Python</span>
                        <span class="badge bg-dark border border-secondary small">LangChain</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top border-secondary">
                        <small class="text-neon fw-bold">From ₹1000/hr</small>
                        <a href="#hire-form" class="btn btn-sm btn-outline-light">Hire</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="hire-expert-card p-4 border border-secondary rounded h-100">
                    <div class="hire-expert-icon mb-3">
                        <i class="fas fa-paint-brush"></i>
                    </div>
                    <h3 class="h5 text-white fw-bold">UI/UX Designer</h3>
                    <p class="text-white-50 small mb-3">Figma wireframes, user research, interactive prototypes, design systems, mobile-first responsive design, and usability testing.</p>
                    <div class="d-flex flex-wrap gap-1 mb-3">
                        <span class="badge bg-dark border border-secondary small">Figma</span>
                        <span class="badge bg-dark border border-secondary small">Prototyping</span>
                        <span class="badge bg-dark border border-secondary small">UX Research</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top border-secondary">
                        <small class="text-neon fw-bold">From ₹700/hr</small>
                        <a href="#hire-form" class="btn btn-sm btn-outline-light">Hire</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- HIRING MODELS -->
<section class="py-5 border-top border-secondary">
    <div class="container py-4">
        <div class="text-center mb-5">
            <span class="badge border border-neon text-neon mb-3 px-3 py-2 text-uppercase">Flexible Engagement</span>
            <h2 class="h3 text-white fw-bold">How You Can <span class="text-neon">Hire</span></h2>
        </div>

        <div class="row g-4 justify-content-center">
            <div class="col-md-4">
                <div class="service-detail-card p-4 border border-secondary rounded h-100 text-center">
                    <i class="fas fa-clock fa-2x text-neon mb-3"></i>
                    <h4 class="text-white h5 fw-bold">Hourly Basis</h4>
                    <p class="text-white-50 small mb-3">Pay only for the hours worked. Ideal for small tasks, bug fixes, consultations, and quick improvements.</p>
                    <span class="badge bg-dark border border-secondary p-2">Min 10 hours</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="service-detail-card p-4 border border-neon rounded h-100 text-center" style="box-shadow: 0 0 20px rgba(0,229,255,0.1);">
                    <i class="fas fa-calendar-alt fa-2x text-neon mb-3"></i>
                    <h4 class="text-white h5 fw-bold">Monthly Retainer</h4>
                    <p class="text-white-50 small mb-3">Dedicated expert(s) working on your project every month. Includes regular reporting and priority support.</p>
                    <span class="badge bg-dark border border-neon p-2">Most Popular</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="service-detail-card p-4 border border-secondary rounded h-100 text-center">
                    <i class="fas fa-project-diagram fa-2x text-neon mb-3"></i>
                    <h4 class="text-white h5 fw-bold">Fixed-Price Project</h4>
                    <p class="text-white-50 small mb-3">Defined scope, fixed budget, clear deliverables. Best for one-time builds with well-defined requirements.</p>
                    <span class="badge bg-dark border border-secondary p-2">Clear Scope</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- WHY HIRE FROM US -->
<section class="py-5 border-top border-secondary" style="background: linear-gradient(180deg, rgba(10,13,15,0.85) 0%, rgba(5,5,5,0.8) 100%);">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="h3 text-white fw-bold">Why Hire From <span class="text-neon">Nectra Digital?</span></h2>
        </div>
        <div class="row g-3">
            <div class="col-6 col-lg-3">
                <div class="text-center p-3 border border-secondary rounded h-100 service-detail-card">
                    <i class="fas fa-bolt fa-lg text-neon mb-2"></i>
                    <h6 class="text-white small fw-bold mb-1">48hr Onboarding</h6>
                    <p class="text-white-50 small mb-0">Expert starts working within 48 hours of confirmation</p>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="text-center p-3 border border-secondary rounded h-100 service-detail-card">
                    <i class="fas fa-exchange-alt fa-lg text-neon mb-2"></i>
                    <h6 class="text-white small fw-bold mb-1">Easy Replacement</h6>
                    <p class="text-white-50 small mb-0">Not satisfied? We replace the expert at no extra cost</p>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="text-center p-3 border border-secondary rounded h-100 service-detail-card">
                    <i class="fas fa-file-contract fa-lg text-neon mb-2"></i>
                    <h6 class="text-white small fw-bold mb-1">NDA & IP Protection</h6>
                    <p class="text-white-50 small mb-0">Signed NDA + full IP ownership transferred to you</p>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="text-center p-3 border border-secondary rounded h-100 service-detail-card">
                    <i class="fas fa-tasks fa-lg text-neon mb-2"></i>
                    <h6 class="text-white small fw-bold mb-1">Daily Reporting</h6>
                    <p class="text-white-50 small mb-0">Daily standups + weekly progress reports via Slack/Teams</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- PROCESS -->
<section class="py-5 border-top border-secondary">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="h3 text-white fw-bold">How It <span class="text-neon">Works</span></h2>
        </div>
        <div class="row g-4 justify-content-center">
            <div class="col-md-6 col-lg-3">
                <div class="process-card text-center p-4 border border-secondary rounded h-100 position-relative">
                    <div class="process-number">01</div>
                    <i class="fas fa-file-alt fa-2x text-neon mb-3"></i>
                    <h4 class="h6 text-white fw-bold">Submit Requirements</h4>
                    <p class="text-white-50 small mb-0">Fill the form below with your project details, tech stack needs, and timeline.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="process-card text-center p-4 border border-secondary rounded h-100 position-relative">
                    <div class="process-number">02</div>
                    <i class="fas fa-user-check fa-2x text-neon mb-3"></i>
                    <h4 class="h6 text-white fw-bold">Expert Matching</h4>
                    <p class="text-white-50 small mb-0">We match you with the best-fit expert(s) based on skills, experience, and availability.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="process-card text-center p-4 border border-secondary rounded h-100 position-relative">
                    <div class="process-number">03</div>
                    <i class="fas fa-handshake fa-2x text-neon mb-3"></i>
                    <h4 class="h6 text-white fw-bold">Interview & Confirm</h4>
                    <p class="text-white-50 small mb-0">Interview the expert, discuss scope, and confirm engagement terms.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="process-card text-center p-4 border border-secondary rounded h-100 position-relative">
                    <div class="process-number">04</div>
                    <i class="fas fa-rocket fa-2x text-neon mb-3"></i>
                    <h4 class="h6 text-white fw-bold">Start Working</h4>
                    <p class="text-white-50 small mb-0">Expert onboards within 48 hours and starts delivering with daily updates.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- HIRE FORM -->
<section id="hire-form" class="py-5 border-top border-secondary" style="background: linear-gradient(180deg, rgba(10,13,15,0.85) 0%, rgba(5,5,5,0.8) 100%);">
    <div class="container py-4">
        <div class="text-center mb-5">
            <span class="badge border border-neon text-neon mb-3 px-3 py-2 text-uppercase">Get Started</span>
            <h2 class="display-6 text-white fw-bold">Hire Your <span class="text-neon">Expert Now</span></h2>
            <p class="text-white-50 mt-3">Fill in your requirements and we'll match you with the perfect expert within 24 hours.</p>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="p-4 p-md-5 border border-secondary rounded" style="background: rgba(15,15,15,0.7); backdrop-filter: blur(5px);">
                    
                    <?php if($msg): ?>
                    <div class="alert alert-<?php echo $msg_type; ?> border-<?php echo $msg_type; ?>" style="background: rgba(0,0,0,0.5);">
                        <i class="fas fa-<?php echo ($msg_type=='success') ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
                        <?php echo $msg; ?>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="hire-experts#hire-form">
                        <input type="text" name="website_url" style="display:none; opacity:0; visibility:hidden;" autocomplete="off" tabindex="-1">
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label small text-white-50 text-uppercase" style="letter-spacing: 1px;">Full Name *</label>
                                <input type="text" name="full_name" class="form-control" required placeholder="Your full name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-white-50 text-uppercase" style="letter-spacing: 1px;">Email Address *</label>
                                <input type="email" name="email" class="form-control" required placeholder="email@company.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-white-50 text-uppercase" style="letter-spacing: 1px;">Phone / WhatsApp *</label>
                                <input type="text" name="phone" class="form-control" required placeholder="+91 98765 43210">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-white-50 text-uppercase" style="letter-spacing: 1px;">Expert Required *</label>
                                <select name="service_needed" class="form-select" required>
                                    <option value="" disabled selected>Select expert type...</option>
                                    <option value="Full-Stack Web Developer">Full-Stack Web Developer</option>
                                    <option value="Frontend Developer (React/Next.js)">Frontend Developer (React/Next.js)</option>
                                    <option value="Backend Developer (Node/PHP/Python)">Backend Developer (Node/PHP/Python)</option>
                                    <option value="Mobile App Developer">Mobile App Developer (React Native/Flutter)</option>
                                    <option value="SEO Specialist">SEO Specialist</option>
                                    <option value="Digital Marketing Expert">Digital Marketing Expert</option>
                                    <option value="AI & Automation Engineer">AI & Automation Engineer</option>
                                    <option value="UI/UX Designer">UI/UX Designer</option>
                                    <option value="E-Commerce Developer (Shopify)">E-Commerce Developer (Shopify)</option>
                                    <option value="DevOps / Cloud Engineer">DevOps / Cloud Engineer</option>
                                    <option value="Multiple Experts / Team">Multiple Experts / Full Team</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-white-50 text-uppercase" style="letter-spacing: 1px;">Budget Range *</label>
                                <select name="budget" class="form-select" required>
                                    <option value="" disabled selected>Select budget...</option>
                                    <option value="Under ₹25,000">Under ₹25,000 (Small task)</option>
                                    <option value="₹25,000 - ₹75,000">₹25,000 - ₹75,000 (Standard)</option>
                                    <option value="₹75,000 - ₹2,00,000">₹75,000 - ₹2,00,000 (Premium)</option>
                                    <option value="₹2,00,000+">₹2,00,000+ (Enterprise)</option>
                                    <option value="Monthly Retainer">Monthly Retainer (₹15K+/mo)</option>
                                    <option value="Not Sure">Not Sure — Let's Discuss</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-white-50 text-uppercase" style="letter-spacing: 1px;">Timeline</label>
                                <select name="timeline" class="form-select">
                                    <option value="ASAP">ASAP (Within 1 week)</option>
                                    <option value="2-4 weeks">2-4 Weeks</option>
                                    <option value="1-2 months">1-2 Months</option>
                                    <option value="Ongoing">Ongoing / Long-term</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label small text-white-50 text-uppercase" style="letter-spacing: 1px;">Project Details *</label>
                                <textarea name="project_details" class="form-control" rows="4" required placeholder="Describe your project, tech stack requirements, and what the expert should accomplish..."></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" name="submit_hire" class="btn btn-nectra w-100 py-3 mt-2">
                                    SUBMIT HIRING REQUEST <i class="fas fa-paper-plane ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="text-center mt-4 pt-4 border-top border-secondary">
                        <p class="text-white-50 small mb-3">Or connect directly:</p>
                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                            <a href="https://wa.me/917678387759?text=Hi%2C%20I%20want%20to%20hire%20an%20expert%20from%20Nectra%20Digital." target="_blank" class="btn btn-success btn-sm"><i class="fab fa-whatsapp me-1"></i> WhatsApp</a>
                            <a href="mailto:contact@nectradigital.com" class="btn btn-outline-light btn-sm"><i class="fas fa-envelope me-1"></i> Email Us</a>
                            <a href="tel:+917678387759" class="btn btn-outline-light btn-sm"><i class="fas fa-phone me-1"></i> Call Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
