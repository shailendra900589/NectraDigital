<?php
/**
 * Section heading — title + optional subtitle only (no orphan eyebrow labels).
 *
 * @param string $titleHtml Safe HTML for the main heading
 * @param string|null $subtitle Optional muted line below
 * @param string $align center|left
 */
function nd_render_section_head(string $titleHtml, ?string $subtitle = null, string $align = 'center'): void
{
    $alignClass = $align === 'left' ? 'text-start' : 'text-center';
    echo '<div class="nd-section-head ' . $alignClass . ' mb-4">';
    echo '<h2 class="nd-section-title h3 fw-bold text-dark mb-2">' . $titleHtml . '</h2>';
    if ($subtitle !== null && $subtitle !== '') {
        $subClass = $align === 'left' ? '' : ' mx-auto';
        echo '<p class="text-muted nd-section-sub mb-0' . $subClass . '">' . htmlspecialchars($subtitle) . '</p>';
    }
    echo '</div>';
}
