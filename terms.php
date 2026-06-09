<?php 
$page_title = "Terms of Engagement";
$page_desc = "Terms and Conditions for engaging with Nectra Digital. Service agreements, payment terms, and intellectual property rights.";
include 'includes/header.php'; 
?>

<main class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="mb-5 text-center">
                    <h6 class="text-neon text-uppercase mb-2">Legal Infrastructure</h6>
                    <h1 class="text-white">TERMS OF <span class="text-neon">ENGAGEMENT</span></h1>
                    <p class="text-white-50">Effective Date: <?php echo date("F Y"); ?></p>
                </div>

                <div class="bg-glass border border-secondary p-5 rounded text-white-50" style="font-size: 0.95rem; line-height: 1.8;">
                    
                    <h4 class="text-white mb-3">01. The Agreement</h4>
                    <p class="mb-4">
                        By initializing a project with Nectra Digital, you agree to these terms. We do not provide "off-the-shelf" products; we engineer custom digital assets. Therefore, all timelines provided are estimates based on complexity.
                    </p>

                    <h4 class="text-white mb-3">02. Payments & Intellectual Property</h4>
                    <p class="mb-4">
                        <strong>• Payment Structure:</strong> A 50% initialization fee is required to deploy our development team. The remaining 50% is due upon project deployment.
                        <br><strong>• IP Rights:</strong> Upon full payment, 100% of the code, design, and assets become the intellectual property of the Client. Nectra Digital retains the right to display the work in our portfolio unless a Non-Disclosure Agreement (NDA) is signed.
                    </p>

                    <h4 class="text-white mb-3">03. Refund Policy</h4>
                    <p class="mb-4">
                        Due to the nature of custom engineering, refunds are not issued once the "Blueprint" phase is approved and coding has commenced. We prioritize satisfaction through infinite revisions during the design phase.
                    </p>

                    <h4 class="text-white mb-3">04. Limitation of Liability</h4>
                    <p class="mb-4">
                        Nectra Digital is not liable for third-party API failures (e.g., Google, OpenAI, AWS) or downtime caused by hosting providers not managed by us.
                    </p>

                    <div class="border-top border-secondary pt-4 mt-5">
                        <p class="small mb-0">
                            Questions regarding these terms? Contact HQ at: 
                            <?php require_once __DIR__ . '/includes/site-contact.php'; echo nectra_email_html_link('text-neon text-decoration-none'); ?>
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>