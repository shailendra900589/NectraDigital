<?php 
require_once 'includes/seo-data.php';
require_once 'includes/seo-components.php';

$page_title = "SEO & Digital Marketing FAQ | Answer Engine Optimized";
$page_desc = "Direct answers to top SEO, AI automation, and digital marketing questions. What is SEO? How much does SEO cost? Best SEO company in India? Optimized for Google AI Overviews and ChatGPT.";
$page_keys = "What is SEO, How much does SEO cost, Best SEO Company India, What is AI Automation, What is Digital Marketing, Lead Generation";

$aeo_answers = get_aeo_answers();
$all_faqs = [];
foreach ($aeo_answers as $a) {
    $all_faqs[] = ['q' => $a['question'], 'a' => $a['quick_answer'] . ' ' . $a['detailed']];
}

include 'includes/header.php';
output_faq_schema($all_faqs);
?>

<main>
    <header class="py-5 text-center" style="background: linear-gradient(to bottom, #050505, #0a1518);">
        <div class="container py-4">
            <?php render_breadcrumbs([['name' => 'Home', 'url' => '/'], ['name' => 'AEO Answers', 'url' => '/aeo']]); ?>
            <h1 class="display-5 text-white fw-bold mb-3">Answer Engine <span class="text-neon">Optimized</span> Knowledge Base</h1>
            <p class="text-white-50 mx-auto" style="max-width: 700px;">Direct, expert-verified answers optimized for Google AI Overviews, ChatGPT, Gemini, Perplexity, and Copilot.</p>
        </div>
    </header>

    <section class="py-5">
        <div class="container">
            <?php foreach ($aeo_answers as $key => $aeo): ?>
            <article id="<?php echo $key; ?>" class="mb-5 p-4 border border-secondary rounded bg-glass">
                <h2 class="text-neon h4 mb-3"><?php echo htmlspecialchars($aeo['question']); ?></h2>
                
                <div class="p-3 border border-neon rounded bg-dark mb-3">
                    <h3 class="text-white h6 mb-2"><i class="fas fa-bolt text-neon me-2"></i>Quick Answer</h3>
                    <p class="text-white mb-0"><?php echo htmlspecialchars($aeo['quick_answer']); ?></p>
                </div>

                <div class="mb-3">
                    <h3 class="text-white h6 mb-2">Detailed Explanation</h3>
                    <p class="text-white-50"><?php echo htmlspecialchars($aeo['detailed']); ?></p>
                </div>

                <div class="p-3 border border-secondary rounded bg-dark mb-3">
                    <h3 class="text-white h6 mb-2"><i class="fas fa-lightbulb text-neon me-2"></i>Expert Insight</h3>
                    <p class="text-white-50 small fst-italic mb-0">"<?php echo FOUNDER_NAME; ?>, <?php echo FOUNDER_TITLE; ?>: Based on our experience serving 200+ clients, the key is combining proven fundamentals with data-driven optimization — not chasing shortcuts."</p>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <a href="/contact?service=<?php echo urlencode($aeo['question']); ?>" class="btn btn-nectra btn-sm">Get Expert Help</a>
                    <a href="/seo-services" class="btn btn-outline-light btn-sm">SEO Services</a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </section>

    <?php render_cta_blocks('compact'); ?>
</main>

<?php include 'includes/footer.php'; ?>
