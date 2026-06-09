<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit; }
include '../includes/db.php';

$growthMsg = null;
$growthStats = ['ready' => false];
if (is_file(__DIR__ . '/includes/admin-growth.php')) {
    require_once __DIR__ . '/includes/admin-growth.php';
    $pageForPost = $_GET['page'] ?? 'home';
    $growthMsg = admin_handle_growth_post($pageForPost);
    $growthStats = admin_growth_stats();
}

// --- HELPER: IMAGE UPLOAD FUNCTION ---
function upload_ad_image($file) {
    $target_dir = "../assets/uploads/";
    if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }

    if ($file["size"] > 512000) return ["error" => "Error: File too large. Max 500KB."];

    $allowed_types = ['image/webp', 'image/svg+xml', 'image/png', 'image/jpeg'];
    if (!in_array(mime_content_type($file["tmp_name"]), $allowed_types)) return ["error" => "Error: Only WEBP, SVG, PNG, JPG allowed."];

    $ext = pathinfo($file["name"], PATHINFO_EXTENSION);
    $new_name = uniqid("ad_", true) . "." . $ext;
    $target_file = $target_dir . $new_name;

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ["success" => "assets/uploads/" . $new_name];
    }
    return ["error" => "Upload failed."];
}

// --- GLOBAL ACTIONS ---
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

