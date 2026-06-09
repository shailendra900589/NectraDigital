<?php 
$page_title = "Transmission Received";
$noindex = true;
include 'includes/header.php'; 
?>

<main class="d-flex align-items-center justify-content-center text-center" style="min-height: 80vh; background: #050505;">
    <div class="container">
        <div class="mb-4">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle border border-neon" style="width: 100px; height: 100px; box-shadow: 0 0 30px rgba(0,229,255,0.2);">
                <i class="fas fa-check fa-3x text-neon"></i>
            </div>
        </div>
        <h6 class="text-white-50 text-uppercase mb-2" style="letter-spacing: 2px;">Status: Encrypted & Sent</h6>
        <h1 class="display-4 fw-bold text-white mb-4">TRANSMISSION <span class="text-neon">RECEIVED</span></h1>
        <p class="lead text-white-50 mx-auto mb-5" style="max-width: 600px;">
            Your request has been logged in our secure database. A Nectra Digital strategist will establish contact within 24 hours.
        </p>
        <a href="/" class="btn btn-nectra">RETURN TO BASE</a>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
