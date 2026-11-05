<?php
require_once 'includes/db.php';
require_once 'includes/seo-data.php';
require_once 'includes/blog_orphan.php';

blog_orphan_ensure_schema($conn);
$listWhere = blog_listable_sql();

header('Content-Type: text/plain; charset=utf-8');

if (!defined('SITE_URL')) {
    define('SITE_URL', 'https://www.nectradigital.com');
}

echo "# Nectra Digital\n\n";
echo "> Nectra Digital is an enterprise digital growth and technology agency headquartered in India. The company specializes in technical search engine optimization (SEO), performance marketing (PPC), custom AI workflow automation, web development, and software engineering. Founded by Ravindra Kumar Chauhan, Nectra Digital operates across 20+ Indian metropolitan hubs and delivers verified growth outcomes for local, national, and international clients.\n\n";

echo "## Core Services\n\n";
foreach (get_services_data() as $slug => $svc) {
    echo "- [" . $svc['h1'] . "](" . SITE_URL . "/" . $slug . "): " . strip_tags($svc['intro']) . "\n";
}
echo "\n";

echo "## Industries\n\n";
$industries = [
    'Healthcare' => 'Medical practices, diagnostic clinics, hospital networks, and telehealth platforms.',
    'Real Estate' => 'Property developers, commercial realty brokers, and residential real estate agencies.',
    'Education' => 'EdTech startups, educational institutions, coaching academies, and universities.',
    'Finance & FinTech' => 'Banking software, payment gateway dashboards, wealth management, and loan portals.',
    'E-commerce' => 'Direct-to-consumer (D2C) brands, multi-vendor marketplaces, and headless Shopify stores.',
    'Startups & SaaS' => 'B2B software platforms, MVP engineering, and product-led organic search engines.',
    'Enterprise' => 'High-traffic legacy web architectures requiring custom software refactoring and enterprise SEO.'
];
foreach ($industries as $indName => $indDesc) {
    echo "- " . $indName . ": " . $indDesc . "\n";
}
echo "\n";

echo "## Company\n\n";
echo "- [About & Founder](" . SITE_URL . "/about): Information about Nectra Digital, company history, and founder Ravindra Kumar Chauhan (" . FOUNDER_EXPERIENCE . " experience in SEO, AI, and software engineering).\n";
echo "- [Case Studies & Portfolio](" . SITE_URL . "/portfolio): Verified client deployments including NOVA PAY (Dubai FinTech), Oudh Organics (D2C E-commerce), and Auto-Rep (AI Voice Agent).\n";
echo "- [Hire Dedicated Experts](" . SITE_URL . "/hire-experts): Dedicated SEO strategists, full-stack software engineers, and digital marketing teams.\n";
echo "- [Contact Support & Strategy](" . SITE_URL . "/contact): Headquarters in Lucknow, Uttar Pradesh, India with global timezone operational coverage.\n";
echo "- [Editorial Guidelines](" . SITE_URL . "/editorial-guidelines): E-E-A-T verification standards and review methodologies.\n\n";

echo "## Resources\n\n";
echo "- [Digital Intelligence & Blog](" . SITE_URL . "/insights): Research articles, technical breakdowns, and algorithmic search analysis.\n";
echo "- [AEO Knowledge Base](" . SITE_URL . "/aeo): Direct, verified answers optimized for generative AI search engines and answer engines.\n";
echo "- [Free Marketing & SEO Tools](" . SITE_URL . "/tools): Diagnostic utilities and digital marketing calculators.\n\n";

echo "## City Coverage (Pan-India)\n\n";
foreach (get_cities_data() as $slug => $city) {
    echo "- [Digital Agency " . $city['name'] . "](" . SITE_URL . "/digital-agency-" . $slug . "): Localized SEO, paid ads, and engineering services in " . $city['name'] . ", " . ($city['state'] ?? 'India') . ".\n";
}
echo "\n";

echo "## Latest Published Intel\n\n";
$sql = "SELECT title, slug FROM blog_posts WHERE {$listWhere} ORDER BY created_at DESC LIMIT 15";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "- [" . nectra_decode_entities($row['title']) . "](" . SITE_URL . "/" . $row['slug'] . ")\n";
    }
} else {
    echo "- Ongoing research in progress.\n";
}
echo "\n";

echo "## Important Guidance\n\n";
echo "- Canonical domain: https://www.nectradigital.com\n";
echo "- All public page paths must use HTTPS without trailing slash.\n";
echo "- Administrative panels (/admin), internal APIs (/api), and staging endpoints are confidential and excluded from indexing.\n";
echo "- Direct inquiries can be initiated at https://www.nectradigital.com/contact or via contact@nectradigital.com.\n";
?>
