document.addEventListener("DOMContentLoaded", function() {
    
    const form = document.getElementById('contactForm');
    
    if(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btn = form.querySelector('button');
            const responseDiv = document.getElementById('responseMsg');
            const originalText = btn.innerHTML;
            
            // Loading State (Mars Speed)
            btn.innerHTML = 'CONNECTING...';
            btn.style.opacity = '0.7';
            btn.disabled = true;

            const formData = new FormData(this);

            fetch('process.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    responseDiv.innerHTML = `<span class="text-neon"><i class="fas fa-check-circle"></i> ${data.message}</span>`;
                    form.reset();
                } else {
                    responseDiv.innerHTML = `<span class="text-danger"><i class="fas fa-exclamation-triangle"></i> ${data.message}</span>`;
                }
            })
            .catch(error => {
                responseDiv.innerHTML = '<span class="text-danger">System Error. Check Console.</span>';
            })
            .finally(() => {
                btn.innerHTML = originalText;
                btn.style.opacity = '1';
                btn.disabled = false;
            });
        });
    }
});