<?php
require_once dirname(__DIR__) . '/brand-config.php';
require_once dirname(__DIR__) . '/nav-data.php';
?>
<section class="nd-hero-home nd-section-dark">
    <div class="nd-hero-glow"></div>
    <div class="container position-relative z-1">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <h1 class="nd-hero-title text-white mb-3">We Design &amp; Develop Software, Websites and Mobile Apps That Move Businesses Forward</h1>
                <p class="nd-hero-subtitle-dark mb-4">Nectra Digital helps startups, growing businesses and enterprises design, build and scale custom software, high-performance websites and mobile applications.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="<?php echo nectra_nav_path(NECTRA_CTA_PRIMARY_URL); ?>" class="btn btn-nectra-primary btn-lg"><?php echo NECTRA_CTA_PRIMARY_LABEL; ?></a>
                    <a href="<?php echo nectra_nav_path('/portfolio'); ?>" class="btn btn-nectra-outline-light btn-lg">View Our Work</a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="nd-hero-visual" aria-hidden="true">
                    <div class="nd-hero-visual-bar"><span></span><span></span><span></span></div>
                    <p class="text-white-50 small mb-2">Product engineering workspace</p>
                    <div class="row g-2 mb-2">
                        <div class="col-6"><div class="p-2 rounded bg-dark border border-secondary border-opacity-25 text-white small">Web platform</div></div>
                        <div class="col-6"><div class="p-2 rounded bg-dark border border-secondary border-opacity-25 text-white small">Mobile app</div></div>
                    </div>
                    <div class="p-2 rounded bg-dark border border-secondary border-opacity-25 text-white-50 small mb-0">API · Database · Deployment pipeline</div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include dirname(__DIR__) . '/ui/trust-bar.php'; ?>

<section class="nd-section nd-section-alt">
    <div class="container">
        <div class="text-center nd-section-title-wrap mb-5">
            <h2 class="nd-section-title">Build Better Digital Products</h2>
            <p class="text-muted mx-auto" style="max-width: 640px;">Development-first delivery — software, web and mobile at the center of what we do.</p>
        </div>
        <div class="row g-4">
            <?php
            $core = [
                ['num' => '01', 'title' => 'Custom Software Development', 'desc' => 'Tailored software systems, SaaS platforms, business applications, portals and workflow solutions.', 'url' => '/software-development-services'],
                ['num' => '02', 'title' => 'Website Design & Development', 'desc' => 'Fast, scalable, conversion-focused corporate websites, web platforms and e-commerce experiences.', 'url' => '/web-development-services'],
                ['num' => '03', 'title' => 'Mobile App Development', 'desc' => 'Android, iOS and cross-platform mobile applications built around real business requirements.', 'url' => '/mobile-app-development'],
            ];
            foreach ($core as $i => $c): ?>
            <div class="col-md-4">
                <article class="nd-card nd-core-service-card p-4 <?php echo $i === 0 ? 'nd-core-featured' : ''; ?>">
                    <span class="text-primary-blue fw-bold small"><?php echo $c['num']; ?></span>
                    <h3 class="h5 fw-bold mt-2"><?php echo htmlspecialchars($c['title']); ?></h3>
                    <p class="text-muted small mb-3"><?php echo htmlspecialchars($c['desc']); ?></p>
                    <a href="<?php echo nectra_nav_path($c['url']); ?>" class="fw-semibold text-primary-blue text-decoration-none small">Learn more <i class="fas fa-arrow-right ms-1"></i></a>
                </article>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="nd-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="nd-section-title">From Idea to Production</h2>
        </div>
        <div class="row g-3 nd-cap-grid">
            <?php
            $caps = ['Web Applications', 'SaaS Platforms', 'Enterprise Software', 'E-commerce', 'UI/UX Design', 'API Integrations', 'AI & Automation', 'Maintenance & Support'];
            foreach ($caps as $cap): ?>
            <div class="col-6 col-md-3"><div class="nd-cap-item"><i class="fas fa-check text-primary-blue me-2"></i><?php echo htmlspecialchars($cap); ?></div></div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="nd-section nd-section-alt">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="nd-section-title">How We Work</h2>
            <p class="text-muted mx-auto mb-0" style="max-width: 36rem;">End-to-end delivery — from discovery through launch and long-term scale.</p>
        </div>
        <?php include dirname(__DIR__) . '/ui/process.php'; ?>
    </div>
</section>

