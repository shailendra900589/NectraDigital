<?php 
require_once 'config.php'; 
require_once 'seo-data.php';

global $page_title, $page_desc, $page_img, $page_keys, $noindex, $og_type, $page_schema;

$request_uri = strtok($_SERVER["REQUEST_URI"], '?');
$clean_uri = str_replace('.php', '', $request_uri); 
$final_url = trim(SITE_URL . $clean_uri, '/'); 

$default_title = "Nectra Digital | Best SEO Company India & AI Automation Agency";
$default_desc  = "Nectra Digital is the best SEO company in India offering search engine optimization services, AI automation, digital marketing, web development, and software development. 5+ years expertise, 200+ projects.";
$default_img   = SITE_URL . "/assets/images/logo.png"; 

$site_name_safe = defined('SITE_NAME') ? SITE_NAME : 'Nectra Digital';

if (isset($page_img) && !empty($page_img)) {
    if (strpos($page_img, 'http') === 0) {
        $meta_img = $page_img;
    } elseif (strpos($page_img, 'assets/') !== false) {
        $meta_img = SITE_URL . '/' . ltrim($page_img, '/');
    } else {
        $meta_img = SITE_URL . '/assets/uploads/' . $page_img;
    }
} else {
    $meta_img = $default_img;
}

if (!empty($page_title)) {
    $meta_title = (strpos($page_title, 'Nectra Digital') !== false) ? $page_title : $page_title . " | " . $site_name_safe;
} else {
    $meta_title = $default_title;
}

$meta_desc = (!empty($page_desc)) ? $page_desc : $default_desc;
$meta_keys = (!empty($page_keys)) ? $page_keys : "SEO Company India, Best SEO Company India, SEO Services India, Digital Marketing Agency India, AI Automation Services, Web Development Agency";
$meta_og_type = (!empty($og_type)) ? $og_type : 'website';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title><?php echo htmlspecialchars($meta_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($meta_desc); ?>">
    <link rel="canonical" href="<?php echo htmlspecialchars($final_url); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($meta_keys); ?>">
    <meta name="author" content="<?php echo FOUNDER_NAME; ?>">
    <meta name="robots" content="<?php echo !empty($noindex) ? 'noindex, nofollow' : 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1'; ?>">
    
    <?php if (!empty($noindex)): ?>
    <meta name="googlebot" content="noindex, nofollow">
    <?php endif; ?>

    <link rel="icon" type="image/png" href="<?php echo SITE_URL; ?>/assets/favicon_io/favicon-32x32.png">
    <link rel="manifest" href="<?php echo SITE_URL; ?>/assets/favicon_io/site.webmanifest">
    <link rel="alternate" type="text/plain" href="<?php echo SITE_URL; ?>/llms.txt" title="LLM Context">
    <link rel="alternate" type="application/rss+xml" title="Nectra Digital Latest Intel" href="<?php echo SITE_URL; ?>/rss.xml" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    
    <meta property="og:site_name" content="<?php echo $site_name_safe; ?>" />
    <meta property="og:type" content="<?php echo $meta_og_type; ?>" />
    <meta property="og:title" content="<?php echo htmlspecialchars($meta_title); ?>" />
    <meta property="og:description" content="<?php echo htmlspecialchars($meta_desc); ?>" />
    <meta property="og:url" content="<?php echo htmlspecialchars($final_url); ?>" />
    <meta property="og:image" content="<?php echo htmlspecialchars($meta_img); ?>" />
    <meta property="og:image:alt" content="<?php echo $site_name_safe; ?> - SEO & Digital Marketing Agency India" />
    <meta property="og:locale" content="en_IN" />

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@nectradigital">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($meta_title); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($meta_desc); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($meta_img); ?>">
    <meta name="twitter:image:alt" content="<?php echo $site_name_safe; ?>">

    <?php
    $global_schemas = [get_organization_schema(), get_website_schema(), get_founder_schema()];
    if (!empty($page_schema) && is_array($page_schema)) {
        $global_schemas = array_merge($global_schemas, $page_schema);
    }
    output_schema_graph($global_schemas);
    ?>

    <script async src="https://www.googletagmanager.com/gtag/js?id=G-1S5L5N87KR"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-1S5L5N87KR');
    </script>

    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-TCZBG6K3');</script>
    
    <script>
    (function(c,l,a,r,i,t,y){
        c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
        t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i+"?ref=bwt";
        y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
    })(window, document, "clarity", "script", "vjpbvxww01");
    </script>
    
    <script async type="application/javascript" src="https://news.google.com/swg/js/v1/swg-basic.js"></script>
    <script>
      (self.SWG_BASIC = self.SWG_BASIC || []).push( basicSubscriptions => {
        basicSubscriptions.init({
          type: "NewsArticle",
          isPartOfType: ["Product"],
          isPartOfProductId: "CAow-93FDA:openaccess",
          clientOptions: { theme: "light", lang: "en" },
        });
      });
    </script>
</head>
<body>

<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TCZBG6K3" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<div id="particles-js"></div>

<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="/" aria-label="Nectra Digital Home">
            <span class="text-white fw-bold">NECTRA</span><span class="text-neon">DIGITAL</span>
        </a>
        
        <button class="navbar-toggler bg-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link" href="/">Home</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="/services" id="servicesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Services</a>
                    <ul class="dropdown-menu dropdown-menu-dark border-secondary" aria-labelledby="servicesDropdown">
                        <li><a class="dropdown-item" href="/seo-services">SEO Services</a></li>
                        <li><a class="dropdown-item" href="/local-seo-services">Local SEO</a></li>
                        <li><a class="dropdown-item" href="/ai-automation-services">AI Automation</a></li>
                        <li><a class="dropdown-item" href="/digital-marketing-services">Digital Marketing</a></li>
                        <li><a class="dropdown-item" href="/web-development-services">Web Development</a></li>
                        <li><hr class="dropdown-divider border-secondary"></li>
                        <li><a class="dropdown-item" href="/services">All Services</a></li>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link" href="/about">About</a></li>
                <li class="nav-item"><a class="nav-link" href="/portfolio">Portfolio</a></li>
                <li class="nav-item"><a class="nav-link" href="/hire-experts"><b>Hire Experts</b></a></li>
                <li class="nav-item"><a class="nav-link" href="/insights">Intel</a></li>
                <li class="nav-item"><a class="nav-link" href="/careers">Careers</a></li>
                <li class="nav-item ms-lg-3">
                    <a href="/contact" class="btn btn-nectra btn-sm">FREE AUDIT <i class="fas fa-bolt"></i></a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<br><br>
