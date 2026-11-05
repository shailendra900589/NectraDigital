<?php 
require_once 'includes/db.php';

// --- HANDLE APPLICATION SUBMISSION ---
$msg = "";
$error_msg = "";

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply_job'])) {
    
    // 1. HONEYPOT (BOT TRAP)
    $hp = $_POST['website_url'] ?? '';
    if(!empty($hp)) {
        // Bot detected - Do nothing, show fake success
        $msg = "Your application has been received successfully.";
    } 
    else {
        // 2. SECURITY: LINK PREVENTION IN COVER LETTER
        $cv_text = clean_input($_POST['cover_letter']);
        $has_link = false;
        $patterns = ["http:", "https:", "www.", ".com", ".net", ".org", "href="];
        foreach($patterns as $p) { 
            if(stripos($cv_text, $p) !== false) $has_link = true; 
        }

        if($has_link) {
            $error_msg = "Security Notice: Links are not allowed in the cover letter.";
        } else {
            // 3. HANDLE PDF UPLOAD
            $job_id = intval($_POST['job_id']);
            $name = clean_input($_POST['name']);
            $email = clean_input($_POST['email']);
            
            if(isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
                $file = $_FILES['resume'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $mime = mime_content_type($file['tmp_name']);
                
                // STRICT PDF CHECK (Max 2MB)
                if($ext === 'pdf' && $mime === 'application/pdf' && $file['size'] <= 2097152) { 
                    $target_dir = "assets/uploads/resumes/";
                    if(!is_dir($target_dir)) mkdir($target_dir, 0777, true);
                    
                    $new_name = uniqid("cv_", true) . ".pdf";
                    
                    if(move_uploaded_file($file['tmp_name'], $target_dir . $new_name)) {
                        $full_path = $target_dir . $new_name;
                        
                        // 4. SAVE TO DATABASE
                        $stmt = $conn->prepare("INSERT INTO applications (job_id, name, email, cover_letter, resume_path, ip_address) VALUES (?, ?, ?, ?, ?, ?)");
                        $ip = $_SERVER['REMOTE_ADDR'];
                        $stmt->bind_param("isssss", $job_id, $name, $email, $cv_text, $full_path, $ip);
                        
                        if($stmt->execute()) {
                            $msg = "Your application has been successfully submitted to our talent team.";
                        } else {
                            $error_msg = "Database Error. Please try again or email us directly.";
                        }
                    } else {
                        $error_msg = "File upload failed. Server permission error.";
                    }
                } else {
                    $error_msg = "Invalid file. Only PDF documents under 2MB are accepted.";
                }
            } else {
                $error_msg = "Please attach your resume in PDF format.";
            }
        }
    }
}

// SEO SETTINGS
$page_title = "Careers at Nectra Digital — Join Our Engineering & Growth Team";
$page_desc = "Explore career opportunities at Nectra Digital. We hire talented software engineers, AI developers, and technical growth specialists for high-impact enterprise projects.";
include 'includes/header.php'; 
?>

