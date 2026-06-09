<?php 
require_once 'includes/db.php';

// --- HANDLE APPLICATION SUBMISSION ---
$msg = "";
$error_msg = "";

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply_job'])) {
    
    // 1. HONEYPOT (BOT TRAP)
    $hp = $_POST['website_url'];
    if(!empty($hp)) {
        // Bot detected - Do nothing, show fake success
        $msg = "Application transmitted.";
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
            $error_msg = "Security Alert: Links are strictly prohibited in the cover letter.";
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
                    // Create dir if not exists
                    if(!is_dir($target_dir)) mkdir($target_dir, 0777, true);
                    
                    $new_name = uniqid("cv_", true) . ".pdf";
                    
                    if(move_uploaded_file($file['tmp_name'], $target_dir . $new_name)) {
                        $full_path = $target_dir . $new_name;
                        
                        // 4. SAVE TO DATABASE
                        $stmt = $conn->prepare("INSERT INTO applications (job_id, name, email, cover_letter, resume_path, ip_address) VALUES (?, ?, ?, ?, ?, ?)");
                        $ip = $_SERVER['REMOTE_ADDR'];
                        $stmt->bind_param("isssss", $job_id, $name, $email, $cv_text, $full_path, $ip);
                        
                        if($stmt->execute()) {
                            $msg = "Application encrypted and transmitted to HR.";
                        } else {
                            $error_msg = "Database Error. Try again.";
                        }
                    } else {
                        $error_msg = "File upload failed. Server permission denied.";
                    }
                } else {
                    $error_msg = "Invalid File. Only PDF files under 2MB are allowed.";
                }
            } else {
                $error_msg = "Resume PDF is required.";
            }
        }
    }
}

// SEO SETTINGS
$page_title = "Careers & Recruitment Protocol";
$page_desc = "Join Nectra Digital. We recruit elite digital architects. Remote positions available for Devs, AI Engineers, and Marketers.";
include 'includes/header.php'; 
?>

<main>
    <header class="py-5 text-center" style="background: linear-gradient(to bottom, #050505 0%, #0a1518 100%); border-bottom: 1px solid #222;">
        <div class="container py-5">
            <h6 class="text-neon text-uppercase mb-3" style="letter-spacing: 3px;">Recruitment Protocol</h6>
            <h1 class="display-4 fw-bold text-white mb-4">JOIN THE <span class="text-neon">ELITE</span></h1>
            <p class="lead text-white-50 mx-auto" style="max-width: 700px;">
                We don't hire employees. We recruit Architects. <br>
                Execute high-level missions in Web, AI, and Growth.
            </p>
        </div>
    </header>

    <div class="container mt-4">
        <?php if($msg) echo "<div class='alert alert-success bg-dark text-success border border-success'><i class='fas fa-check-circle'></i> $msg</div>"; ?>
        <?php if($error_msg) echo "<div class='alert alert-danger bg-dark text-danger border border-danger'><i class='fas fa-shield-alt'></i> $error_msg</div>"; ?>
    </div>

    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                
                <?php
                $sql = "SELECT * FROM careers WHERE status='Open' ORDER BY created_at DESC";
                $result = $conn->query($sql);
                
                if($result->num_rows > 0) {
                    while($job = $result->fetch_assoc()) {
                        
                        // SEO: Auto-Generate Job Schema
                        $schema = json_encode([
                            "@context" => "https://schema.org",
                            "@type" => "JobPosting",
                            "title" => $job['position'],
                            "description" => strip_tags($job['description']),
                            "datePosted" => date('Y-m-d', strtotime($job['created_at'])),
                            "hiringOrganization" => ["@type" => "Organization", "name" => "Nectra Digital"],
                            "jobLocation" => ["@type" => "Place", "address" => $job['location']],
                            "employmentType" => "FULL_TIME"
                        ]);
                        echo "<script type='application/ld+json'>$schema</script>";

                        echo '
                        <div class="col-lg-10 mx-auto">
                            <div class="card border border-secondary p-4 shadow-lg" style="background-color: #111 !important;">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                    <div>
                                        <h3 class="text-white h4 mb-2">'.$job['position'].'</h3>
                                        <div class="text-white-50 small mb-3">
                                            <span class="me-3"><i class="fas fa-code text-neon me-1"></i> '.$job['stack'].'</span>
                                            <span class="me-3"><i class="fas fa-map-marker-alt text-neon me-1"></i> '.$job['location'].'</span>
                                            <span><i class="far fa-clock text-neon me-1"></i> Full Time</span>
                                        </div>
                                    </div>
                                    <button class="btn btn-nectra" data-bs-toggle="modal" data-bs-target="#applyModal'.$job['id'].'">
                                        APPLY NOW
                                    </button>
                                </div>
                                
                                <hr class="border-secondary my-4" style="opacity: 0.3;">
                                
                                <div class="text-white-50 small job-desc">
                                    '.$job['description'].'
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="applyModal'.$job['id'].'" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content border-secondary text-white" style="background-color: #000 !important; border: 1px solid #333;">
                                    <div class="modal-header border-secondary">
                                        <h5 class="modal-title">Apply: <span class="text-info">'.$job['position'].'</span></h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <form method="POST" enctype="multipart/form-data">
                                            <input type="hidden" name="job_id" value="'.$job['id'].'">
                                            <input type="text" name="website_url" style="display:none;" autocomplete="off">
                                            
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="small text-secondary mb-1">Full Name</label>
                                                    <input type="text" name="name" class="form-control bg-dark text-white border-secondary" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="small text-secondary mb-1">Email Address</label>
                                                    <input type="email" name="email" class="form-control bg-dark text-white border-secondary" required>
                                                </div>
                                                <div class="col-12">
                                                    <label class="small text-secondary mb-1">Cover Letter (Strictly No Links)</label>
                                                    <textarea name="cover_letter" class="form-control bg-dark text-white border-secondary" rows="4" placeholder="Tell us why you are the best fit..." required></textarea>
                                                </div>
                                                <div class="col-12">
                                                    <label class="small text-secondary mb-1">Upload Resume (PDF Only, Max 2MB)</label>
                                                    <input type="file" name="resume" class="form-control bg-dark text-white border-secondary" accept=".pdf" required>
                                                    <div class="form-text text-white-50">File must be .pdf format.</div>
                                                </div>
                                                <div class="col-12 mt-3">
                                                    <button type="submit" name="apply_job" class="btn btn-success w-100 py-3">TRANSMIT DOSSIER</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>';
                    }
                } else {
                    echo "<div class='text-center text-white-50 py-5'><p>No active recruitment protocols initiated. Check back later.</p></div>";
                }
                ?>
            </div>
        </div>
    </section>
</main>

<style>
/* Job Description Styling */
.job-desc ul { padding-left: 20px; color: #ccc; margin-bottom: 15px; }
.job-desc li { margin-bottom: 5px; }
.job-desc p { margin-bottom: 15px; line-height: 1.6; }
.job-desc h4, .job-desc h5, .job-desc strong { color: #fff; display: block; margin-top: 20px; margin-bottom: 10px; font-weight: bold; }
/* Modal Fixes */
.modal-backdrop.show { opacity: 0.8; }
</style>

<?php include 'includes/footer.php'; ?>