<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit; }
include '../includes/db.php';

function upload_ad_image($file) {
    $target_dir = "../assets/uploads/";
    if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
    if ($file["size"] > 2097152) return ["error" => "File too large. Max 2MB."];
    $allowed_types = ['image/webp', 'image/svg+xml', 'image/png', 'image/jpeg', 'image/gif'];
    if (!in_array(mime_content_type($file["tmp_name"]), $allowed_types)) return ["error" => "Only WEBP, SVG, PNG, JPG, GIF allowed."];
    $ext = pathinfo($file["name"], PATHINFO_EXTENSION);
    $new_name = uniqid("ad_", true) . "." . $ext;
    $target_file = $target_dir . $new_name;
    if (move_uploaded_file($file["tmp_name"], $target_file)) return ["success" => "assets/uploads/" . $new_name];
    return ["error" => "Upload failed."];
}

// --- ACTIONS ---
if (isset($_GET['delete_post'])) {
    $conn->query("DELETE FROM blog_posts WHERE id=".intval($_GET['delete_post']));
    header("Location: dashboard.php?page=blog&msg=deleted"); exit;
}
if (isset($_GET['del_ad'])) {
    $conn->query("DELETE FROM ads WHERE id=".intval($_GET['del_ad']));
    header("Location: dashboard.php?page=ads&msg=ad_deleted"); exit;
}
if (isset($_GET['del_comment'])) {
    $conn->query("DELETE FROM comments WHERE id=".intval($_GET['del_comment']));
    header("Location: dashboard.php?page=comments&msg=com_deleted"); exit;
}
if (isset($_GET['approve_comment'])) {
    $conn->query("UPDATE comments SET status='approved' WHERE id=".intval($_GET['approve_comment']));
    header("Location: dashboard.php?page=comments&msg=approved"); exit;
}
if (isset($_GET['del_career'])) {
    $conn->query("DELETE FROM careers WHERE id=".intval($_GET['del_career']));
    header("Location: dashboard.php?page=careers&msg=job_deleted"); exit;
}
if (isset($_GET['del_hire'])) {
    $conn->query("DELETE FROM hire_requests WHERE id=".intval($_GET['del_hire']));
    header("Location: dashboard.php?page=hire_requests&msg=deleted"); exit;
}
if (isset($_POST['update_hire_status'])) {
    $req_id = intval($_POST['request_id']);
    $new_status = $conn->real_escape_string($_POST['status']);
    $conn->query("UPDATE hire_requests SET status='$new_status' WHERE id=$req_id");
    header("Location: dashboard.php?page=hire_requests&msg=status_updated"); exit;
}
if (isset($_GET['del_lead'])) {
    $conn->query("DELETE FROM leads WHERE id=".intval($_GET['del_lead']));
    header("Location: dashboard.php?page=leads&msg=deleted"); exit;
}

