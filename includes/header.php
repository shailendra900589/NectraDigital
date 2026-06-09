<?php 
require_once __DIR__ . '/config.php'; 
require_once __DIR__ . '/text-utils.php';
require_once __DIR__ . '/seo-data.php';
require_once __DIR__ . '/growth/helpers.php';
require_once __DIR__ . '/i18n.php';

global $page_title, $page_desc, $page_img, $page_keys, $noindex, $og_type, $page_schema, $canonical_url;

$nectra_lang = nectra_get_user_lang();

$request_uri = strtok($_SERVER["REQUEST_URI"] ?? '/', '?');
$clean_uri = str_replace('.php', '', $request_uri); 
if (!empty($canonical_url)) {
    $final_url = rtrim($canonical_url, '/');
} else {
    $final_url = trim(SITE_URL . $clean_uri, '/'); 
}

$canonical_href = nectra_lang_url($final_url, $nectra_lang);

$default_title = "Best SEO & Digital Marketing in India | Nectra Digital";
$default_desc  = "Best SEO company in India — search engine optimization, AI automation, digital marketing & software development. 200+ projects. Free audit.";
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
    $meta_title = ge_trim_seo_title(trim($page_title), $site_name_safe, 50, 55);
} else {
    $meta_title = ge_trim_seo_title($default_title, $site_name_safe, 50, 55);
}

$meta_desc = ge_trim_seo_description((!empty($page_desc)) ? $page_desc : $default_desc, 120, 160);
$meta_keys = (!empty($page_keys)) ? $page_keys : "SEO Company India, Best SEO Company India, SEO Services India, Digital Marketing Agency India, AI Automation Services, Web Development Agency";
$meta_og_type = (!empty($og_type)) ? $og_type : 'website';
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars(nectra_html_lang()); ?>" dir="<?php echo nectra_html_dir(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title><?php echo nectra_display_text($meta_title); ?></title>
    <meta name="description" content="<?php echo nectra_display_text($meta_desc); ?>">
    <link rel="canonical" href="<?php echo htmlspecialchars($canonical_href); ?>">
    <?php nectra_output_hreflang_tags($final_url); ?>
    <meta name="keywords" content="<?php echo htmlspecialchars($meta_keys); ?>">
    <meta name="author" content="<?php echo FOUNDER_NAME; ?>">
    <meta name="robots" content="<?php echo !empty($noindex) ? 'noindex, nofollow' : 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1'; ?>">
    
    <?php if (!empty($noindex)): ?>
    <meta name="googlebot" content="noindex, nofollow">
    <?php endif; ?>

    <link rel="icon" type="image/png" href="<?php echo SITE_URL; ?>/assets/favicon_io/favicon-32x32.png">
    <link rel="manifest" href="<?php echo SITE_URL; ?>/assets/favicon_io/site.webmanifest">
    <link rel="alternate" type="text/plain" href="<?php echo SITE_URL; ?>/llms.txt" title="LLM Context">
    <link rel="alternate" type="application/rss+xml" title="Nectra Digital RSS Feed" href="<?php echo SITE_URL; ?>/rss.xml" />
    <link rel="alternate" type="application/rss+xml" title="Nectra Digital Discover Feed" href="<?php echo SITE_URL; ?>/discover-feed.xml" />
    <link rel="alternate" type="application/atom+xml" title="Nectra Digital Atom Feed" href="<?php echo SITE_URL; ?>/atom.xml" />
    <link rel="alternate" type="application/rss+xml" title="Nectra Digital News" href="<?php echo SITE_URL; ?>/news-sitemap.xml" />

    <?php if (empty($noindex)): ?>
    <meta name="googlebot" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="bingbot" content="index, follow">
    <meta name="news_keywords" content="<?php echo htmlspecialchars($meta_keys); ?>">
    <meta name="syndication-source" content="<?php echo htmlspecialchars($final_url); ?>">
    <?php endif; ?>

    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/i18n.css?v=3">
    <script>window.NectraI18n = <?php echo json_encode(nectra_i18n_config_js(), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP); ?>;</script>
    <?php if ($nectra_lang !== 'en'):
        $nectra_gcode = nectra_google_lang_code($nectra_lang);
        $nectra_cdom = nectra_cookie_domain();
    ?>
    <script>
    (function(g,d){
        var v='/en/'+g;
        document.cookie='googtrans='+v+';path=/;max-age=31536000;SameSite=Lax';
        document.cookie='googtrans='+v+';path=/;domain='+d+';max-age=31536000;SameSite=Lax';
        document.cookie='nectra_lang='+encodeURIComponent(<?php echo json_encode($nectra_lang); ?>)+';path=/;max-age=31536000;SameSite=Lax';
    })(<?php echo json_encode($nectra_gcode); ?>, <?php echo json_encode($nectra_cdom); ?>);
    </script>
    <?php endif; ?>
    <script src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    
    <meta property="og:site_name" content="<?php echo $site_name_safe; ?>" />
    <meta property="og:type" content="<?php echo $meta_og_type; ?>" />
    <meta property="og:title" content="<?php echo nectra_display_text($meta_title); ?>" />
    <meta property="og:description" content="<?php echo nectra_display_text($meta_desc); ?>" />
    <meta property="og:url" content="<?php echo htmlspecialchars($canonical_href); ?>" />
    <meta property="og:image" content="<?php echo htmlspecialchars($meta_img); ?>" />
    <meta property="og:image:alt" content="<?php echo $site_name_safe; ?> - SEO & Digital Marketing Agency India" />
    <meta property="og:locale" content="en_IN" />
    <meta property="og:locale:alternate" content="hi_IN" />
    <meta property="og:locale:alternate" content="bn_IN" />
    <meta property="og:locale:alternate" content="ta_IN" />
    <?php if (defined('NECTRA_FACEBOOK_URL')): ?>
    <meta property="article:publisher" content="<?php echo NECTRA_FACEBOOK_URL; ?>" />
    <?php endif; ?>

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@nectradigital">
    <meta name="twitter:title" content="<?php echo nectra_display_text($meta_title); ?>">
    <meta name="twitter:description" content="<?php echo nectra_display_text($meta_desc); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($meta_img); ?>">
    <meta name="twitter:image:alt" content="<?php echo $site_name_safe; ?>">

    <?php
    require_once __DIR__ . '/site-contact.php';
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
    <?php if (defined('NECTRA_FB_PIXEL_ID') && NECTRA_FB_PIXEL_ID !== ''): ?>
    <script>
    !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
    n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '<?php echo htmlspecialchars(NECTRA_FB_PIXEL_ID, ENT_QUOTES); ?>');
    fbq('track', 'PageView');
    </script>
    <?php endif; ?>
</head>
<body<?php echo $nectra_lang !== 'en' ? ' class="nectra-translated nectra-lang-' . htmlspecialchars($nectra_lang) . '"' : ''; ?>>

<div id="particles-js"></div>

<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center notranslate" href="/" aria-label="Nectra Digital Home">
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
                <li class="nav-item">
                    <?php include __DIR__ . '/language-switcher.php'; ?>
                </li>
                <li class="nav-item ms-lg-3">
                    <a href="/contact" class="btn btn-nectra btn-sm">FREE AUDIT <i class="fas fa-bolt"></i></a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<br><br>
