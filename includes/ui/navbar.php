<?php
require_once dirname(__DIR__) . '/brand-config.php';
require_once dirname(__DIR__) . '/nav-data.php';
$isDarkNav = !empty($isDarkHeader);
$ndNavPaths = [
    'home' => '/',
    'services' => '/services',
    'solutions' => '/solutions',
    'portfolio' => '/portfolio',
    'about' => '/about',
    'insights' => '/insights',
    'contact' => '/contact',
];
?>
<nav class="navbar navbar-expand-lg fixed-top nd-navbar <?php echo $isDarkNav ? 'nd-navbar-dark' : ''; ?>" id="mainNavbar">
    <div class="container position-relative">
        <a class="nd-navbar-brand notranslate" href="<?php echo htmlspecialchars(nectra_nav_path('/')); ?>" aria-label="Nectra Digital Home">
            <span>NECTRA</span><span class="nd-brand-accent">DIGITAL</span>
        </a>

        <div class="d-flex align-items-center gap-2 d-lg-none">
            <a href="<?php echo htmlspecialchars(nectra_nav_path(NECTRA_CTA_PRIMARY_URL)); ?>" class="btn btn-nectra-primary btn-nectra-sm"><?php echo htmlspecialchars(NECTRA_CTA_PRIMARY_LABEL); ?></a>
            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNavOffcanvas" aria-controls="mobileNavOffcanvas" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <div class="collapse navbar-collapse d-none d-lg-flex" id="desktopNav">
            <ul class="navbar-nav ms-auto align-items-center nd-desktop-nav">
                <li class="nav-item"><a class="<?php echo nectra_nav_link_class($ndNavPaths['home']); ?>" href="<?php echo htmlspecialchars(nectra_nav_path('/')); ?>"<?php echo nectra_nav_link_aria($ndNavPaths['home']); ?>>Home</a></li>
                <li class="nav-item dropdown nd-dropdown-mega">
                    <a class="<?php echo nectra_nav_link_class($ndNavPaths['services'], 'dropdown-toggle'); ?>" href="<?php echo htmlspecialchars(nectra_nav_path('/services')); ?>" id="servicesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" aria-haspopup="true"<?php echo nectra_nav_link_aria($ndNavPaths['services']); ?>>
                        Services
                    </a>
                    <?php include __DIR__ . '/mega-menu.php'; ?>
                </li>
                <li class="nav-item"><a class="<?php echo nectra_nav_link_class($ndNavPaths['solutions']); ?>" href="<?php echo htmlspecialchars(nectra_nav_path('/solutions')); ?>"<?php echo nectra_nav_link_aria($ndNavPaths['solutions']); ?>>Solutions</a></li>
                <li class="nav-item"><a class="<?php echo nectra_nav_link_class($ndNavPaths['portfolio']); ?>" href="<?php echo htmlspecialchars(nectra_nav_path('/portfolio')); ?>"<?php echo nectra_nav_link_aria($ndNavPaths['portfolio']); ?>>Portfolio</a></li>
                <li class="nav-item"><a class="<?php echo nectra_nav_link_class($ndNavPaths['about']); ?>" href="<?php echo htmlspecialchars(nectra_nav_path('/about')); ?>"<?php echo nectra_nav_link_aria($ndNavPaths['about']); ?>>About</a></li>
                <li class="nav-item"><a class="<?php echo nectra_nav_link_class($ndNavPaths['insights']); ?>" href="<?php echo htmlspecialchars(nectra_nav_path('/insights')); ?>"<?php echo nectra_nav_link_aria($ndNavPaths['insights']); ?>>Insights</a></li>
                <li class="nav-item"><a class="<?php echo nectra_nav_link_class($ndNavPaths['contact']); ?>" href="<?php echo htmlspecialchars(nectra_nav_path('/contact')); ?>"<?php echo nectra_nav_link_aria($ndNavPaths['contact']); ?>>Contact</a></li>
                <li class="nav-item ms-lg-2 d-none d-xl-block">
                    <?php
                    $langSwitcherPath = dirname(__DIR__) . '/language-switcher.php';
                    if (is_file($langSwitcherPath)) {
                        include $langSwitcherPath;
                    }
                    ?>
                </li>
                <li class="nav-item ms-lg-2">
                    <a href="<?php echo htmlspecialchars(nectra_nav_path(NECTRA_CTA_PRIMARY_URL)); ?>" class="btn btn-nectra-primary btn-nectra-sm">
                        <?php echo htmlspecialchars(NECTRA_CTA_PRIMARY_LABEL); ?> <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<?php include __DIR__ . '/mobile-menu.php'; ?>