<main class="bg-light">
    <!-- Hero Header -->
    <header class="nd-page-hero nd-legal-hero text-center">
        <div class="container py-2 py-md-3 text-center">
            <span class="badge bg-primary bg-opacity-25 text-info border border-primary border-opacity-25 px-3 py-1.5 rounded-pill mb-3 small fw-semibold text-uppercase" style="letter-spacing: 0.05em;">
                Engineering & Growth Careers
            </span>
            <h1 class="fw-bold text-white mb-3 nd-hero-title">Build High-Impact Digital Systems</h1>
            <p class="lead text-white-50 mx-auto nd-prose-width">
                Join an engineering-driven digital studio building mission-critical web applications, AI automation agents, and compounding growth engines.
            </p>
        </div>
    </header>

    <div class="container mt-4">
        <?php if($msg): ?>
        <div class="alert alert-success d-flex align-items-center gap-2 rounded-3 shadow-xs">
            <i class="fas fa-check-circle"></i>
            <div><?php echo htmlspecialchars($msg); ?></div>
        </div>
        <?php endif; ?>
        <?php if($error_msg): ?>
        <div class="alert alert-danger d-flex align-items-center gap-2 rounded-3 shadow-xs">
            <i class="fas fa-exclamation-triangle"></i>
            <div><?php echo htmlspecialchars($error_msg); ?></div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Job Listings Section -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <span class="text-primary fw-semibold small text-uppercase" style="letter-spacing: 0.05em;">Open Positions</span>
                <h2 class="h2 fw-bold text-dark mt-1">Current Opportunities</h2>
                <p class="text-muted small mx-auto" style="max-width: 520px;">We offer competitive compensation, remote flexibility, and the opportunity to work on bleeding-edge tech stacks.</p>
            </div>

            <div class="row g-4">
                <?php
                $sql = "SELECT * FROM careers WHERE status='Open' ORDER BY created_at DESC";
                $result = $conn->query($sql);
                
                if($result && $result->num_rows > 0) {
                    while($job = $result->fetch_assoc()) {
                        
                        // SEO: Auto-Generate Job Schema
                        $schema = json_encode([
                            "@context" => "https://schema.org",
                            "@type" => "JobPosting",
                            "title" => $job['position'],
                            "description" => strip_tags($job['description']),
                            "datePosted" => date('Y-m-d', strtotime($job['created_at'])),
                            "hiringOrganization" => ["@type" => "Organization", "name" => "Nectra Digital", "sameAs" => "https://www.nectradigital.com"],
                            "jobLocation" => ["@type" => "Place", "address" => $job['location']],
                            "employmentType" => "FULL_TIME"
                        ], JSON_UNESCAPED_SLASHES);
                        echo "<script type='application/ld+json'>$schema</script>";
                        ?>
                        <div class="col-lg-10 mx-auto">
                            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white position-relative">
                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
                                    <div>
                                        <h3 class="h4 fw-bold text-dark mb-2"><?php echo htmlspecialchars($job['position']); ?></h3>
                                        <div class="d-flex flex-wrap align-items-center gap-3 text-muted small">
                                            <span><i class="fas fa-code text-primary me-1"></i> <?php echo htmlspecialchars($job['stack']); ?></span>
                                            <span><i class="fas fa-map-marker-alt text-primary me-1"></i> <?php echo htmlspecialchars($job['location']); ?></span>
                                            <span><i class="far fa-clock text-primary me-1"></i> Full Time</span>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary px-4 py-2 rounded-pill fw-semibold shadow-xs" data-bs-toggle="modal" data-bs-target="#applyModal<?php echo $job['id']; ?>">
                                        Apply Now <i class="fas fa-arrow-right ms-1"></i>
                                    </button>
                                </div>
                                
                                <hr class="border-secondary border-opacity-10 my-3">
                                
                                <div class="text-secondary small job-desc">
                                    <?php echo $job['description']; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Apply Modal -->
                        <div class="modal fade" id="applyModal<?php echo $job['id']; ?>" tabindex="-1" aria-labelledby="modalLabel<?php echo $job['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                    <div class="modal-header bg-dark text-white p-4" style="background: #081426 !important;">
                                        <div>
                                            <span class="badge bg-primary bg-opacity-25 text-info px-2.5 py-1 rounded-pill small mb-1">Job Application</span>
                                            <h5 class="modal-title fw-bold text-white mb-0" id="modalLabel<?php echo $job['id']; ?>">
                                                Apply for <?php echo htmlspecialchars($job['position']); ?>
                                            </h5>
                                        </div>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4 p-md-5 bg-white">
                                        <form method="POST" enctype="multipart/form-data">
                                            <input type="hidden" name="job_id" value="<?php echo $job['id']; ?>">
                                            <input type="text" name="website_url" style="display:none;" autocomplete="off">
                                            
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-semibold text-dark">Full Name *</label>
                                                    <input type="text" name="name" class="form-control" placeholder="e.g. Rahul Sharma" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-semibold text-dark">Email Address *</label>
                                                    <input type="email" name="email" class="form-control" placeholder="rahul@example.com" required>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small fw-semibold text-dark">Cover Letter * <span class="text-muted fw-normal">(No external URLs allowed)</span></label>
                                                    <textarea name="cover_letter" class="form-control" rows="4" placeholder="Briefly describe your background, tech stack experience, and why you're a fit..." required></textarea>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small fw-semibold text-dark">Upload Resume * <span class="text-muted fw-normal">(PDF only, max 2MB)</span></label>
                                                    <input type="file" name="resume" class="form-control" accept=".pdf" required>
                                                </div>
                                                <div class="col-12 mt-4">
                                                    <button type="submit" name="apply_job" class="btn btn-primary w-100 py-3 rounded-pill fw-semibold shadow-xs">
                                                        Submit Application <i class="fas fa-paper-plane ms-2"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<div class='col-12 text-center text-muted py-5'><div class='p-5 bg-white rounded-4 shadow-sm max-w-md mx-auto'><i class='fas fa-briefcase fa-3x text-muted mb-3 opacity-50'></i><h4 class='fw-bold text-dark mb-1'>No Open Positions Right Now</h4><p class='small text-muted mb-0'>We don't have active vacancies at this moment. You are welcome to send an open portfolio inquiry via our contact page.</p></div></div>";
                }
                ?>
            </div>
        </div>
    </section>
</main>

<style>
/* Job Description Formatting */
.job-desc ul { padding-left: 20px; color: #475569; margin-bottom: 15px; }
.job-desc li { margin-bottom: 6px; }
.job-desc p { margin-bottom: 15px; line-height: 1.6; color: #475569; }
.job-desc h4, .job-desc h5, .job-desc strong { color: #0F172A; display: block; margin-top: 20px; margin-bottom: 10px; font-weight: 700; }
</style>

<?php include 'includes/footer.php'; ?>