// Stats
$stats = [];
$stats['leads'] = $conn->query("SELECT COUNT(*) as c FROM leads")->fetch_assoc()['c'];
$stats['posts'] = $conn->query("SELECT COUNT(*) as c FROM blog_posts")->fetch_assoc()['c'];
$stats['comments'] = $conn->query("SELECT COUNT(*) as c FROM comments WHERE status='pending'")->fetch_assoc()['c'];
$stats['hire'] = $conn->query("SELECT COUNT(*) as c FROM hire_requests WHERE status='new'")->fetch_assoc()['c'];
$stats['careers'] = $conn->query("SELECT COUNT(*) as c FROM careers WHERE status='Open'")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Nectra Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body { background: #0d0d0d; color: #ccc; font-family: 'Inter', sans-serif; margin: 0; }
        
        .sidebar {
            min-height: 100vh; background: #080808; border-right: 1px solid #1a1a1a;
            width: 260px; position: fixed; top: 0; left: 0; z-index: 1000;
            display: flex; flex-direction: column; transition: transform 0.3s ease;
        }
        .sidebar-brand { padding: 20px; border-bottom: 1px solid #1a1a1a; }
        .sidebar-brand h4 { font-family: 'Orbitron', sans-serif; margin: 0; font-size: 1.1rem; }
        
        .sidebar .nav-link {
            color: #888; padding: 14px 20px; border-bottom: 1px solid #111;
            font-size: 0.85rem; transition: all 0.2s;
        }
        .sidebar .nav-link:hover { background: rgba(0, 229, 255, 0.05); color: #fff; }
        .sidebar .nav-link.active { background: rgba(0, 229, 255, 0.1); color: #00E5FF; border-left: 3px solid #00E5FF; font-weight: 600; }
        .sidebar .nav-link .badge { font-size: 0.65rem; }
        
        .content { margin-left: 260px; padding: 30px; min-height: 100vh; }
        
        .topbar { display: none; background: #080808; padding: 12px 20px; border-bottom: 1px solid #1a1a1a; position: sticky; top: 0; z-index: 999; }
        
        .stat-card { background: #141418; border: 1px solid #222; border-radius: 10px; padding: 20px; transition: all 0.3s; }
        .stat-card:hover { border-color: rgba(0, 229, 255, 0.3); transform: translateY(-2px); }
        .stat-card .stat-value { font-size: 1.8rem; font-weight: 700; color: #fff; }
        .stat-card .stat-label { font-size: 0.75rem; color: #666; text-transform: uppercase; letter-spacing: 1px; }
        
        .card { background: #141418; border: 1px solid #222; color: #fff; border-radius: 10px; }
        .table-dark { --bs-table-bg: transparent; --bs-table-border-color: #222; }
        .table-dark th { color: #666; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid #222; }
        .table-dark td { border-bottom: 1px solid #1a1a1a; vertical-align: middle; }
        
        .form-control, .form-select { background-color: #1a1a1a; border-color: #333; color: #fff; border-radius: 8px; }
        .form-control:focus, .form-select:focus { background-color: #222; color: #fff; border-color: #00E5FF; box-shadow: 0 0 0 2px rgba(0,229,255,0.1); }
        
        .btn-cyan { background: #00E5FF; color: #000; font-weight: 600; border: none; }
        .btn-cyan:hover { background: #00bcd4; color: #000; }
        
        img.ad-preview { object-fit: cover; border: 1px solid #333; border-radius: 4px; }
        
        @media (max-width: 991px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .content { margin-left: 0; padding: 20px 15px; }
            .topbar { display: flex; align-items: center; justify-content: space-between; }
            .overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 999; }
            .overlay.show { display: block; }
        }
    </style>
</head>
<body>

<!-- Mobile Topbar -->
<div class="topbar">
    <button class="btn btn-sm btn-outline-light" id="sidebarToggle"><i class="fas fa-bars"></i></button>
    <span class="text-white fw-bold" style="font-family: 'Orbitron', sans-serif; font-size: 0.9rem;">NECTRA<span style="color:#00E5FF;">OS</span></span>
    <a href="logout.php" class="btn btn-sm btn-outline-danger"><i class="fas fa-power-off"></i></a>
</div>
<div class="overlay" id="overlay"></div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <h4><span class="text-white">NECTRA</span><span style="color: #00E5FF;">OS</span></h4>
        <small class="text-white-50" style="font-size: 0.7rem;">Admin Panel v2.0</small>
    </div>
    <a href="?page=overview" class="nav-link <?php echo (!isset($_GET['page']) || $_GET['page']=='overview')?'active':''; ?>"><i class="fas fa-chart-line me-2"></i> Overview</a>
    <a href="?page=leads" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page']=='leads')?'active':''; ?>"><i class="fas fa-inbox me-2"></i> Leads <?php if($stats['leads'] > 0) echo "<span class='badge bg-success float-end'>{$stats['leads']}</span>"; ?></a>
    <a href="?page=hire_requests" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page']=='hire_requests')?'active':''; ?>"><i class="fas fa-user-tie me-2"></i> Hire Requests <?php if($stats['hire'] > 0) echo "<span class='badge bg-warning float-end'>{$stats['hire']}</span>"; ?></a>
    <a href="?page=blog" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page']=='blog')?'active':''; ?>"><i class="fas fa-edit me-2"></i> Blog Posts</a>
    <a href="?page=comments" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page']=='comments')?'active':''; ?>"><i class="fas fa-comments me-2"></i> Comments <?php if($stats['comments'] > 0) echo "<span class='badge bg-info float-end'>{$stats['comments']}</span>"; ?></a>
    <a href="?page=ads" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page']=='ads')?'active':''; ?>"><i class="fas fa-ad me-2"></i> Ads</a>
    <a href="?page=careers" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page']=='careers')?'active':''; ?>"><i class="fas fa-briefcase me-2"></i> Careers</a>
    <a href="send_notification.php" class="nav-link"><i class="fas fa-bell me-2"></i> Push Notify</a>
    <a href="logout.php" class="nav-link mt-auto text-danger border-top border-secondary"><i class="fas fa-power-off me-2"></i> Logout</a>
</div>

<div class="content">
    <?php
    $page = isset($_GET['page']) ? $_GET['page'] : 'overview';

    // ==========================================
    // OVERVIEW / STATS
    // ==========================================
    if ($page == 'overview') {
        echo '<h4 class="mb-4 text-white">Dashboard Overview</h4>';
        echo '<div class="row g-3 mb-4">';
        echo '<div class="col-6 col-md-3"><div class="stat-card"><div class="stat-value" style="color:#00E5FF;">'.$stats['leads'].'</div><div class="stat-label">Total Leads</div></div></div>';
        echo '<div class="col-6 col-md-3"><div class="stat-card"><div class="stat-value" style="color:#ffc107;">'.$stats['hire'].'</div><div class="stat-label">New Hire Requests</div></div></div>';
        echo '<div class="col-6 col-md-3"><div class="stat-card"><div class="stat-value" style="color:#4caf50;">'.$stats['posts'].'</div><div class="stat-label">Blog Posts</div></div></div>';
        echo '<div class="col-6 col-md-3"><div class="stat-card"><div class="stat-value" style="color:#ff5722;">'.$stats['comments'].'</div><div class="stat-label">Pending Comments</div></div></div>';
        echo '</div>';

        // Recent leads
        echo '<div class="card p-4 mb-4"><h6 class="text-white-50 mb-3"><i class="fas fa-inbox me-2"></i>Recent Leads</h6>';
        $recent = $conn->query("SELECT * FROM leads ORDER BY created_at DESC LIMIT 5");
        if($recent->num_rows > 0) {
            echo '<div class="table-responsive"><table class="table table-dark table-sm"><thead><tr><th>Name</th><th>Email</th><th>Service</th><th>Date</th></tr></thead><tbody>';
            while($r = $recent->fetch_assoc()) {
                echo "<tr><td class='text-white'>".htmlspecialchars($r['name'])."</td><td class='text-info small'>".htmlspecialchars($r['email'])."</td><td><span class='badge bg-secondary'>".htmlspecialchars($r['service'])."</span></td><td class='text-white-50 small'>".date('M d', strtotime($r['created_at']))."</td></tr>";
            }
            echo '</tbody></table></div>';
        } else { echo '<p class="text-white-50 small mb-0">No leads yet.</p>'; }
        echo '</div>';

        // Recent hire requests
        echo '<div class="card p-4"><h6 class="text-white-50 mb-3"><i class="fas fa-user-tie me-2"></i>Recent Hire Requests</h6>';
        $recent_hr = $conn->query("SELECT * FROM hire_requests ORDER BY created_at DESC LIMIT 5");
        if($recent_hr->num_rows > 0) {
            echo '<div class="table-responsive"><table class="table table-dark table-sm"><thead><tr><th>Client</th><th>Service</th><th>Budget</th><th>Status</th></tr></thead><tbody>';
            while($r = $recent_hr->fetch_assoc()) {
                $st_class = $r['status'] == 'new' ? 'bg-warning' : ($r['status'] == 'contacted' ? 'bg-info' : 'bg-secondary');
                echo "<tr><td class='text-white'>".htmlspecialchars($r['full_name'])."</td><td>".htmlspecialchars($r['service_needed'])."</td><td class='small'>".htmlspecialchars($r['budget'])."</td><td><span class='badge $st_class'>".ucfirst($r['status'])."</span></td></tr>";
            }
            echo '</tbody></table></div>';
        } else { echo '<p class="text-white-50 small mb-0">No hire requests yet.</p>'; }
        echo '</div>';
    }

    // ==========================================
    // BLOG MANAGEMENT
    // ==========================================
    elseif ($page == 'blog') {
        echo '<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <h4 class="text-white mb-0"><i class="fas fa-edit text-info me-2"></i>Blog Posts</h4>
                <a href="create_post.php" class="btn btn-cyan btn-sm"><i class="fas fa-plus me-1"></i> New Post</a>
              </div>';
        if(isset($_GET['msg']) && $_GET['msg'] == 'deleted') echo "<div class='alert alert-danger py-2'>Post deleted.</div>";

        $sql = "SELECT * FROM blog_posts ORDER BY created_at DESC";
        $result = $conn->query($sql);

        echo '<div class="table-responsive"><table class="table table-dark align-middle">
                <thead><tr><th>Date</th><th>Title</th><th>Category</th><th>Actions</th></tr></thead><tbody>';
        while($row = $result->fetch_assoc()) {
            $title = htmlspecialchars_decode($row['title'], ENT_QUOTES);
            echo "<tr>
                    <td class='text-white-50 small'>".date('M d, Y', strtotime($row['created_at']))."</td>
                    <td class='fw-bold text-white' style='max-width:300px;'>".htmlspecialchars($title)."</td>
                    <td><span class='badge bg-secondary'>".htmlspecialchars($row['category'])."</span></td>
                    <td>
                        <a href='edit_post.php?id={$row['id']}' class='btn btn-sm btn-outline-warning me-1' title='Edit'><i class='fas fa-pen'></i></a>
                        <a href='?page=blog&delete_post={$row['id']}' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"Delete this post?\")' title='Delete'><i class='fas fa-trash'></i></a>
                    </td>
                  </tr>";
        }
        echo '</tbody></table></div>';
    }

    // ==========================================
    // ADS MANAGEMENT
    // ==========================================
    elseif ($page == 'ads') {
        if(isset($_POST['save_ad'])){
            $title = clean_input($_POST['title']);
            $type = $_POST['type'];
            $placement = $_POST['placement'];
            $code = $conn->real_escape_string($_POST['ad_code'] ?? '');
            $link = clean_input($_POST['link'] ?? '');
            $img_path = "";

            if ($type == 'image' && isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $upload = upload_ad_image($_FILES['image']);
                if (isset($upload['error'])) {
                    echo "<div class='alert alert-danger'>{$upload['error']}</div>";
                } else {
                    $img_path = $upload['success'];
                }
            }

            if (!isset($upload) || isset($upload['success'])) {
                $conn->query("INSERT INTO ads (title, type, placement, image_path, ad_code, link) VALUES ('$title', '$type', '$placement', '$img_path', '$code', '$link')");
                echo "<div class='alert alert-success py-2'>Ad created successfully!</div>";
            }
        }
        if(isset($_GET['msg']) && $_GET['msg'] == 'ad_deleted') echo "<div class='alert alert-danger py-2'>Ad deleted.</div>";

        echo "<h4 class='text-white mb-4'><i class='fas fa-ad text-info me-2'></i>Ad Manager</h4>";
        
        echo "<div class='card p-4 mb-4'>
            <h6 class='text-white mb-3'>Create New Ad</h6>
            <form method='POST' enctype='multipart/form-data'>
                <div class='row g-3'>
                    <div class='col-md-4'><input type='text' name='title' class='form-control' placeholder='Ad Name' required></div>
                    <div class='col-md-4'><select name='placement' class='form-select'><option value='header'>Top Banner</option><option value='sidebar'>Sidebar</option><option value='content'>In-Article</option></select></div>
                    <div class='col-md-4'><select name='type' class='form-select' id='adType'><option value='image'>Image Ad</option><option value='code'>HTML/Code Ad</option></select></div>
                    <div class='col-md-6 ad-image-group'><input type='file' name='image' class='form-control'></div>
                    <div class='col-md-6 ad-image-group'><input type='text' name='link' class='form-control' placeholder='Click URL'></div>
                    <div class='col-12 ad-code-group' style='display:none;'><textarea name='ad_code' class='form-control' rows='3' placeholder='Paste ad HTML/JS code here'></textarea></div>
                    <div class='col-12'><button type='submit' name='save_ad' class='btn btn-cyan w-100'>Create Ad</button></div>
                </div>
            </form>
        </div>";

        $res = $conn->query("SELECT * FROM ads ORDER BY id DESC");
        echo "<div class='table-responsive'><table class='table table-dark'><thead><tr><th>Preview</th><th>Name</th><th>Type</th><th>Placement</th><th>Action</th></tr></thead><tbody>";
        while($row = $res->fetch_assoc()){
            $preview = ($row['type'] == 'image' && !empty($row['image_path'])) ? "<img src='../{$row['image_path']}' width='50' height='50' class='ad-preview'>" : "<i class='fas fa-code fa-lg text-info'></i>";
            echo "<tr><td>$preview</td><td class='text-white'>".htmlspecialchars($row['title'])."</td><td><span class='badge bg-secondary'>{$row['type']}</span></td><td><span class='badge bg-info'>{$row['placement']}</span></td><td><a href='?page=ads&del_ad={$row['id']}' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"Delete?\")'>Delete</a></td></tr>";
        }
        echo "</tbody></table></div>";
    }

    // ==========================================
    // COMMENTS MANAGEMENT
    // ==========================================
    elseif ($page == 'comments') {
        if(isset($_GET['msg'])) {
            if($_GET['msg'] == 'approved') echo "<div class='alert alert-success py-2'>Comment approved.</div>";
            if($_GET['msg'] == 'com_deleted') echo "<div class='alert alert-danger py-2'>Comment deleted.</div>";
        }
        echo "<h4 class='text-white mb-4'><i class='fas fa-comments text-info me-2'></i>Comments</h4>";
        $res = $conn->query("SELECT c.*, bp.title as post_title FROM comments c LEFT JOIN blog_posts bp ON c.post_id = bp.id ORDER BY c.created_at DESC");
        echo "<div class='table-responsive'><table class='table table-dark'><thead><tr><th>User</th><th>Comment</th><th>Post</th><th>Status</th><th>Action</th></tr></thead><tbody>";
        while($row = $res->fetch_assoc()){
            $status_badge = $row['status'] == 'pending' ? "<span class='badge bg-warning'>Pending</span>" : "<span class='badge bg-success'>Approved</span>";
            $approve_btn = $row['status'] == 'pending' ? "<a href='?page=comments&approve_comment={$row['id']}' class='btn btn-sm btn-outline-success me-1' title='Approve'><i class='fas fa-check'></i></a>" : "";
            echo "<tr>
                <td><strong class='text-white'>".htmlspecialchars($row['name'])."</strong><br><small class='text-white-50'>".htmlspecialchars($row['email'])."</small></td>
                <td><div style='max-width:250px; max-height:60px; overflow-y:auto;' class='small text-white-50'>".htmlspecialchars($row['comment'])."</div></td>
                <td class='small text-info'>".htmlspecialchars($row['post_title'] ?? 'Unknown')."</td>
                <td>$status_badge</td>
                <td>$approve_btn<a href='?page=comments&del_comment={$row['id']}' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"Delete?\")'><i class='fas fa-trash'></i></a></td>
            </tr>";
        }
        echo "</tbody></table></div>";
    }

    // ==========================================
    // CAREERS & APPLICATIONS
    // ==========================================
    elseif ($page == 'careers') {
        if(isset($_GET['msg']) && $_GET['msg'] == 'job_deleted') echo "<div class='alert alert-danger py-2'>Position closed.</div>";

        echo '<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <h4 class="text-white mb-0"><i class="fas fa-briefcase text-info me-2"></i>Careers</h4>
                <a href="create_job.php" class="btn btn-cyan btn-sm"><i class="fas fa-plus me-1"></i> New Position</a>
              </div>';

        $res = $conn->query("SELECT * FROM careers ORDER BY created_at DESC");
        echo "<div class='table-responsive'><table class='table table-dark'><thead><tr><th>Position</th><th>Stack</th><th>Location</th><th>Status</th><th>Action</th></tr></thead><tbody>";
        while($row = $res->fetch_assoc()){
            $st = $row['status'] == 'Open' ? "<span class='badge bg-success'>Open</span>" : "<span class='badge bg-secondary'>Closed</span>";
            echo "<tr>
                <td class='text-white fw-bold'>".htmlspecialchars($row['position'])."</td>
                <td>".htmlspecialchars($row['stack'])."</td>
                <td class='small'>".htmlspecialchars($row['location'])."</td>
                <td>$st</td>
                <td><a href='?page=careers&del_career={$row['id']}' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"Delete position?\")'>Delete</a></td>
            </tr>";
        }
        echo "</tbody></table></div>";

        // Applications
        echo '<h5 class="mt-5 text-white"><i class="fas fa-file-pdf me-2 text-warning"></i>Applications</h5><hr class="border-secondary">';
        $app_res = $conn->query("SELECT a.*, c.position FROM applications a LEFT JOIN careers c ON a.job_id = c.id ORDER BY a.created_at DESC");
        echo "<div class='table-responsive'><table class='table table-dark'><thead><tr><th>Candidate</th><th>Position</th><th>Cover Letter</th><th>Resume</th><th>Date</th></tr></thead><tbody>";
        if($app_res->num_rows > 0){
            while($app = $app_res->fetch_assoc()){
                echo "<tr>
                    <td><strong class='text-white'>".htmlspecialchars($app['name'])."</strong><br><small class='text-info'>".htmlspecialchars($app['email'])."</small></td>
                    <td>".htmlspecialchars($app['position'] ?? 'N/A')."</td>
                    <td><div style='max-height:60px; overflow-y:auto; font-size:0.8rem;' class='text-white-50'>".htmlspecialchars($app['cover_letter'])."</div></td>
                    <td><a href='../{$app['resume_path']}' target='_blank' class='btn btn-sm btn-outline-success'><i class='fas fa-download'></i> PDF</a></td>
                    <td class='small text-white-50'>".date('M d', strtotime($app['created_at']))."</td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='5' class='text-center text-white-50 py-3'>No applications received.</td></tr>";
        }
        echo "</tbody></table></div>";
    }

    // ==========================================
    // HIRE REQUESTS
    // ==========================================
    elseif ($page == 'hire_requests') {
        echo '<h4 class="text-white mb-4"><i class="fas fa-user-tie text-info me-2"></i>Hire Expert Requests</h4>';
              
        if(isset($_GET['msg'])) {
            if($_GET['msg'] == 'deleted') echo "<div class='alert alert-danger py-2'>Request deleted.</div>";
            if($_GET['msg'] == 'status_updated') echo "<div class='alert alert-success py-2'>Status updated.</div>";
        }

        $sql = "SELECT * FROM hire_requests ORDER BY created_at DESC";
        $result = $conn->query($sql);

        echo '<div class="table-responsive"><table class="table table-dark align-middle">
                <thead><tr><th>Date</th><th>Client</th><th>Service & Budget</th><th>Status</th><th>Actions</th></tr></thead><tbody>';
        
        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $st_class = $row['status'] == 'new' ? 'bg-warning' : ($row['status'] == 'contacted' ? 'bg-info' : 'bg-secondary');
                echo "<tr>
                        <td><small class='text-white-50'>".date('M d, Y', strtotime($row['created_at']))."</small></td>
                        <td>
                            <strong class='text-white'>".htmlspecialchars($row['full_name'])."</strong><br>
                            <a href='mailto:".htmlspecialchars($row['email'])."' class='small text-info text-decoration-none'>".htmlspecialchars($row['email'])."</a><br>
                            <a href='https://wa.me/".preg_replace('/[^0-9]/', '', $row['phone'])."' target='_blank' class='small text-success text-decoration-none'><i class='fab fa-whatsapp'></i> ".htmlspecialchars($row['phone'])."</a>
                        </td>
                        <td>
                            <span class='badge bg-secondary'>".htmlspecialchars($row['service_needed'])."</span><br>
                            <small class='text-white-50'>".htmlspecialchars($row['budget'])."</small>
                        </td>
                        <td>
                            <form method='POST' class='d-inline-block'>
                                <input type='hidden' name='request_id' value='{$row['id']}'>
                                <input type='hidden' name='update_hire_status' value='1'>
                                <select name='status' class='form-select form-select-sm d-inline-block w-auto' style='font-size:0.75rem;' onchange='this.form.submit()'>
                                    <option value='new' ".($row['status'] == 'new' ? 'selected' : '').">New</option>
                                    <option value='contacted' ".($row['status'] == 'contacted' ? 'selected' : '').">Contacted</option>
                                    <option value='closed' ".($row['status'] == 'closed' ? 'selected' : '').">Closed</option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <button class='btn btn-sm btn-outline-info' onclick='toggleDetails({$row['id']})' title='View Details'><i class='fas fa-eye'></i></button>
                            <a href='?page=hire_requests&del_hire={$row['id']}' class='btn btn-sm btn-outline-danger ms-1' onclick='return confirm(\"Delete?\")'><i class='fas fa-trash'></i></a>
                        </td>
                      </tr>
                      <tr id='details-{$row['id']}' style='display:none;'>
                          <td colspan='5' class='p-3' style='background:#1a1a1a; border-left: 3px solid #00E5FF;'>
                              <h6 class='text-info mb-2 small'>Project Details:</h6>
                              <p class='mb-0 text-white-50 small' style='white-space: pre-wrap;'>".htmlspecialchars($row['project_details'])."</p>
                          </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='5' class='text-center py-4 text-white-50'>No hire requests found.</td></tr>";
        }
        echo '</tbody></table></div>';
    }

    // ==========================================
    // LEADS (DEFAULT)
    // ==========================================
    elseif ($page == 'leads') {
        if(isset($_GET['msg']) && $_GET['msg'] == 'deleted') echo "<div class='alert alert-danger py-2'>Lead deleted.</div>";
        echo '<h4 class="text-white mb-4"><i class="fas fa-inbox text-info me-2"></i>Contact Form Leads</h4>';
        $sql = "SELECT * FROM leads ORDER BY created_at DESC";
        $result = $conn->query($sql);
        echo '<div class="table-responsive"><table class="table table-dark align-middle"><thead><tr><th>Date</th><th>Status</th><th>Contact</th><th>Service</th><th>Message</th><th>Action</th></tr></thead><tbody>';
        while($row = $result->fetch_assoc()){
            $spam = $row['is_spam'] ? "<span class='badge bg-danger'>Spam</span>" : "<span class='badge bg-success'>Valid</span>";
            echo "<tr>
                <td class='small text-white-50'>".date('M d', strtotime($row['created_at']))."</td>
                <td>$spam</td>
                <td><strong class='text-white'>".htmlspecialchars($row['name'])."</strong><br><small class='text-info'>".htmlspecialchars($row['email'])."</small></td>
                <td><span class='badge bg-secondary'>".htmlspecialchars($row['service'])."</span><br><small class='text-white-50'>".htmlspecialchars($row['budget'])."</small></td>
                <td><div style='max-width:200px; max-height:60px; overflow-y:auto;' class='small text-white-50'>".htmlspecialchars($row['message'])."</div></td>
                <td><a href='?page=leads&del_lead={$row['id']}' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"Delete lead?\")' title='Delete'><i class='fas fa-trash'></i></a></td>
            </tr>";
        }
        echo '</tbody></table></div>';
    }
    ?>
</div>

<script>
function toggleDetails(id) {
    var row = document.getElementById('details-' + id);
    row.style.display = (row.style.display === 'none' || row.style.display === '') ? 'table-row' : 'none';
}

// Ad type toggle
document.addEventListener('DOMContentLoaded', function() {
    var adType = document.getElementById('adType');
    if(adType) {
        adType.addEventListener('change', function() {
            var imgGroups = document.querySelectorAll('.ad-image-group');
            var codeGroups = document.querySelectorAll('.ad-code-group');
            if(this.value === 'code') {
                imgGroups.forEach(function(el) { el.style.display = 'none'; });
                codeGroups.forEach(function(el) { el.style.display = 'block'; });
            } else {
                imgGroups.forEach(function(el) { el.style.display = 'block'; });
                codeGroups.forEach(function(el) { el.style.display = 'none'; });
            }
        });
    }

    // Mobile sidebar toggle
    var toggle = document.getElementById('sidebarToggle');
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('overlay');
    if(toggle) {
        toggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        });
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    }
});
</script>

</body>
</html>
