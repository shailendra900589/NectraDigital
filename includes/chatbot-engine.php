<?php
/**
 * Nectra AI Chatbot — intent matching, services KB, lead capture (EN + HI/Hinglish)
 */
require_once __DIR__ . '/seo-data.php';

define('NECTRA_CHAT_PHONE', '+91 7678387759');
define('NECTRA_CHAT_PHONE_RAW', '+917678387759');
define('NECTRA_CHAT_EMAIL', 'contact@nectradigital.com');
define('NECTRA_CHAT_WHATSAPP', 'https://wa.me/917678387759');

function nectra_chat_normalize(string $text): string
{
    $text = mb_strtolower(trim($text), 'UTF-8');
    $text = preg_replace('/\s+/u', ' ', $text);
    return $text;
}

function nectra_chat_matches(string $text, array $patterns): bool
{
    foreach ($patterns as $p) {
        if (preg_match($p, $text)) {
            return true;
        }
    }
    return false;
}

function nectra_chat_welcome(): string
{
    if (function_exists('ge_setting')) {
        $w = ge_setting('chatbot_welcome', '');
        if ($w !== '') {
            return $w;
        }
    }
    return 'Namaste! Main Nectra AI hoon. Aap services, pricing, contact ya free SEO audit ke baare mein pooch sakte hain. Main Hindi aur English dono mein help kar sakta hoon.';
}

function nectra_chat_services_list(): string
{
    $services = get_services_data();
    $lines = ["Nectra Digital ki main services:\n"];
    $i = 1;
    foreach ($services as $slug => $svc) {
        $name = preg_replace('/\s*\|.*$/', '', $svc['h1'] ?? $svc['title'] ?? $slug);
        $intro = mb_substr(strip_tags($svc['intro'] ?? ''), 0, 90);
        $lines[] = "{$i}. **{$name}** — {$intro}…";
        $lines[] = "   " . SITE_URL . "/{$slug}";
        $i++;
    }
    $lines[] = "\nKisi ek service par detail chahiye? Naam likhiye (jaise SEO, Web Development, AI).";
    $lines[] = "Free audit ke liye \"audit\" ya \"lead submit\" likhein.";
    return implode("\n", $lines);
}

function nectra_chat_service_detail(string $text): ?string
{
    $services = get_services_data();
    $map = [
        'seo' => 'seo-services',
        'search engine' => 'seo-services',
        'local seo' => 'local-seo-services',
        'google map' => 'local-seo-services',
        'technical seo' => 'technical-seo-services',
        'enterprise seo' => 'enterprise-seo-services',
        'digital marketing' => 'digital-marketing-services',
        'marketing' => 'digital-marketing-services',
        'google ads' => 'google-ads-services',
        'ppc' => 'google-ads-services',
        'meta ads' => 'meta-ads-services',
        'facebook ads' => 'meta-ads-services',
        'instagram ads' => 'meta-ads-services',
        'ai automation' => 'ai-automation-services',
        'automation' => 'ai-automation-services',
        'chatbot' => 'ai-chatbot-development',
        'whatsapp bot' => 'whatsapp-ai-bot-development',
        'whatsapp' => 'whatsapp-ai-bot-development',
        'web development' => 'web-development-services',
        'website' => 'web-development-services',
        'software' => 'software-development-services',
        'app development' => 'mobile-app-development-services',
        'mobile app' => 'mobile-app-development-services',
        'ecommerce' => 'ecommerce-development-services',
        'e-commerce' => 'ecommerce-development-services',
        'branding' => 'branding-services',
    ];

    foreach ($map as $keyword => $slug) {
        if (strpos($text, $keyword) !== false && isset($services[$slug])) {
            $svc = $services[$slug];
            $name = $svc['h1'] ?? $svc['title'];
            $intro = strip_tags($svc['intro'] ?? '');
            $features = implode(', ', array_slice($svc['features'] ?? [], 0, 5));
            $faq = $svc['faqs'][0]['a'] ?? '';
            return "**{$name}**\n\n{$intro}\n\nKey offerings: {$features}.\n\n" .
                ($faq ? "FAQ: {$faq}\n\n" : '') .
                "Page: " . SITE_URL . "/{$slug}\n\n" .
                "Proposal chahiye? \"lead submit\" likhein ya apna naam + email bhejein.";
        }
    }
    return null;
}

function nectra_chat_contact_reply(): string
{
    return "Hamse connect karein:\n\n" .
        "📞 Phone: " . NECTRA_CHAT_PHONE . "\n" .
        "📧 Email: " . NECTRA_CHAT_EMAIL . "\n" .
        "💬 WhatsApp: " . NECTRA_CHAT_WHATSAPP . "\n" .
        "📍 Office: Lucknow, Uttar Pradesh, India\n" .
        "🌐 Contact page: " . SITE_URL . "/contact\n\n" .
        "Aap direct call ya WhatsApp kar sakte hain — team 24 ghante ke andar respond karti hai.";
}

