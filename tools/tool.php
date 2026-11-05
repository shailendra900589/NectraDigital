<?php
require_once __DIR__ . '/../includes/growth/bootstrap.php';

use Growth\Models\Tool;

$slug = $_GET['slug'] ?? '';
$tool = ge_table_exists('ge_tools') ? Tool::findBySlug($slug) : null;

if (!$tool || $tool['status'] !== 'active') {
    header('HTTP/1.0 404 Not Found');
    header('Location: ' . SITE_URL . '/404.php');
    exit;
}

Tool::incrementUsage((int)$tool['id']);

$page_title = $tool['name'] . ' — Free Tool | ' . SITE_NAME;
$page_desc = $tool['description'] ?? 'Free enterprise tool by Nectra Digital.';
$page_keys = $tool['name'] . ', free tool, SEO utilities';

include __DIR__ . '/../includes/header.php';
?>

<main class="bg-light">
    <!-- Hero Header -->
    <header class="nd-page-hero nd-legal-hero text-center">
        <div class="container py-2 py-md-3">
            <h1 class="fw-bold text-white mb-2 nd-hero-title"><?php echo htmlspecialchars($tool['name']); ?></h1>
            <p class="text-white-50 mx-auto mb-0" style="max-width: 650px;"><?php echo htmlspecialchars($tool['description'] ?? ''); ?></p>
        </div>
    </header>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white mb-4">
                    <?php echo render_tool_ui($tool); ?>
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-white">
                    <h5 class="fw-bold text-dark mb-2">Need Custom Implementation or Deep Audit?</h5>
                    <p class="text-muted small mx-auto mb-3" style="max-width: 520px;">Our engineering team can build customized automation pipelines or run comprehensive technical audits for your domain.</p>
                    <div>
                        <a href="/contact?service=<?php echo urlencode($tool['name']); ?>" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold shadow-xs">
                            Schedule Free Technical Consultation <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
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
                <div class="col-12">
                    <label class="form-label small fw-semibold text-dark">Page Title</label>
                    <input type="text" id="mt_title" class="form-control" placeholder="e.g. Best Enterprise SEO Services | Nectra Digital">
                </div>
                <div class="col-12">
                    <label class="form-label small fw-semibold text-dark">Meta Description</label>
                    <textarea id="mt_desc" class="form-control" rows="3" placeholder="Brief summary under 160 characters..."></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label small fw-semibold text-dark">Keywords (Comma separated)</label>
                    <input type="text" id="mt_keys" class="form-control" placeholder="e.g. SEO services, web development, AI automation">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold">Generate Meta Code</button>
                </div>
                <div class="col-12">
                    <label class="form-label small fw-semibold text-dark mt-2">Generated HTML</label>
                    <pre id="metaOutput" class="bg-dark text-white p-3 rounded-3 small mb-0 font-monospace" style="white-space: pre-wrap; font-size: 0.82rem;"></pre>
                </div>
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
                <div class="col-md-6">
                    <label class="form-label small fw-semibold text-dark">Monthly Marketing Spend (₹ / $)</label>
                    <input type="number" id="roi_spend" class="form-control" value="50000">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold text-dark">Gross Revenue Generated (₹ / $)</label>
                    <input type="number" id="roi_rev" class="form-control" value="200000">
                </div>
                <div class="col-12">
                    <button class="btn btn-primary px-4 py-2 rounded-pill fw-semibold" onclick="calcRoi()">Calculate ROI & Margin</button>
                </div>
                <div class="col-12">
                    <div id="roiResult" class="p-3 bg-light rounded-3 text-dark fw-bold h5 mb-0"></div>
                </div>
            </div>
            <script>function calcRoi(){const s=+document.getElementById('roi_spend').value,r=+document.getElementById('roi_rev').value;
            if(s<=0){document.getElementById('roiResult').textContent='Please enter spend greater than 0';return;}
            const roi=((r-s)/s)*100;const profit=r-s;
            document.getElementById('roiResult').innerHTML='<span class="text-success">Net Profit: ₹'+profit.toLocaleString('en-IN')+'</span> · ROI: <strong>'+roi.toFixed(1)+'%</strong>';}</script>
            <?php
            break;
        case 'cost_calculator':
            ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold text-dark">Total Pages / Screens</label>
                    <input type="number" id="wc_pages" class="form-control" value="10">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold text-dark">Custom Features Tier (1–5)</label>
                    <input type="number" id="wc_feat" class="form-control" value="3" min="1" max="5">
                </div>
                <div class="col-12">
                    <button class="btn btn-primary px-4 py-2 rounded-pill fw-semibold" onclick="calcCost()">Estimate Project Cost</button>
                </div>
                <div class="col-12">
                    <div id="costResult" class="p-3 bg-light rounded-3 text-dark fw-bold h5 mb-0"></div>
                </div>
            </div>
            <script>function calcCost(){const p=+document.getElementById('wc_pages').value,f=+document.getElementById('wc_feat').value;
            const est=((p*12000)+(f*20000));
            document.getElementById('costResult').innerHTML='Estimated Budget Range: <strong class="text-primary">₹'+est.toLocaleString('en-IN')+' – ₹'+(Math.round(est*1.25)).toLocaleString('en-IN')+'</strong>';}</script>
            <?php
            break;
        case 'seo_audit':
        case 'keyword':
        case 'content_analyzer':
            ?>
            <form class="row g-3" onsubmit="return analyzeUrl(event,'<?php echo $type; ?>')">
                <div class="col-md-8">
                    <label class="form-label small fw-semibold text-dark">Target URL to Inspect</label>
                    <input type="url" id="audit_url" class="form-control" placeholder="https://example.com" required>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill fw-semibold">Analyze URL</button>
                </div>
                <div class="col-12">
                    <pre id="auditResult" class="bg-dark text-white-50 p-3 rounded-3 small mb-0 font-monospace" style="max-height: 280px; overflow-y: auto;"></pre>
                </div>
            </form>
            <script>async function analyzeUrl(e,type){e.preventDefault();const u=document.getElementById('audit_url').value;document.getElementById('auditResult').textContent='Analyzing target domain...';
            try{const r=await fetch('/api/tool-analyze.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({url:u,type:type})});const j=await r.json();document.getElementById('auditResult').textContent=JSON.stringify(j,null,2);}catch(err){document.getElementById('auditResult').textContent='Error running analysis: '+err.message;}}</script>
            <?php
            break;
        default:
            echo '<p class="text-muted small">Interactive UI for this tool type. Contact us for custom integration.</p>';
            echo '<a href="/contact?service=' . urlencode($tool['name']) . '" class="btn btn-primary btn-sm rounded-pill px-3">Request Demo</a>';
    }
    return ob_get_clean();
}

include __DIR__ . '/../includes/footer.php';
