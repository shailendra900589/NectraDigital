<?php 
require_once 'config.php'; 

// ==========================================
// 1. SMART URL & SEO LOGIC
// ==========================================

// FIX 1: GLOBAL SCOPE - Force header to see variables from post.php
global $page_title, $page_desc, $page_img, $page_keys;

// Clean URL Logic
$request_uri = strtok($_SERVER["REQUEST_URI"], '?');
$clean_uri = str_replace('.php', '', $request_uri);
$base_path = parse_url(SITE_URL, PHP_URL_PATH) ?: '';
if ($base_path && strpos($clean_uri, $base_path) === 0) {
    $clean_uri = substr($clean_uri, strlen($base_path));
}
$final_url = rtrim(SITE_URL . '/' . ltrim($clean_uri, '/'), '/'); 

// Default Values
$default_title = "Best Software Development Company in Lucknow | Nectra Digital";
$default_desc  = "Nectra Digital is a leading software development company in Lucknow offering custom web development, AI automation, mobile apps, and SEO services. 150+ projects delivered globally.";
$default_img   = SITE_URL . "/assets/images/logo.png"; 

// FIX 2: Define Site Name safely (Prevents errors if config.php misses it)
$site_name_safe = defined('SITE_NAME') ? SITE_NAME : 'Nectra Digital';

// --- DYNAMIC IMAGE LOGIC FOR POSTS ---
if (isset($page_img) && !empty($page_img)) {
    // Check if it's already a full URL
    if (strpos($page_img, 'http') === 0) {
        $meta_img = $page_img;
    } 
    // Check if it's a relative path with 'assets'
    elseif (strpos($page_img, 'assets/') !== false) {
        $meta_img = SITE_URL . '/' . ltrim($page_img, '/');
    }
    // Case: Just a filename
    else {
        $meta_img = SITE_URL . '/assets/uploads/' . $page_img;
    }
} else {
    $meta_img = $default_img;
}

// FIX 3: ROBUST TITLE LOGIC
// We use !empty() to catch cases where the title exists but is blank
if (!empty($page_title)) {
    $meta_title = $page_title . " | " . $site_name_safe;
} else {
    $meta_title = $default_title;
}

// FIX 4: Description & Keywords Logic
$meta_desc = (!empty($page_desc)) ? $page_desc : $default_desc;
$meta_keys = (!empty($page_keys)) ? $page_keys : "Nectra Digital, Digital Agency Lucknow, Tech Agency India";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title><?php echo $meta_title; ?></title>
    <meta name="description" content="<?php echo $meta_desc; ?>">
    <link rel="canonical" href="<?php echo $final_url; ?>">
    <meta name="keywords" content="<?php echo $meta_keys; ?>">

    <meta name="robots" content="max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <link rel="icon" type="image/png" href="<?php echo SITE_URL; ?>/assets/favicon_io/favicon-32x32.png">
    
    <link rel="manifest" href="<?php echo SITE_URL; ?>/manifest.json">
    <meta name="theme-color" content="#00E5FF">
    <link rel="alternate" type="application/rss+xml" title="Nectra Digital Blog" href="<?php echo SITE_URL; ?>/rss.xml">
    <link rel="alternate" type="application/feed+json" title="Nectra Digital JSON Feed" href="<?php echo SITE_URL; ?>/feed.json">
    <link rel="alternate" type="text/plain" href="<?php echo SITE_URL; ?>/llms.txt" title="LLM Context">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="alternate" type="application/rss+xml" title="Nectra Digital Latest Intel" href="<?php echo SITE_URL; ?>/rss.xml" />
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    
    <meta property="og:site_name" content="<?php echo $site_name_safe; ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="<?php echo $meta_title; ?>" />
    <meta property="og:description" content="<?php echo $meta_desc; ?>" />
    <meta property="og:url" content="<?php echo $final_url; ?>" />
    <meta property="og:image" content="<?php echo $meta_img; ?>" />

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo $meta_title; ?>">
    <meta name="twitter:description" content="<?php echo $meta_desc; ?>">
    <meta name="twitter:image" content="<?php echo $meta_img; ?>">

    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": ["Organization", "LocalBusiness"],
      "name": "Nectra Digital",
      "url": "<?php echo SITE_URL; ?>",
      "logo": "<?php echo SITE_URL; ?>/assets/images/logo.png",
      "image": "<?php echo SITE_URL; ?>/assets/images/logo.png",
      "description": "Leading software development company in Lucknow offering custom web development, AI automation, mobile apps, SEO, and digital marketing services to businesses globally.",
      "email": "contact@nectradigital.com",
      "telephone": "+91-7678387759",
      "foundingDate": "2021",
      "numberOfEmployees": {"@type": "QuantitativeValue", "minValue": 10, "maxValue": 50},
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "Lucknow",
        "addressLocality": "Lucknow",
        "addressRegion": "Uttar Pradesh",
        "postalCode": "226001",
        "addressCountry": "IN"
      },
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": "26.8467",
        "longitude": "80.9462"
      },
      "openingHoursSpecification": {
        "@type": "OpeningHoursSpecification",
        "dayOfWeek": ["Monday","Tuesday","Wednesday","Thursday","Friday"],
        "opens": "09:00",
        "closes": "18:00"
      },
      "contactPoint": {
        "@type": "ContactPoint",
        "contactType": "customer support",
        "email": "contact@nectradigital.com",
        "telephone": "+91-7678387759",
        "availableLanguage": ["English", "Hindi"]
      },
      "knowsAbout": ["Web Development", "AI Automation", "SEO", "Mobile App Development", "Custom Software", "Digital Marketing"],
      "sameAs": [
        "https://twitter.com/nectradigital", 
        "https://www.instagram.com/nectradigital",
        "https://www.linkedin.com/company/nectradigital"
      ]
    }
    </script>
    
    <!-- Analytics & Tracking (disabled: injecting broken WhatsApp bar via linked Google Ads tags)
    <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}</script>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-1S5L5N87KR" onload="gtag('js',new Date());gtag('config','G-1S5L5N87KR');"></script>
    <script>(function(c,l,a,r,i,t,y){c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i+"?ref=bwt";y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);})(window,document,"clarity","script","vjpbvxww01");</script>
    -->
    
    
    
    </head>
<body>

<div id="particles-js"></div>

<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="<?php echo SITE_URL; ?>/">
            <span class="text-white fw-bold">NECTRA</span><span class="text-neon">DIGITAL</span>
        </a>
        
        <button class="navbar-toggler bg-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link" href="<?php echo SITE_URL; ?>/">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo SITE_URL; ?>/services">Services</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo SITE_URL; ?>/portfolio">Portfolio</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo SITE_URL; ?>/hire-experts"><b>Hire Experts</b></a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo SITE_URL; ?>/insights">Intel</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo SITE_URL; ?>/careers">Careers</a></li>
                <li class="nav-item ms-lg-4">
                    <a href="<?php echo SITE_URL; ?>/contact" class="btn btn-nectra btn-sm">INITIALIZE <i class="fas fa-bolt"></i></a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div style="height: 70px;"></div>