<?php 
$page_title = "404 // System Failure";
$page_desc = "Error 404. The requested digital asset could not be located in the Nectra Digital mainframe.";
include 'includes/header.php'; 
?>

<main class="d-flex align-items-center justify-content-center text-center" style="min-height: 80vh; background: transparent; overflow: hidden; position: relative;">
    
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: repeating-linear-gradient(0deg, transparent, transparent 1px, rgba(0, 229, 255, 0.03) 1px, rgba(0, 229, 255, 0.03) 2px); pointer-events: none;"></div>

    <div class="container position-relative z-1">
        <h1 class="display-1 fw-bold text-white mb-0" style="font-size: 8rem; text-shadow: 5px 0 0 rgba(255,0,0,0.5), -5px 0 0 rgba(0,229,255,0.5);">404</h1>
        
        <div class="bg-dark border border-danger d-inline-block px-3 py-1 rounded mb-4">
            <span class="text-danger fw-bold small"><i class="fas fa-exclamation-triangle me-2"></i> CRITICAL ERROR: SECTOR NOT FOUND</span>
        </div>
        
        <h2 class="text-white h4 mb-4">SIGNAL LOST</h2>
        <p class="text-white-50 mx-auto mb-5" style="max-width: 500px;">
            The digital asset you are looking for has been purged from our mainframe or never existed.
        </p>
        
        <a href="<?php echo SITE_URL; ?>/" class="btn btn-nectra btn-lg">
            <i class="fas fa-undo-alt me-2"></i> RETURN TO BASE
        </a>
    </div>
</main>

<?php include 'includes/footer.php'; ?>