function nectra_chat_pricing_reply(): string
{
    return "Pricing project scope par depend karti hai:\n\n" .
        "• Local SEO: ~₹15,000–₹50,000/month\n" .
        "• SEO / Digital Marketing: custom monthly retainers\n" .
        "• Web Development: project-based (₹50K+)\n" .
        "• AI Chatbot: ₹50,000 se start\n" .
        "• Enterprise / custom software: quote after discovery call\n\n" .
        "Exact quote ke liye free audit/proposal — \"lead submit\" likhein ya naam + email share karein.";
}

function nectra_chat_about_reply(): string
{
    return "Nectra Digital — India ki leading SEO & AI automation agency.\n\n" .
        "• 5+ years experience | 200+ projects\n" .
        "• Founder: " . FOUNDER_NAME . " (" . FOUNDER_TITLE . ")\n" .
        "• Services: SEO, Ads, Web/App Dev, AI Automation\n" .
        "• HQ: Lucknow, India | Clients globally\n\n" .
        "About: " . SITE_URL . "/about\nPortfolio: " . SITE_URL . "/portfolio";
}

function nectra_chat_is_email(string $text): bool
{
    return (bool) filter_var(trim($text), FILTER_VALIDATE_EMAIL);
}

function nectra_chat_is_phone(string $text): bool
{
    $digits = preg_replace('/\D/', '', $text);
    return strlen($digits) >= 10 && strlen($digits) <= 13;
}

function nectra_chat_extract_lead_from_message(string $text): array
{
    $lead = ['name' => '', 'email' => '', 'phone' => '', 'service' => '', 'message' => $text];
    if (preg_match('/[\w.+-]+@[\w-]+\.[\w.-]+/', $text, $m)) {
        $lead['email'] = $m[0];
    }
    if (preg_match('/(?:\+91|91)?[\s-]?[6-9]\d{9}/', $text, $m)) {
        $lead['phone'] = preg_replace('/\s+/', '', $m[0]);
    }
    if (preg_match('/(?:mera naam|my name is|i am|main)\s+([a-zA-Z\s]{2,40})/iu', $text, $m)) {
        $lead['name'] = trim($m[1]);
    }
    return $lead;
}

function nectra_chat_intent_reply(string $text): ?string
{
    if (nectra_chat_matches($text, [
        '/^(hi|hello|hey|namaste|namaskar|hii|hlw|help|start|menu)\b/u',
        '/kaise ho|kese ho|good (morning|evening|afternoon)/u',
    ])) {
        return nectra_chat_welcome();
    }

    if (nectra_chat_matches($text, [
        '/\b(cancel|stop|exit|quit|bas|band karo|band karein|nahi chahiye|leave it|never mind)\b/u',
    ])) {
        return '__CANCEL_LEAD__';
    }

    if (nectra_chat_matches($text, [
        '/\b(service|services|seva|kya karte|kya offer|kya provide|batao|bataye|batana|list)\b/u',
        '/services ke baare|service ke baare|kon si service|kaun si service/u',
        '/tum kya karte|aap kya karte|what do you (do|offer)/u',
    ])) {
        return nectra_chat_services_list();
    }

    $detail = nectra_chat_service_detail($text);
    if ($detail) {
        return $detail;
    }

    if (nectra_chat_matches($text, [
        '/\b(phone|mobile|number|call|contact|whatsapp|reach|expert|connect)\b/u',
        '/mobile number|phone number|call karo|number do|number de|contact karo/u',
        '/experts ke|expert ka|baat karni|baat kar/u',
    ])) {
        return nectra_chat_contact_reply();
    }

    if (nectra_chat_matches($text, [
        '/\b(price|pricing|cost|rate|quote|budget|kitna|kitne|fees|charge|package)\b/u',
        '/daam|kimat|price kya|cost kya/u',
    ])) {
        return nectra_chat_pricing_reply();
    }

    if (nectra_chat_matches($text, [
        '/\b(about|company|who are you|nectra|founder|portfolio|experience)\b/u',
        '/aap kaun|tum kaun|company ke baare/u',
    ])) {
        return nectra_chat_about_reply();
    }

    if (nectra_chat_matches($text, [
        '/\b(audit|proposal|callback|consultation|meeting|demo|hire|quote request)\b/u',
        '/lead submit|free audit|audit chahiye|proposal chahiye|connect karo/u',
        '/interested|details bhejo|contact me|call back/u',
    ])) {
        return '__START_LEAD__';
    }

    if (nectra_chat_matches($text, [
        '/\b(thank|thanks|dhanyavad|shukriya|ok thanks|got it)\b/u',
    ])) {
        return "You're welcome! Aur koi sawaal ho to poochiye — services, pricing, contact ya free audit.";
    }

    return null;
}

