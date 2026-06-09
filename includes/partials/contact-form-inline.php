<?php
/**
 * Inline lead form for service / city landing pages.
 * Expects: $form_service (string), optional $form_city (string)
 */
$form_service = $form_service ?? 'Consultation';
$form_city = $form_city ?? '';
$form_instance = $form_instance ?? 'main';
$form_id = 'serviceCityForm_' . substr(md5($form_service . $form_city . $form_instance), 0, 8);
?>
<div class="svc-inline-form-wrap">
    <form id="<?php echo htmlspecialchars($form_id); ?>" class="svc-inline-form" novalidate>
        <div class="mb-3">
            <label class="form-label text-white-50 small mb-1">Full Name</label>
            <input type="text" name="name" class="form-control form-control-sm bg-dark text-white border-secondary" required autocomplete="name">
        </div>
        <div class="mb-3">
            <label class="form-label text-white-50 small mb-1">Email</label>
            <input type="email" name="email" class="form-control form-control-sm bg-dark text-white border-secondary" required autocomplete="email">
        </div>
        <div class="mb-3">
            <label class="form-label text-white-50 small mb-1">Phone</label>
            <input type="tel" name="phone" class="form-control form-control-sm bg-dark text-white border-secondary" autocomplete="tel">
        </div>
        <input type="hidden" name="service" value="<?php echo htmlspecialchars($form_service); ?>">
        <input type="hidden" name="city" value="<?php echo htmlspecialchars($form_city); ?>">
        <div class="mb-3">
            <label class="form-label text-white-50 small mb-1">Message</label>
            <textarea name="message" class="form-control form-control-sm bg-dark text-white border-secondary" rows="3" placeholder="Tell us about your project..." required></textarea>
        </div>
        <button type="submit" class="btn btn-nectra w-100 btn-sm">Get Free Proposal</button>
        <div class="form-response mt-2 small"></div>
    </form>
</div>
<script>
(function () {
    const form = document.getElementById(<?php echo json_encode($form_id); ?>);
    if (!form) return;
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }
        const btn = form.querySelector('button[type="submit"]');
        const res = form.querySelector('.form-response');
        const original = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = 'Sending...';
        fetch('/process.php', { method: 'POST', body: new FormData(form) })
            .then(r => r.json())
            .then(data => {
                res.innerHTML = data.status === 'success'
                    ? '<span class="text-success">' + data.message + '</span>'
                    : '<span class="text-danger">' + (data.message || 'Error') + '</span>';
                if (data.status === 'success') form.reset();
            })
            .catch(() => { res.innerHTML = '<span class="text-danger">Connection error. Try again.</span>'; })
            .finally(() => { btn.disabled = false; btn.innerHTML = original; });
    });
})();
</script>
