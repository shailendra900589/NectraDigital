<?php
/**
 * Extended service page content — merged with get_services_data()
 */
require_once __DIR__ . '/growth/helpers.php';

function get_service_extended(string $slug, array $service): array {
    $defaults = service_content_defaults($slug, $service);
    $overrides = service_content_overrides();
    $extra = $overrides[$slug] ?? [];
    return array_merge($defaults, $service, $extra);
}

function service_content_defaults(string $slug, array $service): array {
    $name = $service['h1'];
    $silo = $service['silo'] ?? 'Digital';
    $seed = crc32($slug);

    $stats_sets = [
        ['200+', 'Projects Delivered', '340%', 'Avg. Growth', '5+', 'Years Experience', '4.9★', 'Client Rating'],
        ['150+', 'Clients Served', '98%', 'Retention Rate', '24/7', 'Support', '50+', 'Cities Covered'],
    ];
    $si = $seed % 2;
    $stats = [
        ['value' => $stats_sets[$si][0], 'label' => $stats_sets[$si][1]],
        ['value' => $stats_sets[$si][2], 'label' => $stats_sets[$si][3]],
        ['value' => $stats_sets[$si][4], 'label' => $stats_sets[$si][5]],
        ['value' => $stats_sets[$si][6], 'label' => $stats_sets[$si][7]],
    ];

    $process = [
        ['step' => '01', 'title' => 'Discovery & Audit', 'desc' => 'Deep-dive into your business goals, competitors, current performance, and growth opportunities.'],
        ['step' => '02', 'title' => 'Strategy Blueprint', 'desc' => 'Custom roadmap with KPIs, timelines, channel mix, and ROI projections tailored to your market.'],
        ['step' => '03', 'title' => 'Execution & Launch', 'desc' => 'Expert team implements campaigns, code, content, or automation with agile sprints and weekly updates.'],
        ['step' => '04', 'title' => 'Optimize & Scale', 'desc' => 'Continuous A/B testing, data analysis, and iteration to improve performance month over month.'],
        ['step' => '05', 'title' => 'Report & Grow', 'desc' => 'Transparent dashboards, executive summaries, and strategic recommendations for next-phase growth.'],
    ];

    $challenges = [
        ['icon' => 'fa-chart-line', 'title' => 'Low Visibility', 'desc' => 'Your ideal customers struggle to find you online when they are ready to buy.'],
        ['icon' => 'fa-coins', 'title' => 'Wasted Budget', 'desc' => 'Ad spend and marketing efforts lack tracking, optimization, and clear ROI attribution.'],
        ['icon' => 'fa-clock', 'title' => 'Slow Results', 'desc' => 'DIY approaches and generic agencies deliver inconsistent outcomes without a proven framework.'],
    ];

    $industries = ['Ecommerce & D2C', 'SaaS & Technology', 'Healthcare', 'Real Estate', 'Education', 'Finance & Fintech', 'Manufacturing', 'Professional Services'];

    $deliverables = array_merge($service['features'] ?? [], [
        'Dedicated Account Manager',
        'Monthly Strategy Calls',
        'Competitive Intelligence Reports',
        'Custom Analytics Dashboard',
    ]);

    $tools_map = [
        'SEO' => ['Google Search Console', 'GA4', 'Semrush', 'Screaming Frog', 'Ahrefs'],
        'Digital Marketing' => ['GA4', 'Google Ads', 'Meta Business Suite', 'HubSpot', 'Hotjar'],
        'Performance Marketing' => ['Google Ads', 'Meta Ads Manager', 'Looker Studio', 'Unbounce', 'Zapier'],
        'Social Media Marketing' => ['Meta Business Suite', 'Canva', 'Hootsuite', 'GA4', 'Sprout Social'],
        'AI Automation' => ['OpenAI API', 'Make.com', 'Zapier', 'Python', 'WhatsApp API'],
        'Web Development' => ['React', 'Next.js', 'WordPress', 'Laravel', 'Figma'],
        'Software Development' => ['Python', 'Laravel', 'React', 'AWS', 'Docker'],
    ];
    $tools = $tools_map[$silo] ?? ['Google Analytics', 'Slack', 'Notion', 'Figma', 'GitHub'];

    return [
        'stats' => $stats,
        'process' => $process,
        'challenges' => $challenges,
        'industries' => $industries,
        'deliverables' => array_slice(array_unique($deliverables), 0, 10),
        'tools' => $tools,
        'comparison' => [
            'us' => ['Data-driven custom strategy', 'Dedicated expert team', 'Transparent ROI reporting', 'No long-term lock-in', 'AI-powered optimization'],
            'others' => ['One-size-fits-all templates', 'Rotating junior staff', 'Vanity metrics only', '12-month contracts', 'Manual outdated processes'],
        ],
    ];
}