function nectra_chat_submit_lead(array $lead): array
{
    $name = trim($lead['name'] ?? '');
    $email = trim($lead['email'] ?? '');
    $phone = trim($lead['phone'] ?? '');
    $service = trim($lead['service'] ?? 'Chatbot Inquiry');
    $message = trim($lead['message'] ?? 'Submitted via Nectra AI Chatbot');

    if (!$name || !$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'error' => 'Invalid lead data'];
    }

    if (function_exists('ge_table_exists') && ge_table_exists('ge_crm_leads')) {
        require_once __DIR__ . '/growth/bootstrap.php';
        $id = \Growth\Models\CrmLead::create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone ?: null,
            'service_interest' => $service,
            'message' => $message,
            'source' => 'chatbot',
            'meta_json' => function_exists('ge_json_encode')
                ? ge_json_encode(['page' => $_SERVER['HTTP_REFERER'] ?? ''])
                : json_encode(['page' => $_SERVER['HTTP_REFERER'] ?? '']),
        ]);
        return [
            'success' => true,
            'lead_id' => $id,
            'reply' => "✅ Shukriya {$name}! Aapki details save ho gayi.\n\n" .
                "Hamari team 24 ghante ke andar {$email} par contact karegi.\n" .
                "Urgent? Call/WhatsApp: " . NECTRA_CHAT_PHONE,
        ];
    }

    return [
        'success' => true,
        'reply' => "✅ Shukriya {$name}! Humne aapki request note kar li.\nTeam jald contact karegi. Phone: " . NECTRA_CHAT_PHONE,
    ];
}

function nectra_chat_process(string $message, array &$state): array
{
    $text = nectra_chat_normalize($message);
    if ($text === '') {
        return [
            'reply' => 'Kripya apna message type karein.',
            'quick_replies' => ['Services', 'Pricing', 'Contact', 'Free Audit'],
            'state' => $state,
        ];
    }

    $quick = ['Services', 'Pricing', 'Contact', 'Free Audit'];

    // Lead capture flow
    if (($state['mode'] ?? 'idle') !== 'idle') {
        $mode = $state['mode'];

        // Context-specific inputs before global intent (e.g. "skip" for optional phone)
        if ($mode === 'lead_phone' && in_array($text, ['skip', 'na', 'n/a', 'none', 'no'], true)) {
            return nectra_chat_advance_lead($text, $state);
        }

        if ($text === 'continue lead' || $text === 'continue') {
            return [
                'reply' => nectra_chat_lead_prompt($state),
                'quick_replies' => ['Cancel'],
                'state' => $state,
            ];
        }

        $intent = nectra_chat_intent_reply($text);

        if ($intent === '__CANCEL_LEAD__') {
            $state = ['mode' => 'idle', 'lead' => []];
            return [
                'reply' => 'Theek hai, lead form cancel kar diya. Aur kya help chahiye?',
                'quick_replies' => $quick,
                'state' => $state,
            ];
        }

        // Allow info questions during lead flow without breaking
        if ($intent && $intent !== '__START_LEAD__') {
            $state['lead']['message'] = ($state['lead']['message'] ?? '') . ' | ' . $message;
            $resume = nectra_chat_lead_prompt($state);
            return [
                'reply' => $intent . "\n\n---\n" . $resume,
                'quick_replies' => ['Continue Lead', 'Cancel'],
                'state' => $state,
            ];
        }

        return nectra_chat_advance_lead($text, $state);
    }

    // Try to parse one-shot lead (name + email in one message)
    $parsed = nectra_chat_extract_lead_from_message($text);
    if ($parsed['email'] && ($parsed['name'] || nectra_chat_is_phone($text))) {
        if (!$parsed['name']) {
            $parsed['name'] = 'Website Visitor';
        }
        $result = nectra_chat_submit_lead($parsed);
        if ($result['success']) {
            $state = ['mode' => 'idle', 'lead' => []];
            return [
                'reply' => $result['reply'],
                'quick_replies' => $quick,
                'state' => $state,
                'lead_submitted' => true,
            ];
        }
    }

    if (nectra_chat_is_email($text)) {
        $state = [
            'mode' => 'lead_name',
            'lead' => ['email' => trim($message), 'message' => 'Email first from user'],
        ];
        return [
            'reply' => "Email mil gaya: {$message}\nAb apna **naam** bataiye:",
            'quick_replies' => ['Cancel'],
            'state' => $state,
        ];
    }

    $intent = nectra_chat_intent_reply($text);

    if ($intent === '__START_LEAD__') {
        $state = ['mode' => 'lead_name', 'lead' => ['message' => $message]];
        return [
            'reply' => "Bahut badhiya! Free consultation ke liye kuch details chahiye.\n\nSabse pehle apna **poora naam** bataiye:",
            'quick_replies' => ['Cancel'],
            'state' => $state,
        ];
    }

    if ($intent === '__CANCEL_LEAD__') {
        return [
            'reply' => 'Koi lead form active nahi hai. Main kaise madad kar sakta hoon?',
            'quick_replies' => $quick,
            'state' => $state,
        ];
    }

    if ($intent) {
        return [
            'reply' => $intent,
            'quick_replies' => $quick,
            'state' => $state,
        ];
    }

    return [
        'reply' => "Main samajh gaya aapki query — lekin thoda aur specific batayein.\n\n" .
            "Main help kar sakta hoon:\n" .
            "• **Services** — SEO, Ads, Web, AI\n" .
            "• **Pricing** — packages & quotes\n" .
            "• **Contact** — phone, WhatsApp, email\n" .
            "• **Free Audit** — lead submit\n\n" .
            "Ya seedha phone karein: " . NECTRA_CHAT_PHONE,
        'quick_replies' => $quick,
        'state' => $state,
    ];
}

