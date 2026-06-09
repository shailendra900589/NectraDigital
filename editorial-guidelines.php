<?php 
require_once 'includes/seo-data.php';
require_once 'includes/seo-components.php';

$page_title = "Editorial Guidelines | Nectra Digital Content Standards";
$page_desc = "Nectra Digital editorial guidelines ensuring EEAT-compliant, expert-verified content. Learn about our content creation standards, author verification, and fact-checking process.";
$page_keys = "Editorial Guidelines, EEAT Content Standards, Nectra Digital Editorial Policy";

$page_schema = [get_breadcrumb_schema([
    ['name' => 'Home', 'url' => SITE_URL . '/'],
    ['name' => 'Editorial Guidelines', 'url' => SITE_URL . '/editorial-guidelines']
])];

include 'includes/header.php';
?>

<main class="py-5">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php render_breadcrumbs([['name' => 'Home', 'url' => '/'], ['name' => 'Editorial Guidelines', 'url' => '/editorial-guidelines']]); ?>
                <h1 class="text-white mb-2">Editorial <span class="text-neon">Guidelines</span></h1>
                <p class="text-white-50 mb-5">Last Updated: <?php echo date("F Y"); ?> · Reviewed by <?php echo FOUNDER_NAME; ?></p>

                <div class="bg-glass border border-secondary p-5 rounded text-white-50" style="line-height: 1.8;">
                    <h2 class="text-white h5 mb-3">1. Content Mission</h2>
                    <p class="mb-4">Nectra Digital publishes expert-verified content to educate businesses about SEO, digital marketing, AI automation, and software development. Every article is created to provide genuine value — not to manipulate search rankings with thin or misleading content.</p>

                    <h2 class="text-white h5 mb-3">2. Author Standards (EEAT)</h2>
                    <p class="mb-4">All content is authored or reviewed by <?php echo FOUNDER_NAME; ?> (<?php echo FOUNDER_TITLE; ?>) or verified subject-matter experts with demonstrated experience. Author bios include credentials, expertise areas, and LinkedIn profiles for transparency.</p>

                    <h2 class="text-white h5 mb-3">3. Fact-Checking Process</h2>
                    <p class="mb-4">Before publication, content undergoes: technical accuracy review, data source verification, SEO best-practice validation against current Google guidelines, and readability optimization for both humans and AI answer engines.</p>

                    <h2 class="text-white h5 mb-3">4. Content Updates</h2>
                    <p class="mb-4">We review and update published content quarterly or when significant industry changes occur (algorithm updates, platform changes, new regulations). Updated articles display the revision date.</p>

                    <h2 class="text-white h5 mb-3">5. AI & Automation Disclosure</h2>
                    <p class="mb-4">We may use AI tools to assist research and drafting, but all published content is human-reviewed, edited, and approved by our editorial team. AI-generated content is never published without expert verification.</p>

                    <h2 class="text-white h5 mb-3">6. Corrections Policy</h2>
                    <p class="mb-4">If you identify factual errors, contact <a href="mailto:editorial@nectradigital.com" class="text-neon">editorial@nectradigital.com</a>. We correct verified errors within 48 hours with transparent update notes.</p>

                    <h2 class="text-white h5 mb-3">7. Affiliate & Sponsored Content</h2>
                    <p class="mb-0">Sponsored content and affiliate links are clearly disclosed. Editorial independence is maintained — sponsorship never influences our technical recommendations or reviews.</p>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
