<?php
require_once __DIR__ . '/../includes/growth/bootstrap.php';

use Growth\Models\Tool;

$slug = $_GET['slug'] ?? '';
$tool = ge_table_exists('ge_tools') ? Tool::findBySlug($slug) : null;

if (!$tool || $tool['status'] !== 'active') {
    header('HTTP/1.0 404 Not Found');
    header('Location: /404.php');
    exit;
}

Tool::incrementUsage((int)$tool['id']);

$page_title = $tool['name'] . ' | ' . SITE_NAME;
$page_desc = $tool['description'] ?? '';
$page_keys = $tool['name'] . ', free tool, SEO';

include __DIR__ . '/../includes/header.php';
?>

<main class="py-5">
    <div class="container py-4">
        <nav aria-label="breadcrumb" class="mb-3"><ol class="breadcrumb bg-transparent p-0"><li class="breadcrumb-item"><a href="/tools" class="text-white-50">Tools</a></li><li class="breadcrumb-item active text-neon"><?php echo htmlspecialchars($tool['name']); ?></li></ol></nav>
        <h1 class="display-6 fw-bold text-white mb-3"><?php echo htmlspecialchars($tool['name']); ?></h1>
        <p class="text-white-50 mb-4"><?php echo htmlspecialchars($tool['description'] ?? ''); ?></p>

        <div class="ge-card p-4 border border-secondary rounded bg-glass">
            <?php echo render_tool_ui($tool); ?>
        </div>

        <div class="mt-4 text-center">
            <a href="/contact?service=<?php echo urlencode($tool['name']); ?>" class="btn btn-nectra">Get Expert Help</a>
        </div>
    </div>
</main>

<?php
function render_tool_ui(array $tool): string {
    $type = $tool['tool_type'];
    ob_start();
    switch ($type) {
        case 'meta_generator':
            ?>
            <form id="metaForm" class="row g-3" onsubmit="return generateMeta(event)">
                <div class="col-12"><label class="form-label text-white">Page Title</label><input type="text" id="mt_title" class="form-control" placeholder="Your page title"></div>
                <div class="col-12"><label class="form-label text-white">Description</label><textarea id="mt_desc" class="form-control" rows="3"></textarea></div>
                <div class="col-12"><label class="form-label text-white">Keywords</label><input type="text" id="mt_keys" class="form-control"></div>
                <div class="col-12"><button type="submit" class="btn btn-nectra">Generate Meta Tags</button></div>
                <div class="col-12"><pre id="metaOutput" class="bg-dark p-3 rounded text-neon small"></pre></div>
            </form>
            <script>
            function generateMeta(e){e.preventDefault();
            const t=document.getElementById('mt_title').value,d=document.getElementById('mt_desc').value,k=document.getElementById('mt_keys').value;
            document.getElementById('metaOutput').textContent=`<title>${t}</title>\n<meta name="description" content="${d}">\n<meta name="keywords" content="${k}">`;}
            </script>
            <?php
            break;
        case 'roi_calculator':
            ?>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label text-white">Ad Spend (₹)</label><input type="number" id="roi_spend" class="form-control" value="50000"></div>
                <div class="col-md-4"><label class="form-label text-white">Revenue (₹)</label><input type="number" id="roi_rev" class="form-control" value="200000"></div>
                <div class="col-md-4"><label class="form-label text-white">&nbsp;</label><button class="btn btn-nectra w-100" onclick="calcRoi()">Calculate ROI</button></div>
                <div class="col-12"><div id="roiResult" class="text-neon h4"></div></div>
            </div>
            <script>function calcRoi(){const s=+document.getElementById('roi_spend').value,r=+document.getElementById('roi_rev').value;document.getElementById('roiResult').textContent='ROI: '+(((r-s)/s)*100).toFixed(1)+'%';}</script>
            <?php
            break;
        case 'cost_calculator':
            ?>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label text-white">Pages</label><input type="number" id="wc_pages" class="form-control" value="10"></div>
                <div class="col-md-6"><label class="form-label text-white">Features (1-5)</label><input type="number" id="wc_feat" class="form-control" value="3" min="1" max="5"></div>
                <div class="col-12"><button class="btn btn-nectra" onclick="calcCost()">Estimate Cost</button></div>
                <div class="col-12"><div id="costResult" class="text-neon h4"></div></div>
            </div>
            <script>function calcCost(){const p=+document.getElementById('wc_pages').value,f=+document.getElementById('wc_feat').value;document.getElementById('costResult').textContent='Est. ₹'+((p*15000)+(f*25000)).toLocaleString('en-IN');}</script>
            <?php
            break;
        case 'seo_audit':
        case 'keyword':
        case 'content_analyzer':
            ?>
            <form class="row g-3" onsubmit="return analyzeUrl(event,'<?php echo $type; ?>')">
                <div class="col-md-9"><input type="url" id="audit_url" class="form-control" placeholder="https://example.com" required></div>
                <div class="col-md-3"><button type="submit" class="btn btn-nectra w-100">Analyze</button></div>
                <div class="col-12"><pre id="auditResult" class="bg-dark p-3 rounded text-white-50 small mb-0"></pre></div>
            </form>
            <script>async function analyzeUrl(e,type){e.preventDefault();const u=document.getElementById('audit_url').value;document.getElementById('auditResult').textContent='Analyzing...';
            try{const r=await fetch('/api/tool-analyze.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({url:u,type:type})});const j=await r.json();document.getElementById('auditResult').textContent=JSON.stringify(j,null,2);}catch(err){document.getElementById('auditResult').textContent='Error: '+err.message;}}</script>
            <?php
            break;
        default:
            echo '<p class="text-white-50">Interactive UI for this tool type. Contact us for a custom implementation.</p>';
            echo '<a href="/contact?service=' . urlencode($tool['name']) . '" class="btn btn-nectra">Request Demo</a>';
    }
    return ob_get_clean();
}

include __DIR__ . '/../includes/footer.php';