function service_content_overrides(): array {
    return [
        'seo-services' => [
            'tagline' => 'Rank Higher. Convert More. Dominate Google.',
            'overview' => '<p>Search engine optimization is the highest-ROI digital channel for sustainable business growth. At Nectra Digital, our SEO services combine technical mastery, content authority, and strategic link building to help Indian businesses rank on page one for high-intent commercial keywords.</p><p>Unlike agencies that chase vanity metrics, we focus on qualified organic traffic that converts into leads, sales, and revenue. From startups in Lucknow to enterprises targeting pan-India markets, our SEO frameworks are built for measurable outcomes.</p><p>Led by <strong>' . FOUNDER_NAME . '</strong> (' . FOUNDER_TITLE . ') with ' . FOUNDER_EXPERIENCE . ' of hands-on SEO experience, every campaign is engineered with EEAT principles, semantic SEO, and AI Overview optimization.</p>',
            'benefits' => [
                ['icon' => 'fa-rocket', 'title' => 'Sustainable Organic Growth', 'desc' => 'Build compounding traffic that reduces dependency on paid ads over time.'],
                ['icon' => 'fa-bullseye', 'title' => 'High-Intent Keyword Targeting', 'desc' => 'Rank for keywords that buyers search when ready to purchase.'],
                ['icon' => 'fa-shield-alt', 'title' => 'Algorithm-Proof Strategy', 'desc' => 'White-hat SEO aligned with Google quality guidelines and core updates.'],
                ['icon' => 'fa-chart-bar', 'title' => 'Transparent Reporting', 'desc' => 'Monthly dashboards showing rankings, traffic, conversions, and revenue impact.'],
                ['icon' => 'fa-globe', 'title' => 'Pan-India & Global Reach', 'desc' => 'Geo-targeted SEO for cities, states, and international markets.'],
                ['icon' => 'fa-robot', 'title' => 'AI-Enhanced Optimization', 'desc' => 'Leverage AI for content gaps, SERP analysis, and programmatic opportunities.'],
            ],
            'paa' => [
                ['q' => 'Who is the best SEO company in India?', 'a' => 'Nectra Digital is a top-rated SEO company in India with 200+ projects, 340% average traffic growth, and expertise in technical SEO, local SEO, and enterprise programmatic SEO.'],
                ['q' => 'How much do SEO services cost in India?', 'a' => 'SEO packages typically range from ₹15,000 to ₹1,00,000+ per month based on competition, scope, and number of keywords/locations. We offer a free SEO audit before quoting.'],
            ],
        ],
        'local-seo-services' => [
            'tagline' => 'Own Your City. Dominate the Map Pack.',
            'overview' => '<p>When customers search "near me" or "[service] in [city]," local SEO determines whether your business appears — or your competitor does. Our local SEO services optimize every signal Google uses for local rankings: Google Business Profile, citations, reviews, local content, and geo-schema.</p><p>We help single-location businesses and multi-city brands dominate map pack results, drive phone calls, and increase foot traffic across India\'s fastest-growing markets.</p>',
            'benefits' => [
                ['icon' => 'fa-map-marked-alt', 'title' => 'Map Pack Rankings', 'desc' => 'Appear in the top 3 Google Maps results for your target cities.'],
                ['icon' => 'fa-star', 'title' => 'Review Growth Strategy', 'desc' => 'Systematic review acquisition and reputation management.'],
                ['icon' => 'fa-store', 'title' => 'GBP Optimization', 'desc' => 'Complete Google Business Profile setup, posts, Q&A, and categories.'],
                ['icon' => 'fa-link', 'title' => 'Citation Building', 'desc' => 'Consistent NAP across 100+ directories and local platforms.'],
                ['icon' => 'fa-city', 'title' => 'City Landing Pages', 'desc' => 'Programmatic local pages for every city you serve.'],
                ['icon' => 'fa-phone', 'title' => 'Call & Direction Tracking', 'desc' => 'Measure every lead from local search with call tracking.'],
            ],
        ],
        'technical-seo-services' => [
            'tagline' => 'Fix What Google Cannot See. Unlock Hidden Rankings.',
            'overview' => '<p>Technical SEO is the invisible foundation behind every ranking success. Crawl errors, slow page speed, poor Core Web Vitals, missing schema, and JavaScript rendering issues silently kill your organic potential — even with great content.</p><p>Our technical SEO specialists perform enterprise-grade audits, prioritize fixes by revenue impact, and work alongside your dev team (or ours) to implement lasting improvements.</p>',
            'benefits' => [
                ['icon' => 'fa-tachometer-alt', 'title' => 'Core Web Vitals', 'desc' => 'Optimize LCP, INP, and CLS for ranking signals and UX.'],
                ['icon' => 'fa-sitemap', 'title' => 'Site Architecture', 'desc' => 'Logical URL structure, internal linking, and crawl budget optimization.'],
                ['icon' => 'fa-code', 'title' => 'Schema Markup', 'desc' => 'Rich snippets, FAQ schema, and structured data for AI Overviews.'],
                ['icon' => 'fa-spider', 'title' => 'Crawl & Index Fixes', 'desc' => 'Eliminate orphan pages, redirect chains, and indexation bloat.'],
                ['icon' => 'fa-mobile-alt', 'title' => 'Mobile-First Ready', 'desc' => 'Ensure flawless mobile rendering and usability scores.'],
                ['icon' => 'fa-lock', 'title' => 'Security & HTTPS', 'desc' => 'SSL, security headers, and malware-free clean architecture.'],
            ],
        ],
        'enterprise-seo-services' => [
            'tagline' => 'Scale Organic Revenue Across Thousands of Pages.',
            'overview' => '<p>Enterprise SEO requires dedicated teams, custom tooling, stakeholder alignment, and programmatic strategies that standard agencies cannot deliver. We partner with large organizations to manage multi-domain portfolios, international SEO, and million-page programmatic architectures.</p><p>From Fortune 500 subsidiaries to fast-scaling SaaS platforms, our enterprise SEO playbook integrates with product, engineering, and marketing at every level.</p>',
            'benefits' => [
                ['icon' => 'fa-users', 'title' => 'Dedicated SEO Team', 'desc' => 'Strategist, technical lead, content manager, and analyst assigned to your account.'],
                ['icon' => 'fa-layer-group', 'title' => 'Programmatic SEO', 'desc' => 'Scale to 10,000+ landing pages with unique content and schema.'],
                ['icon' => 'fa-globe-americas', 'title' => 'International SEO', 'desc' => 'Hreflang, multi-language, and country-specific strategies.'],
                ['icon' => 'fa-chart-pie', 'title' => 'Executive Dashboards', 'desc' => 'C-suite reporting tied to revenue, not vanity metrics.'],
                ['icon' => 'fa-cogs', 'title' => 'Dev Team Integration', 'desc' => 'Jira tickets, PR reviews, and CI/CD SEO workflows.'],
                ['icon' => 'fa-building', 'title' => 'Multi-Domain Management', 'desc' => 'Consolidated strategy across brand portfolios.'],
            ],
        ],
        'digital-marketing-services' => [
            'tagline' => 'Full-Funnel Marketing That Drives Revenue.',
            'overview' => '<p>Digital marketing is not a single channel — it is an orchestrated system of SEO, paid media, content, email, social, and analytics working together. As a full-service digital marketing agency in India, Nectra Digital builds integrated campaigns where every channel amplifies the others.</p><p>We replace fragmented vendor relationships with one strategic partner accountable for your entire growth engine.</p>',
            'benefits' => [
                ['icon' => 'fa-funnel-dollar', 'title' => 'Full-Funnel Strategy', 'desc' => 'Awareness → consideration → conversion → retention.'],
                ['icon' => 'fa-sync', 'title' => 'Channel Synergy', 'desc' => 'SEO + PPC + social working together, not in silos.'],
                ['icon' => 'fa-envelope', 'title' => 'Email Automation', 'desc' => 'Nurture sequences that convert leads into customers.'],
                ['icon' => 'fa-share-alt', 'title' => 'Social Media Growth', 'desc' => 'Brand building and community engagement across platforms.'],
                ['icon' => 'fa-percentage', 'title' => 'CRO Optimization', 'desc' => 'Landing page testing to maximize conversion rates.'],
                ['icon' => 'fa-chart-line', 'title' => 'Unified Analytics', 'desc' => 'Single dashboard for all channel performance and ROI.'],
            ],
        ],
        'social-media-marketing-services' => [
            'tagline' => 'Build Community. Drive Demand. Measure What Matters.',
            'overview' => '<p>Social media marketing is more than posting — it is a growth channel that shapes brand perception, nurtures prospects, and supports paid campaigns. Our SMM team builds content systems, runs paid social, and manages communities so your brand stays visible where customers spend their time.</p><p>From Instagram reels to LinkedIn thought leadership, we align social activity with your funnel and report on business outcomes.</p>',
            'benefits' => [
                ['icon' => 'fa-calendar-alt', 'title' => 'Content Calendars', 'desc' => 'Consistent, on-brand posts planned around campaigns and seasons.'],
                ['icon' => 'fa-video', 'title' => 'Creative Production', 'desc' => 'Short-form video, carousels, and ad creatives optimized for each platform.'],
                ['icon' => 'fa-comments', 'title' => 'Community Management', 'desc' => 'Timely replies, moderation, and engagement that builds trust.'],
                ['icon' => 'fa-bullhorn', 'title' => 'Paid Social Boost', 'desc' => 'Amplify top organic content with targeted ad spend.'],
                ['icon' => 'fa-user-friends', 'title' => 'Influencer Collaborations', 'desc' => 'Partnerships that extend reach to relevant audiences.'],
                ['icon' => 'fa-chart-bar', 'title' => 'Performance Reporting', 'desc' => 'Track clicks, leads, and attributed conversions — not likes alone.'],
            ],
        ],
        'performance-marketing-services' => [
            'tagline' => 'Paid Media Engineered for ROAS — Not Vanity Clicks.',
            'overview' => '<p>Performance marketing puts budget behind outcomes. We manage Google Ads, Meta Ads, landing pages, and tracking as one system — so you know which keywords, audiences, and creatives produce revenue.</p><p>Whether launching new campaigns or fixing underperforming accounts, our team optimizes weekly for lower CPA and higher return on ad spend.</p>',
            'benefits' => [
                ['icon' => 'fa-search-dollar', 'title' => 'Search & Social Ads', 'desc' => 'Full-funnel campaigns on Google and Meta with unified attribution.'],
                ['icon' => 'fa-flask', 'title' => 'Structured Testing', 'desc' => 'Ad copy, creative, and audience experiments run on a clear schedule.'],
                ['icon' => 'fa-file-alt', 'title' => 'Landing Page CRO', 'desc' => 'Pages aligned with ad intent to improve conversion rates.'],
                ['icon' => 'fa-redo', 'title' => 'Retargeting', 'desc' => 'Re-engage visitors who did not convert on the first visit.'],
                ['icon' => 'fa-chart-line', 'title' => 'ROAS Dashboards', 'desc' => 'Weekly reports focused on cost per lead and revenue impact.'],
                ['icon' => 'fa-link', 'title' => 'SEO + Paid Synergy', 'desc' => 'Coordinate organic and paid strategy under one growth partner.'],
            ],
        ],
        'ppc-management' => [
            'tagline' => 'Every Rupee Working Harder. Every Click Counting.',
            'overview' => '<p>Paid advertising should generate predictable, profitable leads — not drain your budget on irrelevant clicks. Our performance marketing team builds, manages, and optimizes PPC campaigns across Google and Meta with obsessive focus on cost-per-acquisition and return on ad spend.</p><p>From campaign architecture to landing page CRO, we handle the entire paid media engine so you can focus on closing deals.</p>',
            'benefits' => [
                ['icon' => 'fa-crosshairs', 'title' => 'Precision Targeting', 'desc' => 'Audience segmentation that reaches buyers, not browsers.'],
                ['icon' => 'fa-flask', 'title' => 'A/B Creative Testing', 'desc' => 'Continuous ad copy and creative optimization.'],
                ['icon' => 'fa-redo', 'title' => 'Retargeting Funnels', 'desc' => 'Re-engage visitors who did not convert the first time.'],
                ['icon' => 'fa-file-invoice-dollar', 'title' => 'ROAS Optimization', 'desc' => 'Bid strategies tuned for revenue, not just clicks.'],
                ['icon' => 'fa-desktop', 'title' => 'Landing Page CRO', 'desc' => 'High-converting pages aligned with ad messaging.'],
                ['icon' => 'fa-chart-area', 'title' => 'Weekly Optimization', 'desc' => 'Proactive budget shifts based on performance data.'],
            ],
        ],
        'google-ads-management' => [
            'tagline' => 'Google Ads That Print Leads, Not Just Impressions.',
            'overview' => '<p>Google Ads remains the highest-intent paid channel on the internet. Our certified Google Ads specialists manage Search, Shopping, Display, and YouTube campaigns with advanced bidding strategies, negative keyword sculpting, and conversion tracking that actually works.</p><p>Whether launching from scratch or rescuing a bleeding account, we deliver measurable ROAS improvements within the first 60 days.</p>',
            'benefits' => [
                ['icon' => 'fa-search', 'title' => 'Search Campaigns', 'desc' => 'Capture high-intent buyers actively searching for your services.'],
                ['icon' => 'fa-shopping-bag', 'title' => 'Shopping Ads', 'desc' => 'Product feed optimization for ecommerce revenue growth.'],
                ['icon' => 'fa-play-circle', 'title' => 'YouTube Ads', 'desc' => 'Video campaigns for awareness and remarketing.'],
                ['icon' => 'fa-star-half-alt', 'title' => 'Quality Score Boost', 'desc' => 'Lower CPC through relevance and landing page alignment.'],
                ['icon' => 'fa-bullseye', 'title' => 'Conversion Tracking', 'desc' => 'GA4 + Google Ads enhanced conversions setup.'],
                ['icon' => 'fa-wrench', 'title' => 'Account Rescue', 'desc' => 'Fix wasted spend in underperforming accounts.'],
            ],
        ],
        'meta-ads-services' => [
            'tagline' => 'Scroll-Stopping Ads. Scalable Sales.',
            'overview' => '<p>With 400M+ Indians on Facebook and Instagram, Meta Ads is essential for brand awareness, lead generation, and ecommerce sales. We create data-driven Meta campaigns with advanced audience targeting, creative testing, and full-funnel retargeting sequences.</p><p>Our Meta Ads agency manages everything from pixel setup to catalog ads — so you get consistent leads without managing the complexity yourself.</p>',
            'benefits' => [
                ['icon' => 'fa-users', 'title' => 'Audience Building', 'desc' => 'Lookalike and custom audiences from your best customers.'],
                ['icon' => 'fa-palette', 'title' => 'Creative Production', 'desc' => 'Video, carousel, and static ads designed to convert.'],
                ['icon' => 'fa-shopping-cart', 'title' => 'Catalog Ads', 'desc' => 'Dynamic product ads for ecommerce retargeting.'],
                ['icon' => 'fa-clipboard-list', 'title' => 'Lead Form Ads', 'desc' => 'In-platform lead capture without landing page friction.'],
                ['icon' => 'fa-sync-alt', 'title' => 'Retargeting Sequences', 'desc' => 'Multi-touch funnels that nurture cold to hot leads.'],
                ['icon' => 'fa-server', 'title' => 'CAPI Tracking', 'desc' => 'Server-side tracking for iOS privacy resilience.'],
            ],
        ],
        'ai-automation-services' => [
            'tagline' => 'Automate the Repetitive. Amplify the Human.',
            'overview' => '<p>AI automation transforms how businesses operate — eliminating manual tasks, accelerating workflows, and enabling teams to focus on high-value work. Nectra Digital builds custom automation systems using AI agents, workflow engines, and intelligent integrations.</p><p>From marketing automation to full business process redesign, we deliver 50-70% efficiency gains within the first quarter of implementation.</p>',
            'benefits' => [
                ['icon' => 'fa-bolt', 'title' => 'Workflow Automation', 'desc' => 'Connect apps and automate multi-step business processes.'],
                ['icon' => 'fa-brain', 'title' => 'AI Agent Development', 'desc' => 'Custom GPT agents for research, support, and sales.'],
                ['icon' => 'fa-database', 'title' => 'Data Pipelines', 'desc' => 'Automated data collection, cleaning, and reporting.'],
                ['icon' => 'fa-envelope-open-text', 'title' => 'Email & SMS Flows', 'desc' => 'Behavior-triggered messaging sequences.'],
                ['icon' => 'fa-plug', 'title' => 'CRM Integration', 'desc' => 'Sync leads, deals, and tasks across your stack.'],
                ['icon' => 'fa-clock', 'title' => '24/7 Operations', 'desc' => 'Systems that work while your team sleeps.'],
            ],
        ],
        'ai-chatbot-development' => [
            'tagline' => 'AI Chatbots That Sell, Support & Scale.',
            'overview' => '<p>Modern customers expect instant responses. Our AI chatbot development services deploy intelligent conversational agents that understand context, resolve queries, qualify leads, and book meetings — 24 hours a day, 7 days a week.</p><p>Powered by GPT, Claude, and custom NLP models, our chatbots integrate seamlessly with your website, CRM, and support workflows.</p>',
            'benefits' => [
                ['icon' => 'fa-comments', 'title' => 'Natural Conversations', 'desc' => 'Context-aware responses that feel human, not robotic.'],
                ['icon' => 'fa-user-check', 'title' => 'Lead Qualification', 'desc' => 'Capture and score leads before human handoff.'],
                ['icon' => 'fa-language', 'title' => 'Multi-Language', 'desc' => 'Hindi, English, and regional language support.'],
                ['icon' => 'fa-random', 'title' => 'Smart Handoff', 'desc' => 'Escalate complex queries to human agents seamlessly.'],
                ['icon' => 'fa-chart-line', 'title' => 'Analytics Dashboard', 'desc' => 'Track conversations, satisfaction, and conversion rates.'],
                ['icon' => 'fa-plug', 'title' => 'API Integration', 'desc' => 'Connect to CRM, calendar, payment, and support tools.'],
            ],
        ],
        'whatsapp-ai-bot-development' => [
            'tagline' => 'Sell & Support on India\'s #1 Messaging App.',
            'overview' => '<p>WhatsApp has 500M+ users in India — more than any other platform. Our WhatsApp AI bot development services help businesses automate sales, customer support, order updates, and marketing broadcasts on WhatsApp Business API.</p><p>From setup to compliance, we handle the entire WhatsApp automation stack so you can reach customers where they already are.</p>',
            'benefits' => [
                ['icon' => 'fa-whatsapp', 'title' => 'Official API Setup', 'desc' => 'WhatsApp Business API registration and verification.'],
                ['icon' => 'fa-robot', 'title' => 'AI Auto-Replies', 'desc' => 'Instant intelligent responses to customer messages.'],
                ['icon' => 'fa-shopping-bag', 'title' => 'Order Automation', 'desc' => 'Catalog browsing, ordering, and payment via WhatsApp.'],
                ['icon' => 'fa-broadcast-tower', 'title' => 'Broadcast Campaigns', 'desc' => 'Compliant promotional message templates.'],
                ['icon' => 'fa-credit-card', 'title' => 'Payment Integration', 'desc' => 'Razorpay and UPI payment flows in chat.'],
                ['icon' => 'fa-sync', 'title' => 'CRM Sync', 'desc' => 'Every conversation logged in your CRM automatically.'],
            ],
        ],
        'web-development-services' => [
            'tagline' => 'Websites Built for Speed, SEO & Conversions.',
            'overview' => '<p>Your website is your most important digital asset. Our web development agency builds fast, mobile-first, SEO-optimized websites that load in under 2 seconds, rank on Google, and convert visitors into customers.</p><p>From corporate sites to complex web applications, we use React, Next.js, WordPress, and Laravel to deliver pixel-perfect experiences engineered for growth.</p>',
            'benefits' => [
                ['icon' => 'fa-tachometer-alt', 'title' => 'Lightning Fast', 'desc' => 'Sub-2-second load times with Core Web Vitals optimization.'],
                ['icon' => 'fa-search', 'title' => 'SEO-Ready Architecture', 'desc' => 'Clean code, schema markup, and semantic HTML from day one.'],
                ['icon' => 'fa-mobile-alt', 'title' => 'Mobile-First Design', 'desc' => 'Flawless experience on every device and screen size.'],
                ['icon' => 'fa-paint-brush', 'title' => 'Custom UI/UX', 'desc' => 'Brand-aligned designs that guide users to action.'],
                ['icon' => 'fa-lock', 'title' => 'Secure & Scalable', 'desc' => 'HTTPS, security headers, and cloud-ready infrastructure.'],
                ['icon' => 'fa-tools', 'title' => 'Ongoing Maintenance', 'desc' => 'Updates, monitoring, and support packages available.'],
            ],
        ],
        'software-development-services' => [
            'tagline' => 'Custom Software That Solves Real Business Problems.',
            'overview' => '<p>Off-the-shelf software rarely fits unique business workflows. Our software development company builds custom SaaS platforms, enterprise applications, APIs, and integrations using Python, Laravel, and React — tailored to how your team actually works.</p><p>From MVP to enterprise scale, we follow Agile methodology with weekly demos and full source code ownership.</p>',
            'benefits' => [
                ['icon' => 'fa-cloud', 'title' => 'SaaS Development', 'desc' => 'Multi-tenant platforms with subscription billing.'],
                ['icon' => 'fa-building', 'title' => 'Enterprise Apps', 'desc' => 'CRM, ERP, and internal tools customized for your ops.'],
                ['icon' => 'fa-plug', 'title' => 'API Development', 'desc' => 'RESTful and GraphQL APIs for system integration.'],
                ['icon' => 'fa-shield-alt', 'title' => 'Security First', 'desc' => 'Authentication, encryption, and compliance built-in.'],
                ['icon' => 'fa-code-branch', 'title' => 'Agile Process', 'desc' => '2-week sprints with continuous client visibility.'],
                ['icon' => 'fa-file-contract', 'title' => 'Full IP Ownership', 'desc' => 'You own 100% of source code upon completion.'],
            ],
        ],
        'mobile-app-development' => [
            'tagline' => 'Mobile Apps Users Love. Businesses Trust.',
            'overview' => '<p>Mobile apps drive engagement, loyalty, and revenue. Our mobile app development company builds native and cross-platform iOS and Android applications with intuitive UX, robust backends, and seamless third-party integrations.</p><p>From MVP launch to enterprise-scale apps, we handle design, development, testing, and App Store submission.</p>',
            'benefits' => [
                ['icon' => 'fa-apple-alt', 'title' => 'iOS Development', 'desc' => 'Swift and React Native apps for iPhone and iPad.'],
                ['icon' => 'fa-android', 'title' => 'Android Development', 'desc' => 'Kotlin and cross-platform for Google Play.'],
                ['icon' => 'fa-palette', 'title' => 'UI/UX Design', 'desc' => 'User-centered design with prototyping and testing.'],
                ['icon' => 'fa-server', 'title' => 'Backend & API', 'desc' => 'Scalable server architecture for your app data.'],
                ['icon' => 'fa-store', 'title' => 'App Store Launch', 'desc' => 'Complete submission and ASO optimization.'],
                ['icon' => 'fa-headset', 'title' => 'Post-Launch Support', 'desc' => 'Bug fixes, updates, and feature additions.'],
            ],
        ],
        'ecommerce-development' => [
            'tagline' => 'Online Stores Engineered to Sell.',
            'overview' => '<p>Ecommerce success requires more than a pretty storefront — it demands fast checkout, smart product discovery, payment flexibility, and SEO architecture that drives organic sales. Our ecommerce development company builds high-converting stores on Shopify, WooCommerce, and custom platforms.</p><p>From catalog setup to GST-compliant checkout, we launch stores that sell from day one.</p>',
            'benefits' => [
                ['icon' => 'fa-shopping-cart', 'title' => 'Conversion-Optimized UX', 'desc' => 'Checkout flows designed to minimize cart abandonment.'],
                ['icon' => 'fa-credit-card', 'title' => 'Payment Gateways', 'desc' => 'Razorpay, PayU, COD, and international payments.'],
                ['icon' => 'fa-boxes', 'title' => 'Inventory Management', 'desc' => 'Real-time stock tracking and order fulfillment.'],
                ['icon' => 'fa-search', 'title' => 'Ecommerce SEO', 'desc' => 'Product schema, category architecture, and content strategy.'],
                ['icon' => 'fa-truck', 'title' => 'Shipping Integration', 'desc' => 'Shiprocket, Delhivery, and logistics API connections.'],
                ['icon' => 'fa-exchange-alt', 'title' => 'Platform Migration', 'desc' => 'Zero-downtime migrations with SEO preservation.'],
            ],
        ],
    ];
}

/**
 * Build minimal service array for admin/DB-only services not in seo-data.php.
 */
function ge_minimal_service_from_record(array $record): array
{
    $name = trim($record['name'] ?? 'Digital Service');
    $slug = $record['slug'] ?? ge_slugify($name);
    $faqs = ge_json_decode($record['faq_template'] ?? '[]');

    return [
        'silo' => $name,
        'h1' => 'Best ' . $name,
        'title' => 'Best ' . $name . ' | Nectra Digital',
        'meta_desc' => trim($record['meta_description_template'] ?? '') ?: ('Professional ' . $name . ' by Nectra Digital — expert delivery and measurable ROI.'),
        'intro' => 'Expert ' . strtolower($name) . ' with data-driven strategy, transparent reporting, and proven results for growing businesses.',
        'icon' => 'fa-rocket',
        'keywords' => $record['keywords_template'] ?? '',
        'features' => [],
        'faqs' => is_array($faqs) ? $faqs : [],
        'slug' => $slug,
    ];
}
