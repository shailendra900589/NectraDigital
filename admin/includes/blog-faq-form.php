<?php
/**
 * Reusable FAQ fields for blog create/edit admin forms.
 * Expects $blog_faqs as array of ['q'=>'','a'=>''] (optional).
 */
if (!isset($blog_faqs) || !is_array($blog_faqs)) {
    $blog_faqs = [];
}
if (empty($blog_faqs)) {
    $blog_faqs = [['q' => '', 'a' => '']];
}
?>
<div class="col-12 mb-3">
    <label class="d-block mb-2">FAQs (SEO + FAQPage schema)</label>
    <p class="text-white-50 small mb-2">Add questions &amp; answers for this post. Leave empty to show default EEAT FAQs on the live article.</p>
    <div id="blogFaqRepeater" class="border border-secondary rounded p-3" style="background:rgba(20,20,20,0.5);">
        <?php foreach ($blog_faqs as $faq): ?>
        <div class="row g-2 mb-3 blog-faq-row">
            <div class="col-md-5">
                <input type="text" name="faq_q[]" class="form-control bg-dark text-white" placeholder="Question" value="<?php echo htmlspecialchars($faq['q'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="col-md-6">
                <textarea name="faq_a[]" class="form-control bg-dark text-white" rows="2" placeholder="Answer"><?php echo htmlspecialchars($faq['a'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
            <div class="col-md-1 d-flex align-items-start">
                <button type="button" class="btn btn-sm btn-outline-danger w-100 blog-faq-remove" title="Remove">&times;</button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <button type="button" class="btn btn-sm btn-outline-info mt-2" id="blogFaqAdd">+ Add FAQ</button>
</div>
<script>
(function () {
    var repeater = document.getElementById('blogFaqRepeater');
    if (!repeater) return;

    function bindRemove(btn) {
        btn.addEventListener('click', function () {
            var rows = repeater.querySelectorAll('.blog-faq-row');
            if (rows.length <= 1) {
                rows[0].querySelectorAll('input, textarea').forEach(function (el) { el.value = ''; });
                return;
            }
            btn.closest('.blog-faq-row').remove();
        });
    }

    repeater.querySelectorAll('.blog-faq-remove').forEach(bindRemove);

    document.getElementById('blogFaqAdd').addEventListener('click', function () {
        var row = document.createElement('div');
        row.className = 'row g-2 mb-3 blog-faq-row';
        row.innerHTML = '<div class="col-md-5"><input type="text" name="faq_q[]" class="form-control bg-dark text-white" placeholder="Question"></div>'
            + '<div class="col-md-6"><textarea name="faq_a[]" class="form-control bg-dark text-white" rows="2" placeholder="Answer"></textarea></div>'
            + '<div class="col-md-1 d-flex align-items-start"><button type="button" class="btn btn-sm btn-outline-danger w-100 blog-faq-remove" title="Remove">&times;</button></div>';
        repeater.appendChild(row);
        bindRemove(row.querySelector('.blog-faq-remove'));
    });
})();
</script>
