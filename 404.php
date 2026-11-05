<?php 
$page_title = "Page Not Found (404) | Nectra Digital";
$page_desc = "The page you are looking for does not exist or has been moved. Explore Nectra Digital's services, resources, and insights.";
require_once __DIR__ . '/includes/nav-data.php';
include 'includes/header.php'; 
?>

<main class="nd-page-404 d-flex align-items-center py-5" style="background: #F8FAFC; min-height: 80vh;">
    <div class="container text-center py-5">
        <h1 class="display-1 fw-bold text-dark mb-2 nd-page-404-code">404</h1>
        <h2 class="h3 fw-bold text-dark mb-3 nd-page-404-heading">Page Not Found</h2>
        <p class="text-muted mx-auto mb-4 nd-text-lead" style="max-width: 540px;">
            The URL you requested could not be found. It may have been relocated or updated. Use the links below to navigate to key sections:
        </p>

        <!-- Quick Navigation Cards -->
        <div class="row g-3 justify-content-center mb-5" style="max-width: 760px; margin: 0 auto;">
            <div class="col-sm-6 col-md-3">
                <a href="<?php echo nectra_nav_path('/'); ?>" class="nd-card p-3 d-block text-decoration-none text-center h-100 bg-white">
                    <i class="fas fa-house text-primary fa-lg mb-2"></i>
                    <span class="d-block text-dark small fw-bold">Home</span>
                </a>
            </div>
            <div class="col-sm-6 col-md-3">
                <a href="<?php echo nectra_nav_path('/services'); ?>" class="nd-card p-3 d-block text-decoration-none text-center h-100 bg-white">
                    <i class="fas fa-cogs text-primary fa-lg mb-2"></i>
                    <span class="d-block text-dark small fw-bold">All Services</span>
                </a>
            </div>
            <div class="col-sm-6 col-md-3">
                <a href="<?php echo nectra_nav_path('/insights'); ?>" class="nd-card p-3 d-block text-decoration-none text-center h-100 bg-white">
                    <i class="fas fa-newspaper text-primary fa-lg mb-2"></i>
                    <span class="d-block text-dark small fw-bold">Insights &amp; Blog</span>
                </a>
            </div>
            <div class="col-sm-6 col-md-3">
                <a href="<?php echo nectra_nav_path('/contact'); ?>" class="nd-card p-3 d-block text-decoration-none text-center h-100 bg-white">
                    <i class="fas fa-envelope text-primary fa-lg mb-2"></i>
                    <span class="d-block text-dark small fw-bold">Contact Us</span>
                </a>
            </div>
        </div>

        <div>
            <a href="<?php echo nectra_nav_path('/'); ?>" class="btn btn-primary px-4 py-2 fw-semibold">
                <i class="fas fa-arrow-left me-2"></i> Back to Homepage
            </a>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>