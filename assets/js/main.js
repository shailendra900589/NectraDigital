document.addEventListener("DOMContentLoaded", function() {

    // Portfolio Filter
    const filterBtns = document.querySelectorAll('.filter-btn');
    const projectItems = document.querySelectorAll('.project-item');

    if(filterBtns.length > 0 && projectItems.length > 0) {
        filterBtns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                filterBtns.forEach(function(b) { b.classList.remove('active'); });
                btn.classList.add('active');
                var filterValue = btn.getAttribute('data-filter');
                projectItems.forEach(function(item) {
                    item.style.animation = 'none';
                    item.offsetHeight;
                    if (filterValue === 'all' || item.getAttribute('data-category') === filterValue) {
                        item.style.display = 'block';
                        item.style.animation = 'projectFadeIn 0.5s ease-in';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    }

    // Contact Form Handler (Homepage)
    const form = document.getElementById('contactForm');
    if(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = form.querySelector('button');
            const responseDiv = document.getElementById('responseMsg');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> SENDING...';
            btn.style.opacity = '0.7';
            btn.disabled = true;
            const formData = new FormData(this);
            fetch('process.php', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    window.location.href = "thank-you";
                } else {
                    responseDiv.innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-triangle"></i> ' + data.message + '</span>';
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                    btn.style.opacity = '1';
                }
            })
            .catch(error => {
                responseDiv.innerHTML = '<span class="text-danger">Connection error. Please try again.</span>';
                btn.innerHTML = originalText;
                btn.disabled = false;
                btn.style.opacity = '1';
            });
        });
    }

    // Contact Page Form Handler
    const contactPageForm = document.getElementById('contactPageForm');
    if(contactPageForm) {
        contactPageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!contactPageForm.checkValidity()) {
                e.stopPropagation();
                contactPageForm.classList.add('was-validated');
                return;
            }
            const btn = contactPageForm.querySelector('button[type="submit"]');
            const resp = document.getElementById('pageFormResponse');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> SENDING...';
            btn.disabled = true;
            const formData = new FormData(this);
            fetch('process.php', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    window.location.href = "thank-you";
                } else {
                    resp.innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-triangle"></i> ' + data.message + '</span>';
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            })
            .catch(error => {
                resp.innerHTML = '<span class="text-danger">Connection error. Please try again.</span>';
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        });
    }
});
