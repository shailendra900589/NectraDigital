(function () {
    const orphanToggle = document.getElementById('nectra-chatbot-toggle');
    if (orphanToggle) orphanToggle.remove();
    if (document.getElementById('nectra-chatbot-panel')) return;

    const API = (window.NECTRA_CHATBOT && window.NECTRA_CHATBOT.apiUrl) || '/api/chatbot.php';
    let isSending = false;

    const panel = document.createElement('div');
    panel.id = 'nectra-chatbot-panel';
    panel.innerHTML = `
        <div class="ncb-header">
            <span><i class="fas fa-robot me-2"></i>Nectra AI Assistant</span>
            <div class="ncb-header-actions">
                <button type="button" class="ncb-icon-btn" id="ncbReset" title="New chat" aria-label="Reset chat"><i class="fas fa-redo-alt"></i></button>
                <button type="button" class="ncb-close" id="ncbClose" aria-label="Close chat">&times;</button>
            </div>
        </div>
        <div class="ncb-messages" id="ncbMessages"></div>
        <div class="ncb-quick" id="ncbQuick"></div>
        <div class="ncb-input-row">
            <input type="text" id="ncbInput" placeholder="Hindi ya English mein likhein..." autocomplete="off" maxlength="500">
            <button type="button" id="ncbSend" aria-label="Send message"><i class="fas fa-paper-plane"></i></button>
        </div>`;
    document.body.appendChild(panel);

    window.NECTRA_CHATBOT_INTEGRATED = true;
    window.openNectraChatbot = () => {
        panel.classList.add('open');
        document.getElementById('ncbInput').focus();
    };
    window.closeNectraChatbot = () => panel.classList.remove('open');

    const messagesEl = document.getElementById('ncbMessages');
    const quickEl = document.getElementById('ncbQuick');
    const inputEl = document.getElementById('ncbInput');
    const sendBtn = document.getElementById('ncbSend');

    document.getElementById('ncbClose').addEventListener('click', () => panel.classList.remove('open'));
    document.getElementById('ncbReset').addEventListener('click', () => resetChat());

    function formatReply(text) {
        let html = text
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
            .replace(/(https?:\/\/[^\s<]+)/g, '<a href="$1" target="_blank" rel="noopener">$1</a>');
        return html.replace(/\n/g, '<br>');
    }

    function addMsg(text, who) {
        const d = document.createElement('div');
        d.className = 'ncb-msg ' + who;
        if (who === 'bot') {
            d.innerHTML = formatReply(text);
        } else {
            d.textContent = text;
        }
        messagesEl.appendChild(d);
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function showTyping() {
        let t = document.getElementById('ncbTyping');
        if (!t) {
            t = document.createElement('div');
            t.id = 'ncbTyping';
            t.className = 'ncb-msg bot ncb-typing';
            t.innerHTML = '<span></span><span></span><span></span>';
            messagesEl.appendChild(t);
        }
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function hideTyping() {
        const t = document.getElementById('ncbTyping');
        if (t) t.remove();
    }

    function renderQuickReplies(items) {
        quickEl.innerHTML = '';
        if (!items || !items.length) return;
        items.forEach(label => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'ncb-quick-btn';
            btn.textContent = label;
            btn.addEventListener('click', () => sendMessage(label));
            quickEl.appendChild(btn);
        });
    }

    async function apiCall(body) {
        const res = await fetch(API, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body),
        });
        if (!res.ok) throw new Error('Network error');
        return res.json();
    }

    async function loadWelcome() {
        try {
            const data = await apiCall({ action: 'welcome' });
            addMsg(data.reply || 'Hello!', 'bot');
            renderQuickReplies(data.quick_replies || []);
        } catch (e) {
            addMsg('Namaste! Main Nectra AI hoon. Services, pricing ya contact ke baare mein poochiye.', 'bot');
            renderQuickReplies(['Services', 'Pricing', 'Contact', 'Free Audit']);
        }
    }

    async function resetChat() {
        messagesEl.innerHTML = '';
        quickEl.innerHTML = '';
        try {
            const data = await apiCall({ action: 'reset' });
            addMsg(data.reply, 'bot');
            renderQuickReplies(data.quick_replies || []);
        } catch (e) {
            loadWelcome();
        }
    }

    async function sendMessage(text) {
        const msg = (text || inputEl.value).trim();
        if (!msg || isSending) return;
        isSending = true;
        sendBtn.disabled = true;
        addMsg(msg, 'user');
        inputEl.value = '';
        quickEl.innerHTML = '';
        showTyping();

        try {
            const data = await apiCall({ action: 'chat', message: msg });
            hideTyping();
            if (data.reply) addMsg(data.reply, 'bot');
            renderQuickReplies(data.quick_replies || []);
        } catch (e) {
            hideTyping();
            addMsg('Connection error. Phir se try karein ya call karein: +91 7678387759', 'bot');
            renderQuickReplies(['Contact', 'Services']);
        } finally {
            isSending = false;
            sendBtn.disabled = false;
            inputEl.focus();
        }
    }

    sendBtn.addEventListener('click', () => sendMessage());
    inputEl.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    loadWelcome();
})();
