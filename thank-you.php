<?php 
$page_title = "Inquiry Received — Thank You | Nectra Digital";
$page_desc = "Thank you for reaching out to Nectra Digital. Our team has received your message and will respond within 4 business hours.";
$noindex = true;
include 'includes/header.php'; 
?>

<main class="d-flex align-items-center justify-content-center text-center py-5 bg-light" style="min-height: 75vh;">
    <div class="container py-5">
        <div class="card border-0 shadow-sm rounded-4 p-5 mx-auto bg-white" style="max-width: 640px;">
            <div class="mb-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10 text-success" style="width: 80px; height: 80px;">
                    <i class="fas fa-check fa-2x"></i>
                </div>
            </div>
            <span class="badge bg-success bg-opacity-10 text-success px-3 py-1 rounded-pill small mb-2 fw-semibold">Inquiry Confirmed</span>
            <h1 class="h2 fw-bold text-dark mb-3">Thank You for Reaching Out</h1>
            <p class="text-muted small mx-auto mb-4" style="line-height: 1.6;">
                Your message has been received by our client solutions team. A dedicated technical director will review your project parameters and get in touch within 4 business hours.
            </p>
            <div class="p-3 bg-light rounded-3 mb-4 text-start small text-muted">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="fas fa-clock text-primary"></i>
                    <span><strong>Expected Response:</strong> Within 4 hours (Mon–Fri, 9am–7pm IST)</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <i class="fab fa-whatsapp text-success"></i>
                    <span><strong>Urgent requirement?</strong> Message us on <a href="https://wa.me/917678387759" target="_blank" rel="noopener" class="text-success fw-semibold text-decoration-none">WhatsApp (+91 76783 87759)</a></span>
                </div>
            </div>
            <div class="d-flex flex-wrap justify-content-center gap-2">
                <a href="/" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold shadow-xs">
                    <i class="fas fa-home me-1"></i> Back to Homepage
                </a>
                <a href="/services" class="btn btn-outline-secondary px-4 py-2 rounded-pill fw-semibold">
                    Explore Services
                </a>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
