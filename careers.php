<?php 
require_once 'includes/db.php';

// --- HANDLE APPLICATION SUBMISSION ---
$msg = "";
$error_msg = "";

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply_job'])) {
    
    $hp = $_POST['website_url'];
    if(!empty($hp)) {
        $msg = "Application transmitted.";
    } 
    else {
        $cv_text = clean_input($_POST['cover_letter']);
        $has_link = false;
        $patterns = ["http:", "https:", "www.", ".com", ".net", ".org", "href="];
        foreach($patterns as $p) { 
            if(stripos($cv_text, $p) !== false) $has_link = true; 
        }

        if($has_link) {
            $error_msg = "Security Alert: Links are strictly prohibited in the cover letter.";
        } else {
            $job_id = intval($_POST['job_id']);
            $name = clean_input($_POST['name']);
            $email = clean_input($_POST['email']);
            
            if(isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
                $file = $_FILES['resume'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $mime = mime_content_type($file['tmp_name']);
                
                if($ext === 'pdf' && $mime === 'application/pdf' && $file['size'] <= 2097152) { 
                    $target_dir = "assets/uploads/resumes/";
                    if(!is_dir($target_dir)) mkdir($target_dir, 0777, true);
                    
                    $new_name = uniqid("cv_", true) . ".pdf";
                    
                    if(move_uploaded_file($file['tmp_name'], $target_dir . $new_name)) {
                        $full_path = $target_dir . $new_name;
                        $stmt = $conn->prepare("INSERT INTO applications (job_id, name, email, cover_letter, resume_path, ip_address) VALUES (?, ?, ?, ?, ?, ?)");
                        $ip = $_SERVER['REMOTE_ADDR'];
                        $stmt->bind_param("isssss", $job_id, $name, $email, $cv_text, $full_path, $ip);
                        
                        if($stmt->execute()) {
                            $msg = "Application submitted successfully! Our HR team will review it shortly.";
                        } else {
                            $error_msg = "Database Error. Please try again.";
                        }
                    } else {
                        $error_msg = "File upload failed. Please try again.";
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
$page_title = "Careers at Nectra Digital | Remote Developer & Marketing Jobs in Lucknow";
$page_desc = "Join Nectra Digital — Lucknow's top software company. We're hiring Full Stack Developers, AI Engineers, SEO Specialists, and Digital Marketers. Remote & hybrid positions available. Apply now!";
$page_keys = "jobs at Nectra Digital, developer jobs Lucknow, remote developer jobs India, SEO specialist jobs, digital marketing careers, software engineer hiring, web developer vacancy Lucknow, AI engineer jobs";
include 'includes/header.php'; 

// Fetch jobs for schema
$jobs_sql = "SELECT * FROM careers WHERE status='Open' ORDER BY created_at DESC";
$jobs_result = $conn->query($jobs_sql);
$jobs_data = [];
if($jobs_result->num_rows > 0) {
    while($j = $jobs_result->fetch_assoc()) {
        $jobs_data[] = $j;
    }
}
?>

<?php foreach($jobs_data as $job): 
    // Map job type to Google's employmentType values
    $job_type_lower = strtolower($job['type'] ?? 'Remote');
    $emp_type = 'FULL_TIME';
    if(strpos($job_type_lower, 'freelance') !== false || strpos($job_type_lower, 'contract') !== false) {
        $emp_type = 'CONTRACTOR';
    } elseif(strpos($job_type_lower, 'part') !== false) {
        $emp_type = 'PART_TIME';
    } elseif(strpos($job_type_lower, 'intern') !== false) {
        $emp_type = 'INTERN';
    }
    
    // Determine if remote (separate from employment type)
    $is_remote = (strpos($job_type_lower, 'remote') !== false || strpos(strtolower($job['location']), 'remote') !== false);
    
    // Valid through: 60 days from posting
    $valid_through = date('Y-m-d', strtotime($job['created_at'] . ' +60 days'));
    
    // Google accepts HTML in JobPosting description
    $clean_desc = $job['description'];
    if(strlen(strip_tags($clean_desc)) < 50) $clean_desc = '<p>' . $job['position'] . ' at Nectra Digital. Required stack: ' . $job['stack'] . '. Location: ' . $job['location'] . '</p>';

    $schema = [
        "@context" => "https://schema.org/",
        "@type" => "JobPosting",
        "title" => $job['position'],
        "description" => $clean_desc,
        "identifier" => [
            "@type" => "PropertyValue",
            "name" => "Nectra Digital",
            "value" => "ND-JOB-" . $job['id']
        ],
        "datePosted" => date('Y-m-d', strtotime($job['created_at'])),
        "validThrough" => $valid_through . "T23:59:59+05:30",
        "employmentType" => $emp_type,
        "hiringOrganization" => [
            "@type" => "Organization",
            "name" => "Nectra Digital",
            "sameAs" => "https://www.nectradigital.com",
            "logo" => SITE_URL . "/assets/images/logo.png"
        ],
        "jobLocation" => [
            "@type" => "Place",
            "address" => [
                "@type" => "PostalAddress",
                "streetAddress" => "Lucknow",
                "addressLocality" => "Lucknow",
                "addressRegion" => "Uttar Pradesh",
                "postalCode" => "226001",
                "addressCountry" => "IN"
            ]
        ],
        "directApply" => true,
        "skills" => $job['stack'],
        "qualifications" => $job['qualification'] ?? ''
    ];
    
    // Add salary if available
    if(!empty($job['salary_range'])) {
        $schema["baseSalary"] = [
            "@type" => "MonetaryAmount",
            "currency" => "INR",
            "value" => [
                "@type" => "QuantitativeValue",
                "value" => $job['salary_range'],
                "unitText" => "MONTH"
            ]
        ];
    }
    
    // Add experience requirement
    if(!empty($job['experience'])) {
        $schema["experienceRequirements"] = $job['experience'];
    }
    
    if($is_remote) {
        $schema["jobLocationType"] = "TELECOMMUTE";
        $schema["applicantLocationRequirements"] = [
            "@type" => "Country",
            "name" => "India"
        ];
    }
?>
<script type="application/ld+json"><?php echo json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?></script>
<?php endforeach; ?>

<main>
    <header class="py-5 text-center" style="background: linear-gradient(to bottom, rgba(5,5,5,0.8) 0%, rgba(10,21,24,0.8) 100%); border-bottom: 1px solid #222;">
        <div class="container py-5">
            <div class="d-inline-block border border-neon rounded-pill px-3 py-1 mb-4 bg-dark">
                <small class="text-neon text-uppercase" style="letter-spacing: 2px;"><i class="fas fa-briefcase me-2"></i> We're Hiring</small>
            </div>
            <h1 class="display-4 fw-bold text-white mb-4">Careers at <span class="text-neon">Nectra Digital</span></h1>
            <p class="lead text-white-50 mx-auto" style="max-width: 700px;">
                Build cutting-edge software, solve real problems, and grow with India's fastest-rising tech company. Remote & hybrid roles available.
            </p>
        </div>
    </header>

    <div class="container mt-4">
        <?php if($msg) echo "<div class='alert alert-success bg-dark text-success border border-success'><i class='fas fa-check-circle me-2'></i>$msg</div>"; ?>
        <?php if($error_msg) echo "<div class='alert alert-danger bg-dark text-danger border border-danger'><i class='fas fa-exclamation-triangle me-2'></i>$error_msg</div>"; ?>
    </div>

    <section class="py-5">
        <div class="container">
            
            <?php if(count($jobs_data) > 0): ?>
            <div class="text-center mb-5">
                <h2 class="h4 text-white"><?php echo count($jobs_data); ?> Open Position<?php echo count($jobs_data) > 1 ? 's' : ''; ?></h2>
                <p class="text-white-50 small">Apply directly — no recruiters, no middlemen</p>
            </div>
            <?php endif; ?>

            <div class="row g-4">
                <?php
                if(count($jobs_data) > 0) {
                    foreach($jobs_data as $job) {
                        $location_icon = (strpos(strtolower($job['location']), 'remote') !== false) ? 'fas fa-wifi' : 'fas fa-map-marker-alt';
                        $type_badge = $job['type'] ?? 'Full Time';
                        $exp = !empty($job['experience']) ? $job['experience'] : '';
                        $salary = !empty($job['salary_range']) ? $job['salary_range'] : 'Not Disclosed';
                        $openings = !empty($job['openings']) ? $job['openings'] : 1;
                        $qualification = !empty($job['qualification']) ? $job['qualification'] : '';
                        
                        echo '
                        <div class="col-lg-10 mx-auto">
                            <div class="card border border-secondary p-4 shadow-lg" style="background-color: rgba(17,17,17,0.9) !important;">
                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                                    <div>
                                        <h3 class="text-white h4 mb-2">'.htmlspecialchars($job['position']).'</h3>
                                        <div class="d-flex flex-wrap gap-2 mb-3">';
                                        
                        echo '<span class="badge bg-info text-dark">'.htmlspecialchars($type_badge).'</span>';
                        if($exp) echo '<span class="badge bg-dark border border-secondary text-white-50">'.$exp.'</span>';
                        echo '<span class="badge bg-dark border border-secondary text-white-50"><i class="'.$location_icon.' me-1"></i>'.htmlspecialchars($job['location']).'</span>';
                        if($openings > 1) echo '<span class="badge bg-success">'.$openings.' Openings</span>';
                        
                        echo '</div>
                                        <div class="d-flex flex-wrap gap-3 text-white-50 small">
                                            <span><i class="fas fa-code text-neon me-1"></i> '.htmlspecialchars($job['stack']).'</span>';
                        if($salary !== 'Not Disclosed') echo '<span><i class="fas fa-indian-rupee-sign text-neon me-1"></i> ₹'.$salary.'/month</span>';
                        if($qualification) echo '<span><i class="fas fa-graduation-cap text-neon me-1"></i> '.htmlspecialchars($qualification).'</span>';
                        echo '<span><i class="far fa-calendar text-neon me-1"></i> '.date('M d, Y', strtotime($job['created_at'])).'</span>
                                        </div>
                                    </div>
                                    <button class="btn btn-nectra px-4" data-bs-toggle="modal" data-bs-target="#applyModal'.$job['id'].'">
                                        <i class="fas fa-paper-plane me-1"></i> APPLY NOW
                                    </button>
                                </div>
                                
                                <hr class="border-secondary my-3" style="opacity: 0.3;">
                                
                                <div class="text-white-50 small job-desc">
                                    '.$job['description'].'
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="applyModal'.$job['id'].'" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content border-secondary text-white" style="background-color: #0a0a0a !important; border: 1px solid #333;">
                                    <div class="modal-header border-secondary">
                                        <h5 class="modal-title">Apply for: <span class="text-info">'.htmlspecialchars($job['position']).'</span></h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <form method="POST" enctype="multipart/form-data">
                                            <input type="hidden" name="job_id" value="'.$job['id'].'">
                                            <input type="text" name="website_url" style="display:none;" autocomplete="off">
                                            
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="small text-secondary mb-1">Full Name *</label>
                                                    <input type="text" name="name" class="form-control bg-dark text-white border-secondary" placeholder="Your full name" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="small text-secondary mb-1">Email Address *</label>
                                                    <input type="email" name="email" class="form-control bg-dark text-white border-secondary" placeholder="your@email.com" required>
                                                </div>
                                                <div class="col-12">
                                                    <label class="small text-secondary mb-1">Cover Letter * (No links allowed)</label>
                                                    <textarea name="cover_letter" class="form-control bg-dark text-white border-secondary" rows="4" placeholder="Tell us about your experience and why you are interested..." required></textarea>
                                                </div>
                                                <div class="col-12">
                                                    <label class="small text-secondary mb-1">Resume * (PDF only, max 2MB)</label>
                                                    <input type="file" name="resume" class="form-control bg-dark text-white border-secondary" accept=".pdf" required>
                                                </div>
                                                <div class="col-12 mt-3">
                                                    <button type="submit" name="apply_job" class="btn btn-nectra w-100 py-3 fw-bold">
                                                        <i class="fas fa-paper-plane me-2"></i> Submit Application
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>';
                    }
                } else {
                    echo '<div class="col-12 text-center py-5">
                        <div class="mb-4"><i class="fas fa-satellite-dish fa-3x text-neon"></i></div>
                        <h3 class="text-white h5 mb-3">No Open Positions Right Now</h3>
                        <p class="text-white-50">We are always looking for talented people. Send your resume to <a href="mailto:contact@nectradigital.com" class="text-neon">contact@nectradigital.com</a></p>
                    </div>';
                }
                ?>
            </div>
        </div>
    </section>

    <section class="py-5" style="background: rgba(0,229,255,0.02); border-top: 1px solid rgba(0,229,255,0.1);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="h4 text-white mb-3">Why Work at Nectra Digital?</h2>
                    <div class="row g-4 mt-3">
                        <div class="col-md-4">
                            <div class="text-center p-3">
                                <i class="fas fa-wifi fa-2x text-neon mb-3"></i>
                                <h6 class="text-white">Remote First</h6>
                                <p class="text-white-50 small mb-0">Work from anywhere in India</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3">
                                <i class="fas fa-rocket fa-2x text-neon mb-3"></i>
                                <h6 class="text-white">Real Projects</h6>
                                <p class="text-white-50 small mb-0">SaaS, fintech, e-commerce — no filler work</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3">
                                <i class="fas fa-chart-line fa-2x text-neon mb-3"></i>
                                <h6 class="text-white">Growth Path</h6>
                                <p class="text-white-50 small mb-0">Clear progression + learning budget</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
.job-desc ul { padding-left: 20px; color: #ccc; margin-bottom: 15px; }
.job-desc li { margin-bottom: 5px; }
.job-desc p { margin-bottom: 15px; line-height: 1.6; }
.job-desc h4, .job-desc h5, .job-desc strong { color: #fff; display: block; margin-top: 20px; margin-bottom: 10px; font-weight: bold; }
.modal-backdrop.show { opacity: 0.8; }
</style>

<?php include 'includes/footer.php'; ?>
