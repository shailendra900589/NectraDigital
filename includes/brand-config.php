<?php
/**
 * Brand positioning, CTAs, and shared enquiry options (development-first).
 */
if (defined('NECTRA_BRAND_CONFIG_LOADED')) {
    return;
}
define('NECTRA_BRAND_CONFIG_LOADED', true);

define('NECTRA_TAGLINE', 'Engineering Digital Products That Grow Businesses.');
define('NECTRA_POSITIONING', 'Software, website and mobile app development for startups, SMEs and enterprises.');

define('NECTRA_CTA_PRIMARY_LABEL', 'Start a Project');
define('NECTRA_CTA_PRIMARY_URL', '/contact?service=Start+Project');
define('NECTRA_CTA_SECONDARY_LABEL', 'Talk to an Expert');
define('NECTRA_CTA_SECONDARY_URL', '/contact?service=Consultation');

/** Service select options — development first (field name `service` unchanged for process.php). */
function nectra_enquiry_service_options(): array
{
    return [
        'Software Development' => 'Software Development',
        'Website Development' => 'Website Development',
        'Mobile App Development' => 'Mobile App Development',
        'Web Application' => 'Web Application',
        'SaaS Development' => 'SaaS Development',
        'E-commerce Development' => 'E-commerce Development',
        'AI & Automation' => 'AI & Automation',
        'SEO Services' => 'SEO Services',
        'Digital Marketing' => 'Digital Marketing',
        'Other' => 'Other',
    ];
}

function nectra_render_service_select(string $name = 'service', bool $required = true, string $id = ''): void
{
    $idAttr = $id !== '' ? ' id="' . htmlspecialchars($id) . '"' : '';
    $req = $required ? ' required' : '';
    echo '<select name="' . htmlspecialchars($name) . '" class="form-select"' . $idAttr . $req . '>';
    echo '<option value="" disabled selected>Select service</option>';
    foreach (nectra_enquiry_service_options() as $value => $label) {
        echo '<option value="' . htmlspecialchars($value) . '">' . htmlspecialchars($label) . '</option>';
    }
    echo '</select>';
}

/** Contact page & main enquiry forms — software, website, mobile app first. */
function nectra_project_requirement_options(): array
{
    return [
        'Software Development' => 'Custom Software Development',
        'Web Development' => 'Custom Website & E-commerce',
        'Mobile App Development' => 'Mobile App Development (Android & iOS)',
        'Free SEO Audit' => 'Free Technical SEO Audit',
        'SEO Services' => 'Search Engine Optimization (National / Local)',
        'Performance Marketing' => 'Google Ads & Paid Media (PPC)',
        'AI Automation' => 'AI Workflow Automation & Bots',
        'Full Digital Transformation' => 'Complete Digital Growth Suite',
    ];
}

function nectra_render_project_requirement_select(string $name = 'service', bool $required = true, string $id = 'service', ?string $selectedValue = null): void
{
    $idAttr = $id !== '' ? ' id="' . htmlspecialchars($id) . '"' : '';
    $req = $required ? ' required' : '';
    echo '<select name="' . htmlspecialchars($name) . '" class="form-select"' . $idAttr . $req . '>';
    echo '<option value="" disabled' . ($selectedValue === null || $selectedValue === '' ? ' selected' : '') . '>Select Requirement...</option>';
    foreach (nectra_project_requirement_options() as $value => $label) {
        $sel = ($selectedValue !== null && $selectedValue !== '' && $value === $selectedValue) ? ' selected' : '';
        echo '<option value="' . htmlspecialchars($value) . '"' . $sel . '>' . htmlspecialchars($label) . '</option>';
    }
    echo '</select>';
}
