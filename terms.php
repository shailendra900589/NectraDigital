<?php 
$page_title = "Terms of Service | Service Agreement & Policies";
$page_desc = "Nectra Digital Terms of Service. Read our service agreement, payment terms, intellectual property rights, refund policy, and project delivery terms before engaging with us.";
$page_keys = "terms of service, terms and conditions, nectra digital terms, service agreement, payment terms, refund policy, web development contract";
include 'includes/header.php'; 
?>

<main class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="mb-5 text-center">
                    <span class="badge border border-neon text-neon mb-3 px-3 py-2 text-uppercase" style="letter-spacing: 2px;">Legal</span>
                    <h1 class="display-5 fw-bold text-white">Terms of <span class="text-neon">Service</span></h1>
                    <p class="text-white-50">Effective Date: <?php echo date("F j, Y"); ?></p>
                </div>

                <div class="bg-glass border border-secondary p-4 p-md-5 rounded text-white-50" style="font-size: 0.95rem; line-height: 1.85;">
                    
                    <p class="mb-4">
                        These Terms of Service ("Terms") govern your engagement with <strong class="text-white">Nectra Digital</strong> ("we", "us", "the Company"). By contracting our services, submitting a project inquiry, or using our website, you agree to be bound by these Terms. Please read them carefully.
                    </p>

                    <h2 class="h5 text-white mb-3 mt-5">1. Services We Provide</h2>
                    <p class="mb-2">Nectra Digital provides the following professional services:</p>
                    <ul class="mb-4">
                        <li class="mb-2">Custom Website Development (WordPress, React, Next.js, PHP, Laravel)</li>
                        <li class="mb-2">E-Commerce Development (Shopify, WooCommerce, Custom Platforms)</li>
                        <li class="mb-2">Mobile Application Development (React Native, Flutter)</li>
                        <li class="mb-2">Search Engine Optimization (SEO) & Digital Marketing</li>
                        <li class="mb-2">UI/UX Design & Branding</li>
                        <li class="mb-2">AI & Automation Solutions</li>
                        <li class="mb-2">Dedicated Developer Hiring</li>
                    </ul>
                    <p class="mb-4">The specific scope of work will be defined in the project proposal/agreement shared with you before project commencement.</p>

                    <h2 class="h5 text-white mb-3 mt-5">2. Project Engagement Process</h2>
                    <p class="mb-2">Our standard engagement process follows these steps:</p>
                    <ol class="mb-4">
                        <li class="mb-2"><strong class="text-white">Consultation:</strong> Free initial discussion to understand your requirements</li>
                        <li class="mb-2"><strong class="text-white">Proposal:</strong> Detailed project proposal with scope, timeline, and pricing</li>
                        <li class="mb-2"><strong class="text-white">Agreement:</strong> Signing of project agreement and payment of advance</li>
                        <li class="mb-2"><strong class="text-white">Development:</strong> Project execution with regular updates and milestone reviews</li>
                        <li class="mb-2"><strong class="text-white">Delivery:</strong> Final delivery, testing, and deployment</li>
                        <li class="mb-2"><strong class="text-white">Support:</strong> Post-launch support period (as per agreement)</li>
                    </ol>

                    <h2 class="h5 text-white mb-3 mt-5">3. Payment Terms</h2>
                    <ul class="mb-4">
                        <li class="mb-2"><strong class="text-white">Advance Payment:</strong> A minimum of 50% advance is required before project work begins. For larger projects, milestone-based payments may be arranged.</li>
                        <li class="mb-2"><strong class="text-white">Final Payment:</strong> The remaining balance is due upon project completion and before final files/access are handed over.</li>
                        <li class="mb-2"><strong class="text-white">Payment Methods:</strong> We accept UPI, bank transfer (NEFT/IMPS), PayPal, and other digital payment methods.</li>
                        <li class="mb-2"><strong class="text-white">Late Payments:</strong> Invoices unpaid beyond 15 days may result in project suspension until payment is received.</li>
                        <li class="mb-2"><strong class="text-white">GST:</strong> All prices are exclusive of GST (18%) unless stated otherwise.</li>
                    </ul>

                    <h2 class="h5 text-white mb-3 mt-5">4. Intellectual Property Rights</h2>
                    <ul class="mb-4">
                        <li class="mb-2">Upon <strong class="text-white">full payment</strong>, all custom code, designs, and assets created specifically for your project become your intellectual property.</li>
                        <li class="mb-2">Third-party libraries, frameworks, plugins, and open-source components used in the project remain under their respective licenses.</li>
                        <li class="mb-2">Nectra Digital retains the right to showcase the project in our portfolio and case studies unless a Non-Disclosure Agreement (NDA) is signed.</li>
                        <li class="mb-2">Any pre-existing proprietary tools, templates, or code libraries owned by Nectra Digital remain our property and are licensed for use in your project.</li>
                    </ul>

                    <h2 class="h5 text-white mb-3 mt-5">5. Revisions & Scope Changes</h2>
                    <ul class="mb-4">
                        <li class="mb-2">Each project includes a defined number of revision rounds as specified in the proposal (typically 2-3 rounds for design, unlimited minor tweaks during development).</li>
                        <li class="mb-2">Requests beyond the agreed scope constitute a "Change Request" and will be quoted separately.</li>
                        <li class="mb-2">Significant changes to project direction after development has begun may require a revised timeline and additional costs.</li>
                    </ul>

                    <h2 class="h5 text-white mb-3 mt-5">6. Timelines & Delivery</h2>
                    <ul class="mb-4">
                        <li class="mb-2">All timelines provided are <strong class="text-white">estimates</strong> based on project complexity and are subject to change based on client feedback delays or scope modifications.</li>
                        <li class="mb-2">Delays caused by late content/asset delivery from the client, delayed feedback, or scope changes are not the responsibility of Nectra Digital.</li>
                        <li class="mb-2">We commit to transparent communication regarding any timeline adjustments.</li>
                    </ul>

                    <h2 class="h5 text-white mb-3 mt-5">7. Refund & Cancellation Policy</h2>
                    <ul class="mb-4">
                        <li class="mb-2"><strong class="text-white">Before Work Begins:</strong> Full refund minus any consultation/planning time already invested (if applicable).</li>
                        <li class="mb-2"><strong class="text-white">After Design Phase Approval:</strong> No refund on work already completed. Payment for completed milestones is non-refundable.</li>
                        <li class="mb-2"><strong class="text-white">Project Cancellation:</strong> If you cancel mid-project, you will be billed for all work completed up to that point at our standard hourly rate.</li>
                        <li class="mb-2"><strong class="text-white">Satisfaction Guarantee:</strong> We offer unlimited revisions during the design phase to ensure you're happy before development begins.</li>
                    </ul>

                    <h2 class="h5 text-white mb-3 mt-5">8. Confidentiality</h2>
                    <p class="mb-4">
                        We treat all project information, business data, and communications as confidential. We will not disclose your project details, business strategies, or proprietary information to any third party without your explicit consent. For enhanced protection, we are happy to sign a mutual NDA upon request.
                    </p>

                    <h2 class="h5 text-white mb-3 mt-5">9. Post-Launch Support & Maintenance</h2>
                    <ul class="mb-4">
                        <li class="mb-2">All projects include <strong class="text-white">30 days of free post-launch support</strong> covering bug fixes and minor adjustments.</li>
                        <li class="mb-2">After the free support period, maintenance and updates are available under monthly/annual maintenance plans starting from ₹5,000/month.</li>
                        <li class="mb-2">Hosting, domain renewals, and third-party service costs are the client's responsibility unless explicitly included in the agreement.</li>
                    </ul>

                    <h2 class="h5 text-white mb-3 mt-5">10. Limitation of Liability</h2>
                    <ul class="mb-4">
                        <li class="mb-2">Nectra Digital is not liable for any indirect, incidental, or consequential damages arising from the use of our services.</li>
                        <li class="mb-2">We are not responsible for failures caused by third-party services (hosting providers, APIs, payment gateways, etc.) not directly managed by us.</li>
                        <li class="mb-2">Our total liability shall not exceed the total amount paid by you for the specific service in question.</li>
                        <li class="mb-2">We do not guarantee specific business results (e.g., revenue increase, ranking positions) unless explicitly stated in a performance-based agreement.</li>
                    </ul>

                    <h2 class="h5 text-white mb-3 mt-5">11. Client Responsibilities</h2>
                    <p class="mb-2">To ensure smooth project delivery, the client agrees to:</p>
                    <ul class="mb-4">
                        <li class="mb-2">Provide all required content, assets, and information in a timely manner</li>
                        <li class="mb-2">Respond to feedback requests within 3-5 business days</li>
                        <li class="mb-2">Ensure all content provided is original or properly licensed</li>
                        <li class="mb-2">Designate a single point of contact for project communications</li>
                    </ul>

                    <h2 class="h5 text-white mb-3 mt-5">12. Termination</h2>
                    <p class="mb-4">
                        Either party may terminate the engagement with 7 days written notice. Upon termination, all completed work must be paid for, and any advance payment for uncompleted work will be refunded on a pro-rata basis (minus hours already invested).
                    </p>

                    <h2 class="h5 text-white mb-3 mt-5">13. Dispute Resolution</h2>
                    <p class="mb-4">
                        Any disputes arising from these Terms shall first be resolved through good-faith negotiation. If unresolved, disputes will be subject to arbitration in Lucknow, Uttar Pradesh, India, under the Indian Arbitration and Conciliation Act, 1996. The courts of Lucknow shall have exclusive jurisdiction.
                    </p>

                    <h2 class="h5 text-white mb-3 mt-5">14. Changes to These Terms</h2>
                    <p class="mb-4">
                        We reserve the right to modify these Terms at any time. Changes will be posted on this page with an updated effective date. Continued use of our services after changes constitutes acceptance of the modified Terms.
                    </p>

                    <div class="border-top border-secondary pt-4 mt-5">
                        <h2 class="h5 text-white mb-3">Questions About These Terms?</h2>
                        <p class="small mb-2">If you have any questions or need clarification on any clause, please reach out:</p>
                        <p class="small mb-1"><strong class="text-white">Email:</strong> <a href="mailto:contact@nectradigital.com" class="text-neon text-decoration-none">contact@nectradigital.com</a></p>
                        <p class="small mb-1"><strong class="text-white">Phone:</strong> <a href="tel:+917678387759" class="text-neon text-decoration-none">+91-7678387759</a></p>
                        <p class="small mb-0"><strong class="text-white">Address:</strong> Nectra Digital, Lucknow, Uttar Pradesh, India - 226001</p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