function nectra_chat_lead_prompt(array $state): string
{
    $mode = $state['mode'] ?? 'idle';
    $prompts = [
        'lead_name' => 'Lead form jaari hai — apna **naam** bataiye (ya \"Cancel\" likhein):',
        'lead_email' => 'Ab apna **email** bataiye:',
        'lead_phone' => 'Apna **phone number** bataiye (optional — \"skip\" likh sakte hain):',
        'lead_service' => 'Kaun si **service** chahiye? (SEO, Web Dev, AI, Ads, etc.):',
    ];
    return $prompts[$mode] ?? '';
}

function nectra_chat_advance_lead(string $text, array &$state): array
{
    $quick = ['Services', 'Pricing', 'Contact', 'Cancel'];
    $lead = &$state['lead'];
    $mode = $state['mode'];

    if ($text === 'continue lead' || $text === 'continue') {
        return [
            'reply' => nectra_chat_lead_prompt($state),
            'quick_replies' => ['Cancel'],
            'state' => $state,
        ];
    }

    switch ($mode) {
        case 'lead_name':
            if (strlen($text) < 2) {
                return [
                    'reply' => 'Valid naam likhiye (kam se kam 2 characters):',
                    'quick_replies' => ['Cancel'],
                    'state' => $state,
                ];
            }
            $lead['name'] = ucwords($text);
            $state['mode'] = 'lead_email';
            return [
                'reply' => "Shukriya {$lead['name']}! Ab apna **email address** bataiye:",
                'quick_replies' => ['Cancel'],
                'state' => $state,
            ];

        case 'lead_email':
            if (!nectra_chat_is_email($text)) {
                return [
                    'reply' => 'Valid email chahiye (jaise: name@company.com):',
                    'quick_replies' => ['Cancel'],
                    'state' => $state,
                ];
            }
            $lead['email'] = trim($text);
            $state['mode'] = 'lead_phone';
            return [
                'reply' => "Perfect! **Phone number** bataiye (optional — skip karne ke liye \"skip\" likhein):",
                'quick_replies' => ['Skip', 'Cancel'],
                'state' => $state,
            ];

        case 'lead_phone':
            if (in_array($text, ['skip', 'na', 'n/a', 'none', 'no'], true)) {
                $lead['phone'] = '';
            } elseif (nectra_chat_is_phone($text)) {
                $lead['phone'] = preg_replace('/\s+/', '', $text);
            } else {
                return [
                    'reply' => 'Valid 10-digit phone likhiye ya \"skip\" likhein:',
                    'quick_replies' => ['Skip', 'Cancel'],
                    'state' => $state,
                ];
            }
            $state['mode'] = 'lead_service';
            return [
                'reply' => "Kaun si **service** mein interested hain?\n(SEO, Local SEO, Google Ads, Web Development, AI Automation, etc.)",
                'quick_replies' => ['SEO', 'Web Development', 'AI Automation', 'Cancel'],
                'state' => $state,
            ];

        case 'lead_service':
            $lead['service'] = ucwords($text);
            if (empty($lead['message'])) {
                $lead['message'] = 'Lead via chatbot — service: ' . $lead['service'];
            }
            $result = nectra_chat_submit_lead($lead);
            $state = ['mode' => 'idle', 'lead' => []];
            return [
                'reply' => $result['reply'] ?? 'Thank you! Team contact karegi.',
                'quick_replies' => ['Services', 'Pricing', 'Contact'],
                'state' => $state,
                'lead_submitted' => !empty($result['success']),
            ];
    }

    $state = ['mode' => 'idle', 'lead' => []];
    return [
        'reply' => nectra_chat_welcome(),
        'quick_replies' => $quick,
        'state' => $state,
    ];
}