// --- NEW HIRE REQUESTS ACTIONS ---
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Nectra Admin Control</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #111; color: #ccc; }
        .sidebar { min-height: 100vh; background: #050505; border-right: 1px solid #333; width: 250px; position: fixed; top: 0; left: 0; }
        .content { margin-left: 250px; padding: 30px; }
        .nav-link { color: #aaa; padding: 15px; border-bottom: 1px solid #222; }
        .nav-link:hover, .nav-link.active { background: #00E5FF; color: #000; font-weight: bold; }
        .card { background: #1a1a1a; border: 1px solid #333; color: #fff; }
        .table-dark { --bs-table-bg: #1a1a1a; }
        .form-control, .form-select { background-color: #222; border-color: #444; color: #fff; }
        .form-control:focus, .form-select:focus { background-color: #333; color: #fff; border-color: #00E5FF; }
        img.ad-preview { object-fit: cover; border: 1px solid #444; }
        .stat-box { background: #0d1117; border: 1px solid #333; border-radius: 8px; padding: 1rem; text-align: center; }
        .stat-val { font-size: 1.75rem; font-weight: bold; color: #00E5FF; }
        .stat-lbl { font-size: 0.75rem; color: #888; text-transform: uppercase; }
        .section-title { color: #666; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; padding: 10px 15px 5px; }
    </style>
</head>
<body>

<div class="sidebar d-flex flex-column">
    <div class="p-4 text-center border-bottom border-secondary">
        <h4 class="text-white m-0">NECTRA<span class="text-info">OS</span></h4>
        <small class="text-muted">Admin Control Center</small>
    </div>

    <div class="section-title">Overview</div>
    <a href="?page=home" class="nav-link <?php echo (!isset($_GET['page']) || $_GET['page']=='home')?'active':''; ?>"><i class="fas fa-home me-2"></i> Dashboard Home</a>

    <div class="section-title">Growth & SEO</div>
    <a href="?page=cities" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page']=='cities')?'active':''; ?>"><i class="fas fa-map-marker-alt me-2"></i> Cities</a>
    <a href="?page=seo" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page']=='seo')?'active':''; ?>"><i class="fas fa-search-plus me-2"></i> Auto Indexing</a>
    <a href="growth/services.php" class="nav-link"><i class="fas fa-cogs me-2"></i> Services</a>
    <a href="growth/generate.php" class="nav-link"><i class="fas fa-magic me-2"></i> Generate Pages</a>
    <a href="growth/landing-pages.php" class="nav-link"><i class="fas fa-file-alt me-2"></i> Landing Pages</a>
    <a href="growth/leads.php" class="nav-link"><i class="fas fa-user-plus me-2"></i> CRM Leads</a>
    <a href="growth/settings.php" class="nav-link"><i class="fas fa-sliders-h me-2"></i> Growth Settings</a>

    <div class="section-title">Inbound</div>
    <a href="?page=leads" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page']=='leads')?'active':''; ?>"><i class="fas fa-satellite-dish me-2"></i> Contact Leads</a>
    <a href="?page=hire_requests" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page']=='hire_requests')?'active':''; ?>"><i class="fas fa-user-tie me-2"></i> Hire Requests</a>

    <div class="section-title">Content</div>
    <a href="?page=blog" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page']=='blog')?'active':''; ?>"><i class="fas fa-edit me-2"></i> Blog Ops</a>
    <a href="?page=comments" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page']=='comments')?'active':''; ?>"><i class="fas fa-comments me-2"></i> Comments</a>

    <div class="section-title">Monetization & HR</div>
    <a href="?page=ads" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page']=='ads')?'active':''; ?>"><i class="fas fa-ad me-2"></i> Ad Engine</a>
    <a href="?page=careers" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page']=='careers')?'active':''; ?>"><i class="fas fa-briefcase me-2"></i> Careers</a>

    <a href="<?php echo defined('SITE_URL') ? SITE_URL : 'https://www.nectradigital.com'; ?>" target="_blank" class="nav-link"><i class="fas fa-external-link-alt me-2"></i> View Site</a>
    <a href="logout.php" class="nav-link mt-auto text-danger"><i class="fas fa-power-off me-2"></i> Terminate</a>
</div>

<div class="content">
    <?php
    if ($growthMsg) echo "<div class='alert alert-success alert-dismissible fade show'>".htmlspecialchars($growthMsg)."<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    if (!empty($growthStats['error'])) echo "<div class='alert alert-warning'>Growth Engine: ".htmlspecialchars($growthStats['error'])."</div>";
    if (!$growthStats['ready']) echo "<div class='alert alert-warning'><strong>Growth DB not ready.</strong> Run <a href='../database/migrate.php' target='_blank' class='alert-link'>database/migrate.php</a> or import SQL via phpMyAdmin to enable Cities & Auto Indexing.</div>";

    $page = isset($_GET['page']) ? $_GET['page'] : 'home';

    // ==========================================
    // HOME — Unified Dashboard Overview
    // ==========================================
    if ($page == 'home') {
        $leadCount = $conn->query("SELECT COUNT(*) AS c FROM leads")->fetch_assoc()['c'] ?? 0;
        $hireCount = $conn->query("SELECT COUNT(*) AS c FROM hire_requests WHERE status='new'")->fetch_assoc()['c'] ?? 0;
        echo '<h2 class="mb-4"><i class="fas fa-tachometer-alt text-info"></i> Command Center</h2>';
        echo '<div class="row g-3 mb-4">';
        echo '<div class="col-6 col-md-3"><div class="stat-box"><div class="stat-val">'.$leadCount.'</div><div class="stat-lbl">Contact Leads</div></div></div>';
        echo '<div class="col-6 col-md-3"><div class="stat-box"><div class="stat-val">'.$hireCount.'</div><div class="stat-lbl">New Hire Requests</div></div></div>';
        echo '<div class="col-6 col-md-3"><div class="stat-box"><div class="stat-val">'.number_format($growthStats['cities']).'</div><div class="stat-lbl">Active Cities</div></div></div>';
        echo '<div class="col-6 col-md-3"><div class="stat-box"><div class="stat-val">'.number_format($growthStats['pages']).'</div><div class="stat-lbl">Landing Pages</div></div></div>';
        echo '</div>';

        echo '<div class="row g-4">';
        echo '<div class="col-lg-6"><div class="card p-4"><h5 class="text-info mb-3"><i class="fas fa-map-marker-alt"></i> Quick Add City</h5>';
        echo '<form method="POST" action="?page=cities"><input type="hidden" name="growth_action" value="add_city">';
        echo '<div class="row g-2"><div class="col-md-6"><input type="text" name="name" class="form-control" placeholder="City Name *" required></div>';
        echo '<div class="col-md-6"><input type="text" name="state" class="form-control" placeholder="State"></div>';
        echo '<div class="col-md-6"><input type="text" name="country" class="form-control" value="India" placeholder="Country"></div>';
        echo '<div class="col-md-6"><input type="number" name="population" class="form-control" placeholder="Population"></div>';
        echo '<div class="col-12"><button type="submit" class="btn btn-info w-100">Add City</button></div></div></form>';
        echo '<p class="small text-muted mt-2 mb-0"><a href="?page=cities">Manage all cities →</a> · <a href="growth/cities.php?action=import">Bulk import</a></p></div></div>';

        echo '<div class="col-lg-6"><div class="card p-4"><h5 class="text-info mb-3"><i class="fas fa-rocket"></i> Auto Indexing Status</h5>';
        echo '<div class="d-flex justify-content-between mb-2"><span class="text-white-50">Indexed</span><strong class="text-success">'.number_format($growthStats['indexed']).'</strong></div>';
        echo '<div class="d-flex justify-content-between mb-2"><span class="text-white-50">Pending</span><strong class="text-warning">'.number_format($growthStats['pending_index']).'</strong></div>';
        echo '<div class="d-flex justify-content-between mb-3"><span class="text-white-50">Queue</span><strong>'.number_format($growthStats['queue_pending']).'</strong></div>';
        echo '<form method="POST" action="?page=seo" class="d-grid gap-2"><input type="hidden" name="growth_action" value="queue_and_process">';
        echo '<button type="submit" class="btn btn-success"><i class="fas fa-bolt"></i> Queue & Submit (IndexNow + Bing + DDG)</button></form>';
        echo '<p class="small text-muted mt-2 mb-0"><a href="?page=seo">Full indexing panel →</a></p></div></div>';
        echo '</div>';

        if ($growthStats['ready']) {
            $pct = $growthStats['potential'] > 0 ? min(100, round(($growthStats['pages'] / $growthStats['potential']) * 100)) : 0;
            echo '<div class="card p-4 mt-4"><h5 class="text-info mb-3">Programmatic SEO Matrix</h5>';
            echo '<p class="text-white-50 small">Services × Cities × Industries = '.$growthStats['services'].' × '.$growthStats['cities'].' × '.max(1,$growthStats['industries']+1).' = <strong>'.number_format($growthStats['potential']).'</strong> possible pages</p>';
            echo '<div class="progress mb-2" style="height:10px;background:#333;"><div class="progress-bar bg-info" style="width:'.$pct.'%"></div></div>';
            echo '<p class="small text-muted">'.$pct.'% generated ('.number_format($growthStats['pages']).' / '.number_format($growthStats['potential']).')</p>';
            echo '<a href="growth/generate.php" class="btn btn-sm btn-info me-2">Generate Pages</a>';
            echo '<a href="growth/services.php" class="btn btn-sm btn-outline-secondary me-2">Services</a>';
            echo '<a href="growth/industries.php" class="btn btn-sm btn-outline-secondary">Industries</a></div>';
        }
    }

    // ==========================================
    // CITIES MANAGEMENT
    // ==========================================
    elseif ($page == 'cities') {
        $cities = admin_growth_cities();
        echo '<div class="d-flex justify-content-between align-items-center mb-4"><h2><i class="fas fa-map-marker-alt text-info"></i> City Manager</h2>';
        echo '<div><a href="growth/cities.php?action=import" class="btn btn-outline-secondary me-2"><i class="fas fa-file-import"></i> Bulk Import</a>';
        echo '<a href="growth/cities.php?action=add" class="btn btn-info"><i class="fas fa-plus"></i> Advanced Add</a></div></div>';

        echo '<div class="card p-4 mb-4"><h5>Add New City</h5><form method="POST"><input type="hidden" name="growth_action" value="add_city">';
        echo '<div class="row g-3"><div class="col-md-3"><input type="text" name="name" class="form-control" placeholder="City Name *" required></div>';
        echo '<div class="col-md-2"><input type="text" name="slug" class="form-control" placeholder="Slug (auto)"></div>';
        echo '<div class="col-md-2"><input type="text" name="state" class="form-control" placeholder="State"></div>';
        echo '<div class="col-md-2"><input type="text" name="country" class="form-control" value="India"></div>';
        echo '<div class="col-md-2"><input type="number" name="population" class="form-control" placeholder="Population"></div>';
        echo '<div class="col-md-1"><button type="submit" class="btn btn-success w-100">Add</button></div></div></form></div>';

        echo '<div class="card p-3"><div class="table-responsive"><table class="table table-dark table-hover table-sm mb-0"><thead><tr><th>City</th><th>State</th><th>Population</th><th>Status</th><th>Actions</th></tr></thead><tbody>';
        foreach ($cities as $c) {
            echo '<tr><td><strong>'.htmlspecialchars($c['name']).'</strong><br><code class="small">'.htmlspecialchars($c['slug']).'</code></td>';
            echo '<td>'.htmlspecialchars($c['state']).'</td><td>'.number_format($c['population']).'</td><td>'.$c['status'].'</td><td>';
            echo '<a href="growth/cities.php?action=edit&id='.$c['id'].'" class="btn btn-sm btn-outline-warning me-1"><i class="fas fa-edit"></i></a>';
            echo '<form method="POST" class="d-inline" onsubmit="return confirm(\'Delete city?\')"><input type="hidden" name="growth_action" value="delete_city"><input type="hidden" name="city_id" value="'.$c['id'].'"><button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button></form>';
            echo '</td></tr>';
        }
        if (empty($cities)) echo '<tr><td colspan="5" class="text-center text-muted py-4">No cities yet. Add above or run migration.</td></tr>';
        echo '</tbody></table></div><p class="small text-muted mt-2 mb-0">'.count($cities).' cities · Used for programmatic landing pages</p></div>';
    }

    // ==========================================
    // AUTO INDEXING (IndexNow, Bing, DuckDuckGo, Google)
    // ==========================================
    elseif ($page == 'seo') {
        $idxInfo = function_exists('admin_indexnow_info') ? admin_indexnow_info() : ['key'=>'','key_url'=>'','host'=>''];
        $queue = function_exists('admin_index_queue') ? admin_index_queue(15) : [];
        echo '<h2 class="mb-4"><i class="fas fa-search-plus text-info"></i> Auto Indexing Engine</h2>';

        echo '<div class="row g-3 mb-4">';
        echo '<div class="col-6 col-md-3"><div class="stat-box"><div class="stat-val text-success">'.number_format($growthStats['indexed']).'</div><div class="stat-lbl">Indexed</div></div></div>';
        echo '<div class="col-6 col-md-3"><div class="stat-box"><div class="stat-val text-warning">'.number_format($growthStats['pending_index']).'</div><div class="stat-lbl">Pending</div></div></div>';
        echo '<div class="col-6 col-md-3"><div class="stat-box"><div class="stat-val">'.number_format($growthStats['submitted_index']).'</div><div class="stat-lbl">Submitted</div></div></div>';
        echo '<div class="col-6 col-md-3"><div class="stat-box"><div class="stat-val text-danger">'.number_format($growthStats['failed_index']).'</div><div class="stat-lbl">Failed</div></div></div>';
        echo '</div>';

        echo '<div class="row g-4"><div class="col-lg-5"><div class="card p-4"><h5 class="text-info">Indexing Actions</h5>';
        echo '<form method="POST" class="d-grid gap-2 mb-2"><input type="hidden" name="growth_action" value="queue_pending"><button class="btn btn-info">Queue All Pending (500)</button></form>';
        echo '<form method="POST" class="d-grid gap-2 mb-2"><input type="hidden" name="growth_action" value="process_queue"><button class="btn btn-success">Process Queue → IndexNow + Bing + Yandex</button></form>';
        echo '<form method="POST" class="d-grid gap-2 mb-2"><input type="hidden" name="growth_action" value="ping_sitemap"><button class="btn btn-outline-light">Ping Sitemap (Google + Bing)</button></form>';
        echo '<form method="POST" class="d-grid gap-2"><input type="hidden" name="growth_action" value="queue_and_process"><button class="btn btn-warning text-dark">Queue + Submit All (One Click)</button></form>';
        echo '<hr><p class="small text-muted mb-1"><strong>Search engines:</strong> IndexNow API, Bing, Yandex, DuckDuckGo (via IndexNow), Google sitemap ping</p>';
        echo '<p class="small text-muted mb-0">Cron: <code>php cron/process-indexing.php</code></p></div></div>';

        echo '<div class="col-lg-7"><div class="card p-4 mb-3"><h5 class="text-info">IndexNow Configuration</h5>';
        echo '<p class="small text-white-50 mb-1">API Key: <code>'.htmlspecialchars($idxInfo['key']).'</code></p>';
        echo '<p class="small text-white-50 mb-1">Key file: <a href="'.htmlspecialchars($idxInfo['key_url']).'" target="_blank" class="text-info">'.htmlspecialchars($idxInfo['key_url']).'</a></p>';
        echo '<p class="small text-white-50 mb-0">Sitemap: <code>'.(defined('SITE_URL')?SITE_URL:'').'/sitemap.xml</code> · <a href="growth/settings.php">Edit settings</a></p></div>';

        echo '<div class="card p-3"><h6>Recent Index Queue</h6><div class="table-responsive"><table class="table table-dark table-sm mb-0"><thead><tr><th>URL</th><th>Status</th><th>Date</th></tr></thead><tbody>';
        foreach ($queue as $q) {
            echo '<tr><td class="small"><code>'.htmlspecialchars(parse_url($q['url'], PHP_URL_PATH) ?? $q['url']).'</code></td><td>'.$q['status'].'</td><td class="small text-muted">'.$q['created_at'].'</td></tr>';
        }
        if (empty($queue)) echo '<tr><td colspan="3" class="text-muted text-center">No queue items yet</td></tr>';
        echo '</tbody></table></div></div></div></div>';
    }

    // ==========================================
    // 1. BLOG MANAGEMENT
    // ==========================================
    elseif ($page == 'blog') {
        echo '<div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-layer-group text-info"></i> Intel Management</h2>
                <a href="create_post.php" class="btn btn-info"><i class="fas fa-plus"></i> New Protocol</a>
              </div>';
        if(isset($_GET['msg']) && $_GET['msg'] == 'deleted') echo "<div class='alert alert-danger'>Protocol Deleted.</div>";

        $sql = "SELECT * FROM blog_posts ORDER BY created_at DESC";
        $result = $conn->query($sql);

        echo '<div class="table-responsive"><table class="table table-dark table-hover align-middle">
                <thead><tr><th>Date</th><th>Title</th><th>Category</th><th>Actions</th></tr></thead><tbody>';
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td class='text-white-50 small'>".date('M d', strtotime($row['created_at']))."</td>
                    <td class='fw-bold text-white'>" . nectra_display_text($row['title']) . "</td>
                    <td><span class='badge bg-secondary'>" . nectra_display_text($row['category']) . "</span></td>
                    <td>
                        <a href='edit_post.php?id={$row['id']}' class='btn btn-sm btn-outline-warning me-2'><i class='fas fa-pen'></i></a>
                        <a href='?page=blog&delete_post={$row['id']}' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"Purge this protocol?\")'><i class='fas fa-trash'></i></a>
                    </td>
                  </tr>";
        }
        echo '</tbody></table></div>';
    } 

    // ==========================================
    // 2. ADS MANAGEMENT (With Image Upload)
    // ==========================================
    elseif ($page == 'ads') {
        if(isset($_POST['save_ad'])){
            $title = clean_input($_POST['title']);
            $type = $_POST['type'];
            $placement = $_POST['placement'];
            $code = $conn->real_escape_string($_POST['ad_code']);
            $link = clean_input($_POST['link']);
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
                $conn->query("INSERT INTO ads (title, type, placement, image_path, ad_code, link, status) VALUES ('$title', '$type', '$placement', '$img_path', '$code', '$link', 'active')");
                echo "<div class='alert alert-success'>Ad Campaign Launched!</div>";
            }
        }

        echo "<h2><i class='fas fa-ad text-info'></i> Ad Monetization Engine</h2><hr>";
        
        echo "<div class='card p-4 mb-4'>
            <h5>Create New Ad Unit</h5>
            <p class='text-muted small mb-3'>Tip: Sidebar shows up to <strong>20 ad cards</strong> (minimum 15) on blog posts — sidebar ads first, then header/content ads if needed. Set placement <strong>Sidebar</strong> for priority.</p>
            <form method='POST' enctype='multipart/form-data'>
                <div class='row g-3'>
                    <div class='col-md-4'><input type='text' name='title' class='form-control' placeholder='Ad Name' required></div>
                    <div class='col-md-2'><select name='placement' class='form-select'><option value='header'>Top Banner</option><option value='sidebar'>Sidebar</option><option value='content'>In-Article</option></select></div>
                    <div class='col-md-2'><select name='type' class='form-select' onchange='toggleAdType(this.value)'><option value='image'>Custom Image</option><option value='code'>Google Code</option></select></div>
                    <div class='col-md-4 ad-image-group'><input type='file' name='image' class='form-control mb-2'><input type='text' name='link' class='form-control' placeholder='Link URL'></div>
                    <div class='col-12 ad-code-group' style='display:none;'><textarea name='ad_code' class='form-control' rows='3' placeholder='Paste Google Adsense Code here'></textarea></div>
                    <div class='col-12'><button type='submit' name='save_ad' class='btn btn-success w-100'>LAUNCH AD</button></div>
                </div>
            </form>
        </div>";

        $res = $conn->query("SELECT * FROM ads ORDER BY id DESC");
        echo "<table class='table table-dark'><thead><tr><th>Preview</th><th>Name</th><th>Type</th><th>Placement</th><th>Action</th></tr></thead><tbody>";
        while($row = $res->fetch_assoc()){
            $preview = ($row['type'] == 'image' && !empty($row['image_path'])) ? "<img src='../{$row['image_path']}' width='50' height='50' class='ad-preview'>" : "<i class='fas fa-code fa-lg'></i>";
            echo "<tr><td>$preview</td><td>" . nectra_display_text($row['title']) . "</td><td><span class='badge bg-secondary'>{$row['type']}</span></td><td><span class='badge bg-info'>{$row['placement']}</span></td><td><a href='?page=ads&del_ad={$row['id']}' class='text-danger' onclick='return confirm(\"Delete Ad?\")'>Delete</a></td></tr>";
        }
        echo "</tbody></table>";
        echo "<script>function toggleAdType(val){if(val=='code'){document.querySelector('.ad-image-group').style.display='none';document.querySelector('.ad-code-group').style.display='block';}else{document.querySelector('.ad-image-group').style.display='block';document.querySelector('.ad-code-group').style.display='none';}}</script>";
    }

    // ==========================================
    // 3. COMMENTS MANAGEMENT
    // ==========================================
    elseif ($page == 'comments') {
        echo "<h2><i class='fas fa-comments text-info'></i> Community Manager</h2><hr>";
        $res = $conn->query("SELECT * FROM comments ORDER BY created_at DESC");
        echo "<table class='table table-dark'><thead><tr><th>User</th><th>Comment</th><th>Status</th><th>Action</th></tr></thead><tbody>";
        while($row = $res->fetch_assoc()){
            $status = $row['status'] == 'pending' ? "<a href='?page=comments&approve_comment={$row['id']}' class='btn btn-sm btn-success'>Approve</a>" : "<span class='text-success'>Live</span>";
            echo "<tr><td>{$row['name']}</td><td><small>{$row['comment']}</small></td><td>$status</td><td><a href='?page=comments&del_comment={$row['id']}' class='text-danger' onclick='return confirm(\"Delete Comment?\")'><i class='fas fa-trash'></i></a></td></tr>";
        }
        echo "</tbody></table>";
    }

    // ==========================================
    // 4. CAREERS & APPLICATIONS
    // ==========================================
    elseif ($page == 'careers') {
        // Tab Navigation
        echo '<ul class="nav nav-tabs mb-4 border-secondary">
                <li class="nav-item"><a class="nav-link active bg-dark text-white border-secondary" href="#">Active Jobs</a></li>
                <li class="nav-item"><a class="nav-link text-info" href="#applications">Inbound Applications</a></li>
              </ul>';

        // 1. JOBS SECTION
        echo '<div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-briefcase text-info"></i> Recruitment Ops</h2>
                <a href="create_job.php" class="btn btn-info"><i class="fas fa-plus"></i> Open New Position</a>
              </div>';

        $res = $conn->query("SELECT * FROM careers ORDER BY created_at DESC");
        echo "<table class='table table-dark table-hover'><thead><tr><th>Position</th><th>Stack</th><th>Status</th><th>Action</th></tr></thead><tbody>";
        while($row = $res->fetch_assoc()){
            echo "<tr>
                <td>{$row['position']}</td>
                <td>{$row['stack']}</td>
                <td><span class='badge bg-success'>{$row['status']}</span></td>
                <td><a href='?page=careers&del_career={$row['id']}' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"Close Position?\")'>Close</a></td>
            </tr>";
        }
        echo "</tbody></table>";

        // 2. APPLICATIONS SECTION (Resumes)
        echo '<h3 class="mt-5 text-warning" id="applications"><i class="fas fa-file-pdf"></i> Received Dossiers</h3><hr>';
        $app_res = $conn->query("SELECT a.*, c.position FROM applications a JOIN careers c ON a.job_id = c.id ORDER BY a.created_at DESC");
        
        echo "<div class='table-responsive'><table class='table table-dark table-bordered border-secondary'>
                <thead><tr><th>Candidate</th><th>Applying For</th><th>Cover Letter</th><th>Resume</th></tr></thead><tbody>";
        
        if($app_res->num_rows > 0){
            while($app = $app_res->fetch_assoc()){
                echo "<tr>
                    <td>
                        <strong class='text-white'>{$app['name']}</strong><br>
                        <small class='text-info'>{$app['email']}</small><br>
                        <small class='text-muted'>{$app['created_at']}</small>
                    </td>
                    <td>{$app['position']}</td>
                    <td><div style='max-height:80px; overflow-y:auto; font-size:0.85rem;' class='text-white-50'>{$app['cover_letter']}</div></td>
                    <td>
                        <a href='../{$app['resume_path']}' target='_blank' class='btn btn-sm btn-success'>
                            <i class='fas fa-download'></i> Download PDF
                        </a>
                    </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='4' class='text-center text-white-50'>No applications received yet.</td></tr>";
        }
        echo "</tbody></table></div>";
    }

    // ==========================================
    // 5. NEW: HIRE REQUESTS MANAGEMENT
    // ==========================================
    elseif ($page == 'hire_requests') {
        echo '<div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-user-tie text-info"></i> Hire Expert Requests</h2>
              </div>';
              
        if(isset($_GET['msg'])) {
            if($_GET['msg'] == 'deleted') echo "<div class='alert alert-danger'>Request Deleted.</div>";
            if($_GET['msg'] == 'status_updated') echo "<div class='alert alert-success'>Status Updated successfully.</div>";
        }

        $sql = "SELECT * FROM hire_requests ORDER BY created_at DESC";
        $result = $conn->query($sql);

        echo '<div class="table-responsive"><table class="table table-dark table-hover align-middle">
                <thead><tr><th>Date</th><th>Client Info</th><th>Service & Budget</th><th>Status</th><th>Actions</th></tr></thead><tbody>';
        
        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td><small class='text-white-50'>".date('M d, Y', strtotime($row['created_at']))."</small></td>
                        <td>
                            <strong class='text-white'>".htmlspecialchars($row['full_name'])."</strong><br>
                            <a href='mailto:".htmlspecialchars($row['email'])."' class='small text-info text-decoration-none'>".htmlspecialchars($row['email'])."</a><br>
                            <a href='https://wa.me/".preg_replace('/[^0-9]/', '', $row['phone'])."' target='_blank' class='small text-success text-decoration-none'><i class='fab fa-whatsapp'></i> ".htmlspecialchars($row['phone'])."</a>
                        </td>
                        <td>
                            <span class='badge bg-secondary'>".htmlspecialchars($row['service_needed'])."</span><br>
                            <small class='text-white-50'>Budget: ".htmlspecialchars($row['budget'])."</small>
                        </td>
                        <td>
                            <form method='POST' class='d-inline-block'>
                                <input type='hidden' name='request_id' value='{$row['id']}'>
                                <input type='hidden' name='update_hire_status' value='1'>
                                <select name='status' class='form-select form-select-sm d-inline-block w-auto bg-dark text-white border-secondary' onchange='this.form.submit()'>
                                    <option value='new' ".($row['status'] == 'new' ? 'selected' : '').">New</option>
                                    <option value='contacted' ".($row['status'] == 'contacted' ? 'selected' : '').">Contacted</option>
                                    <option value='closed' ".($row['status'] == 'closed' ? 'selected' : '').">Closed</option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <button class='btn btn-sm btn-outline-info' onclick='toggleDetails({$row['id']})'><i class='fas fa-eye'></i> View</button>
                            <a href='?page=hire_requests&del_hire={$row['id']}' class='btn btn-sm btn-outline-danger ms-1' onclick='return confirm(\"Delete this lead?\")'><i class='fas fa-trash'></i></a>
                        </td>
                      </tr>
                      <tr id='details-{$row['id']}' style='display:none; background-color: #1a1a1a;'>
                          <td colspan='5' class='p-3 border-bottom border-secondary'>
                              <h6 class='text-info mb-2'>Project Directives:</h6>
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
    // LEADS
    // ==========================================
    elseif ($page == 'leads') {
        echo '<h2><i class="fas fa-inbox text-info"></i> Incoming Transmissions</h2><hr class="border-secondary mb-4">';
        $sql = "SELECT * FROM leads ORDER BY created_at DESC";
        $result = $conn->query($sql);
        echo '<div class="table-responsive"><table class="table table-dark table-hover"><thead><tr><th>Status</th><th>Entity</th><th>Service</th><th>Message</th></tr></thead><tbody>';
        while($row = $result->fetch_assoc()){
            $spam = $row['is_spam'] ? "<span class='badge bg-danger'>BOT</span>" : "<span class='badge bg-success'>CLEAN</span>";
            echo "<tr><td>$spam</td><td><strong class='text-white'>{$row['name']}</strong><br><small class='text-info'>{$row['email']}</small><br><small class='text-white-50'>IP: {$row['ip_address']}</small></td><td>{$row['service']}<br><small>{$row['budget']}</small></td><td><div style='max-width:300px; height:60px; overflow-y:auto;' class='small text-white-50'>{$row['message']}</div></td></tr>";
        }
        echo '</tbody></table></div>';
    }

    else {
        header('Location: dashboard.php?page=home');
        exit;
    }
    ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleDetails(id) {
        var row = document.getElementById('details-' + id);
        if (row.style.display === 'none' || row.style.display === '') {
            row.style.display = 'table-row';
        } else {
            row.style.display = 'none';
        }
    }
</script>

</body>
</html>