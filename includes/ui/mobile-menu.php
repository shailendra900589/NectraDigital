<?php
require_once dirname(__DIR__) . '/brand-config.php';
require_once dirname(__DIR__) . '/nav-data.php';
require_once dirname(__DIR__) . '/site-contact.php';
$mobileId = 'mobileNavAccordion';
?>
<div class="offcanvas offcanvas-start nd-offcanvas" tabindex="-1" id="mobileNavOffcanvas" aria-labelledby="mobileNavLabel">
    <div class="offcanvas-header">
        <a class="nd-navbar-brand text-white" href="<?php echo htmlspecialchars(nectra_nav_path('/')); ?>" id="mobileNavLabel">
            <span>NECTRA</span><span class="text-tech-blue">DIGITAL</span>
        </a>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column p-0">
        <div class="accordion accordion-flush nd-mobile-accordion flex-grow-1" id="<?php echo $mobileId; ?>">
            <div class="accordion-item bg-transparent border-0">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#mobServices" aria-expanded="false">Services</button>
                </h2>
                <div id="mobServices" class="accordion-collapse collapse" data-bs-parent="#<?php echo $mobileId; ?>">
                    <div class="accordion-body pt-0">
                        <?php foreach (nectra_mega_menu_columns() as $col): ?>
                        <p class="text-uppercase text-muted small fw-bold mb-2 mt-2"><?php echo htmlspecialchars($col['title']); ?></p>
                        <?php foreach (array_slice($col['items'], 0, 3) as $item): ?>
                        <a href="<?php echo htmlspecialchars(nectra_nav_path($item['url'])); ?>" class="nav-link py-2"><?php echo htmlspecialchars($item['title']); ?></a>
                        <?php endforeach; ?>
                        <?php endforeach; ?>
                        <a href="<?php echo htmlspecialchars(nectra_nav_path('/services')); ?>" class="nav-link py-2 text-tech-blue fw-semibold">View All Services →</a>
                    </div>
                </div>
            </div>
            <div class="accordion-item bg-transparent border-0">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#mobCompany" aria-expanded="false">Company</button>
                </h2>
                <div id="mobCompany" class="accordion-collapse collapse" data-bs-parent="#<?php echo $mobileId; ?>">
                    <div class="accordion-body pt-0">
                        <a href="<?php echo htmlspecialchars(nectra_nav_path('/about')); ?>" class="nav-link py-2">About</a>
                        <a href="<?php echo htmlspecialchars(nectra_nav_path('/solutions')); ?>" class="nav-link py-2">Solutions</a>
                        <a href="<?php echo htmlspecialchars(nectra_nav_path('/portfolio')); ?>" class="nav-link py-2">Portfolio</a>
                        <a href="<?php echo htmlspecialchars(nectra_nav_path('/careers')); ?>" class="nav-link py-2">Careers</a>
                        <a href="<?php echo htmlspecialchars(nectra_nav_path('/insights')); ?>" class="nav-link py-2">Insights</a>
                        <a href="<?php echo htmlspecialchars(nectra_nav_path('/contact')); ?>" class="nav-link py-2">Contact</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-4 border-top border-secondary mt-auto">
            <div class="mb-3">
                <?php
                $langSwitcherPath = dirname(__DIR__) . '/language-switcher.php';
                if (is_file($langSwitcherPath)) {
                    include $langSwitcherPath;
                }
                ?>
            </div>
            <a href="<?php echo htmlspecialchars(nectra_nav_path(NECTRA_CTA_PRIMARY_URL)); ?>" class="btn btn-nectra-primary w-100 mb-2"><?php echo htmlspecialchars(NECTRA_CTA_PRIMARY_LABEL); ?></a>
            <p class="text-center text-muted small mb-0">
                <a href="tel:<?php echo NECTRA_PHONE_E164; ?>" class="text-white-50 text-decoration-none"><?php echo NECTRA_PHONE_DISPLAY; ?></a>
            </p>
        </div>
    </div>
</div>
