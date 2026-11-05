<?php
/**
 * Nectra Digital — Reusable FAQ Accordion Component
 * Auto-injects matching Google FAQPage Schema
 */
$faqList = $faqList ?? [];
$faqAccordionId = $faqAccordionId ?? 'faqAccordion_' . substr(md5(uniqid()), 0, 6);
$faqIsDark = !empty($faqIsDark);

if (!empty($faqList) && is_array($faqList)):
?>
<div class="accordion <?php echo $faqIsDark ? 'nd-accordion-dark' : 'nd-accordion'; ?>" id="<?php echo htmlspecialchars($faqAccordionId); ?>">
    <?php foreach ($faqList as $idx => $item): 
        $collapseId = $faqAccordionId . '_item_' . $idx;
        $isFirst = ($idx === 0);
    ?>
    <div class="accordion-item">
        <h3 class="accordion-header" id="heading_<?php echo $collapseId; ?>">
            <button class="accordion-button <?php echo !$isFirst ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_<?php echo $collapseId; ?>" aria-expanded="<?php echo $isFirst ? 'true' : 'false'; ?>" aria-controls="collapse_<?php echo $collapseId; ?>">
                <?php echo htmlspecialchars($item['q']); ?>
            </button>
        </h3>
        <div id="collapse_<?php echo $collapseId; ?>" class="accordion-collapse collapse <?php echo $isFirst ? 'show' : ''; ?>" aria-labelledby="heading_<?php echo $collapseId; ?>" data-bs-parent="#<?php echo htmlspecialchars($faqAccordionId); ?>">
            <div class="accordion-body">
                <?php echo nl2br(htmlspecialchars($item['a'])); ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php 
    if (function_exists('output_faq_schema')) {
        output_faq_schema($faqList);
    }
endif;
?>