<section class="nd-section">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
            <div>
                <h2 class="nd-section-title mb-1">Selected Projects</h2>
                <p class="text-muted mb-0">Representative engineering work — see portfolio for full detail.</p>
            </div>
            <a href="<?php echo nectra_nav_path('/portfolio'); ?>" class="btn btn-nectra-secondary">View portfolio</a>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="nd-card p-4 h-100">
                    <span class="badge bg-light text-secondary border mb-2">Fintech · Software</span>
                    <h3 class="h6 fw-bold">Payment operations dashboard</h3>
                    <p class="small text-muted mb-2">Secure web application for transaction monitoring and settlements.</p>
                    <p class="small mb-0"><strong>Tech:</strong> React, Node.js, cloud hosting</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="nd-card p-4 h-100">
                    <span class="badge bg-light text-secondary border mb-2">E-commerce · Web</span>
                    <h3 class="h6 fw-bold">D2C brand storefront</h3>
                    <p class="small text-muted mb-2">Custom e-commerce experience with optimized checkout flows.</p>
                    <p class="small mb-0"><strong>Tech:</strong> Shopify, modern frontend</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="nd-card p-4 h-100">
                    <span class="badge bg-light text-secondary border mb-2">Automation · AI</span>
                    <h3 class="h6 fw-bold">Voice-enabled sales assistant</h3>
                    <p class="small text-muted mb-2">AI workflow connecting inbound leads to scheduling and CRM.</p>
                    <p class="small mb-0"><strong>Tech:</strong> Python, APIs, integrations</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="nd-section nd-section-alt">
    <div class="container text-center">
        <h2 class="nd-section-title mb-4">Who We Build For</h2>
        <div class="d-flex flex-wrap justify-content-center gap-2">
            <?php foreach (['FinTech', 'Healthcare', 'Education', 'E-commerce', 'Professional Services', 'Retail', 'Real Estate', 'Logistics', 'SaaS', 'Startups'] as $ind): ?>
            <span class="nd-industry-pill"><?php echo htmlspecialchars($ind); ?></span>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="nd-section">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="nd-section-title">Stack &amp; Tools</h2>
            <p class="text-muted">Technologies we use regularly on client projects (final stack depends on scope).</p>
        </div>
        <?php
        $stack = [
            'Frontend' => ['React', 'Next.js', 'Bootstrap', 'WordPress'],
            'Backend' => ['PHP', 'Laravel', 'Node.js', 'Python'],
            'Mobile' => ['React Native', 'Flutter'],
            'Data & Cloud' => ['MySQL', 'REST APIs', 'AWS'],
        ];
        foreach ($stack as $label => $items): ?>
        <p class="small fw-bold text-muted text-uppercase mb-2"><?php echo htmlspecialchars($label); ?></p>
        <p class="mb-4"><?php foreach ($items as $t) { echo '<span class="nd-tech-badge">' . htmlspecialchars($t) . '</span>'; } ?></p>
        <?php endforeach; ?>
    </div>
</section>

<section class="nd-section nd-section-alt">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="nd-section-title">Why Nectra Digital</h2>
        </div>
        <div class="row g-3">
            <?php
            $why = [
                ['Business-First Development', 'Requirements, users and ROI drive every technical decision.'],
                ['Scalable Architecture', 'Clean structure for growth, integrations and long-term maintenance.'],
                ['Clean UI/UX', 'Interfaces that are clear, accessible and aligned with your brand.'],
                ['Transparent Delivery', 'Visible milestones, demos and honest scope conversations.'],
                ['Responsive Support', 'Post-launch fixes, iterations and partnership when you need it.'],
            ];
            foreach ($why as $w): ?>
            <div class="col-md-6 col-lg-4"><div class="nd-card p-4 h-100"><h3 class="h6 fw-bold"><?php echo htmlspecialchars($w[0]); ?></h3><p class="small text-muted mb-0"><?php echo htmlspecialchars($w[1]); ?></p></div></div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="nd-section nd-growth-secondary">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="nd-section-title">Build It. Launch It. Grow It.</h2>
            <p class="text-muted mx-auto" style="max-width: 680px;">After your product is live, our growth team can support SEO, paid media and measurable acquisition — complementary to development, not a replacement for it.</p>
        </div>
        <div class="row g-3">
            <?php
            $growth = [
                ['SEO', '/seo-services'],
                ['Technical SEO', '/technical-seo-services'],
                ['Performance Marketing', '/performance-marketing-services'],
                ['Google Ads', '/google-ads-management'],
                ['Social Media Marketing', '/social-media-marketing-services'],
            ];
            foreach ($growth as $g): ?>
            <div class="col-6 col-md-4 col-lg"><a href="<?php echo nectra_nav_path($g[1]); ?>" class="nd-card p-3 d-block text-decoration-none text-dark h-100"><span class="fw-semibold small"><?php echo htmlspecialchars($g[0]); ?></span></a></div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="nd-section nd-section-alt" id="contact">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="nd-card p-4 p-md-5">
                    <h2 class="h4 fw-bold text-center mb-1">Start a Project</h2>
                    <p class="text-muted small text-center mb-4">Tell us what you are building. We respond within one business day.</p>
                    <form id="contactForm" class="nd-ajax-form">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                        <div style="display:none"><input type="text" name="website_url" autocomplete="off"></div>
                        <div class="mb-3"><label class="form-label small fw-semibold">Name *</label><input type="text" name="name" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label small fw-semibold">Business email *</label><input type="email" name="email" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label small fw-semibold">Service needed *</label><?php nectra_render_service_select('service', true); ?></div>
                        <div class="mb-3"><label class="form-label small fw-semibold">Project details</label><textarea name="message" class="form-control" rows="3" placeholder="Goals, timeline, existing systems…"></textarea></div>
                        <button type="submit" class="btn btn-nectra-primary w-100"><?php echo NECTRA_CTA_PRIMARY_LABEL; ?></button>
                        <div class="response-msg mt-3 small text-center" id="responseMsg"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$home_faqs = get_homepage_faqs();
include dirname(__DIR__) . '/ui/faq.php';
$ctaHeading = 'Have a Product Idea or Development Requirement?';
$ctaText = 'Tell us what you are building. We will help you plan the right design, technology and development approach.';
include dirname(__DIR__) . '/ui/cta.php';
?>
