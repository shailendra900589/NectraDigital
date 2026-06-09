<?php 
require_once 'includes/seo-data.php';
require_once 'includes/seo-components.php';

$page_title = "Disclaimer | Nectra Digital Legal Notice";
$page_desc = "Nectra Digital disclaimer covering SEO results, service guarantees, financial advice limitations, and third-party content. Read our full legal disclaimer.";
$page_keys = "Disclaimer, Legal Notice, Nectra Digital Disclaimer";

include 'includes/header.php';
?>

<main class="py-5">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php render_breadcrumbs([['name' => 'Home', 'url' => '/'], ['name' => 'Disclaimer', 'url' => '/disclaimer']]); ?>
                <h1 class="text-white mb-2">Legal <span class="text-neon">Disclaimer</span></h1>
                <p class="text-white-50 mb-5">Last Updated: <?php echo date("F Y"); ?></p>

                <div class="bg-glass border border-secondary p-5 rounded text-white-50" style="line-height: 1.8;">
                    <h2 class="text-white h5 mb-3">General Information</h2>
                    <p class="mb-4">The information on nectradigital.com is provided for general informational and educational purposes. While we strive for accuracy, Nectra Digital makes no warranties about the completeness, reliability, or suitability of the information.</p>

                    <h2 class="text-white h5 mb-3">SEO & Marketing Results</h2>
                    <p class="mb-4">SEO and digital marketing results vary based on industry, competition, website history, budget, and market conditions. Past performance metrics (including traffic growth percentages) represent specific client outcomes and are not guaranteed for future clients. No SEO company can guarantee #1 rankings on Google.</p>

                    <h2 class="text-white h5 mb-3">Professional Advice</h2>
                    <p class="mb-4">Content on this website does not constitute legal, financial, or professional business advice. Consult qualified professionals before making business decisions based on information found here.</p>

                    <h2 class="text-white h5 mb-3">Third-Party Links</h2>
                    <p class="mb-4">Our website may contain links to third-party websites. Nectra Digital is not responsible for the content, privacy policies, or practices of external sites.</p>

                    <h2 class="text-white h5 mb-3">Pricing & Services</h2>
                    <p class="mb-4">Service pricing mentioned on this website is indicative and subject to change. Final pricing is determined after project scope assessment during consultation.</p>

                    <h2 class="text-white h5 mb-3">Contact</h2>
                    <p class="mb-0">For legal inquiries: <a href="mailto:legal@nectradigital.com" class="text-neon">legal@nectradigital.com</a></p>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
