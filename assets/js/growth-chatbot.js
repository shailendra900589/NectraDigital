(function () {
    const orphanToggle = document.getElementById('nectra-chatbot-toggle');
    if (orphanToggle) {
        orphanToggle.remove();
    }

    if (document.getElementById('nectra-chatbot-panel')) {
        return;
    }

    const welcome = 'Hi! I am Nectra AI. How can I help you grow today? Ask about our services or leave your details for a free consultation.';
    const panel = document.createElement('div');
    panel.id = 'nectra-chatbot-panel';
    panel.innerHTML = `
        <div class="ncb-header">
            <span><i class="fas fa-robot me-2"></i>Nectra AI Assistant</span>
            <button type="button" class="ncb-close" id="ncbClose" aria-label="Close chat">&times;</button>
        </div>
        <div class="ncb-messages" id="ncbMessages"></div>
        <div class="ncb-input-row">
            <input type="text" id="ncbInput" placeholder="Ask or type your email..." autocomplete="off">
            <button type="button" id="ncbSend">Send</button>
        </div>`;
    document.body.appendChild(panel);

    window.NECTRA_CHATBOT_INTEGRATED = true;
    window.openNectraChatbot = function () {
        panel.classList.add('open');
    };
    window.closeNectraChatbot = function () {
        panel.classList.remove('open');
    };

    const closeBtn = panel.querySelector('#ncbClose');
    if (closeBtn) {
        closeBtn.addEventListener('click', () => panel.classList.remove('open'));
    }

    const messages = panel.querySelector('#ncbMessages');
    let collectingLead = false;
    let leadData = {};

    function addMsg(text, who) {
        const d = document.createElement('div');
        d.className = 'ncb-msg ' + who;
        d.textContent = text;
        messages.appendChild(d);
        messages.scrollTop = messages.scrollHeight;
    }

    addMsg(welcome, 'bot');

    function botReply(input) {
        const lower = input.toLowerCase();
        if (/seo|marketing|development|design|automation/.test(lower)) {
            return 'We offer full-stack digital growth: SEO, ads, web & software development, AI automation, and branding. Would you like a free audit? Share your name and email.';
        }
        if (/price|cost|quote|proposal/.test(lower)) {
            return 'Pricing depends on scope. Share your name, email, and service interest — our team will send a custom proposal within 24 hours.';
        }
        if (/contact|call|meet|consult/.test(lower)) {
            collectingLead = true;
            return 'Great! Please share: your name, email, and what service you need (e.g. SEO in Lucknow).';
        }
        collectingLead = true;
        return 'I can connect you with our experts. Please share your name and email, plus your service interest.';
    }

    async function sendLead() {
        try {
            const res = await fetch('/api/chat-lead.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(leadData),
            });
            const j = await res.json();
            addMsg(j.reply || 'Thank you! We will be in touch.', 'bot');
        } catch (e) {
            addMsg('Thank you! Our team will contact you soon.', 'bot');
        }
        collectingLead = false;
        leadData = {};
    }

    function handleSend() {
        const input = document.getElementById('ncbInput');
        const text = input.value.trim();
        if (!text) return;
        addMsg(text, 'user');
        input.value = '';

        if (collectingLead) {
            if (!leadData.name) { leadData.name = text; addMsg('Thanks! What is your email?', 'bot'); return; }
            if (!leadData.email) {
                leadData.email = text;
                leadData.message = text;
                sendLead();
                return;
            }
        }

        if (text.includes('@') && text.includes('.')) {
            leadData.email = text;
            if (!leadData.name) leadData.name = 'Website Visitor';
            leadData.service = 'Chatbot Lead';
            sendLead();
            return;
        }

        addMsg(botReply(text), 'bot');
    }

    document.getElementById('ncbSend').addEventListener('click', handleSend);
    document.getElementById('ncbInput').addEventListener('keydown', (e) => { if (e.key === 'Enter') handleSend(); });
})();
