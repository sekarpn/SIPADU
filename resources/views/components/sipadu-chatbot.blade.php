<style>
    /* TOMBOL FLOATING ROBOT MINIMALIS (TANPA GRADASI) */
    .sipadu-bot-launcher {
        position: fixed;
        right: 24px;
        bottom: 24px;
        z-index: 99980;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 10px 18px 10px 12px;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, .28);
        background: #237c4d;
        color: #ffffff;
        font-family: 'Plus Jakarta Sans', 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 10px 26px rgba(22, 54, 38, .22);
        transition: transform .22s ease, background .2s ease, box-shadow .22s ease;
    }

    .sipadu-bot-launcher:hover {
        background: #1b663f;
        transform: translateY(-3px);
        box-shadow: 0 14px 30px rgba(22, 54, 38, .28);
    }

    .sipadu-bot-launcher-icon {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .16);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* ANIMASI ROBOT INTERAKTIF */
    .sipadu-robot-svg {
        width: 24px;
        height: 24px;
        overflow: visible;
        animation: sipaduRobotFloat 3s ease-in-out infinite;
    }

    .sipadu-bot-launcher:hover .sipadu-robot-svg {
        animation: sipaduRobotWave .55s ease-in-out infinite alternate;
    }

    .sipadu-robot-eye {
        transform-origin: center;
        animation: sipaduRobotBlink 4.2s infinite;
    }

    .sipadu-robot-antenna-dot {
        transform-origin: center;
        animation: sipaduAntennaPulse 2s ease-in-out infinite;
    }

    .sipadu-bot-window.is-thinking .sipadu-robot-svg {
        animation: sipaduRobotThink .6s ease-in-out infinite alternate;
    }

    .sipadu-bot-window.is-thinking .sipadu-robot-antenna-dot {
        fill: #fde047;
        animation: sipaduAntennaFast .45s ease-in-out infinite alternate;
    }

    @keyframes sipaduRobotFloat {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-2.5px); }
    }

    @keyframes sipaduRobotWave {
        0% { transform: rotate(-6deg) scale(1.04); }
        100% { transform: rotate(6deg) scale(1.04); }
    }

    @keyframes sipaduRobotThink {
        0% { transform: translateX(-2px) rotate(-4deg); }
        100% { transform: translateX(2px) rotate(4deg); }
    }

    @keyframes sipaduRobotBlink {
        0%, 46%, 50%, 96%, 100% { transform: scaleY(1); }
        48%, 98% { transform: scaleY(0.12); }
    }

    @keyframes sipaduAntennaPulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: .55; transform: scale(1.22); }
    }

    @keyframes sipaduAntennaFast {
        from { transform: scale(0.9); opacity: .7; }
        to { transform: scale(1.35); opacity: 1; }
    }

    /* JENDELA POPUP CHATBOT MINIMALIS SOLID */
    .sipadu-bot-window {
        position: fixed;
        right: 24px;
        bottom: 84px;
        z-index: 99985;
        width: min(390px, calc(100vw - 32px));
        height: 520px;
        max-height: calc(100vh - 112px);
        display: none;
        flex-direction: column;
        border-radius: 18px;
        background: #ffffff;
        border: 1px solid #d7e5dc;
        box-shadow: 0 18px 44px rgba(22, 54, 38, .16);
        overflow: hidden;
        font-family: 'Plus Jakarta Sans', 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
        color: #163626;
    }

    .sipadu-bot-window.is-open {
        display: flex;
        animation: sipaduBotPop .22s cubic-bezier(0.22, 1, 0.36, 1);
    }

    @keyframes sipaduBotPop {
        from { opacity: 0; transform: translateY(12px) scale(.97); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    /* HEADER SOLID HIJAU */
    .sipadu-bot-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 14px 18px;
        background: #237c4d;
        color: #ffffff;
        flex-shrink: 0;
    }

    .sipadu-bot-header-info {
        display: flex;
        align-items: center;
        gap: 11px;
    }

    .sipadu-bot-avatar {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        background: rgba(255, 255, 255, .16);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .sipadu-bot-header h4 {
        margin: 0;
        font-size: 14px;
        font-weight: 800;
        letter-spacing: -.01em;
    }

    .sipadu-bot-header span {
        display: block;
        font-size: 11px;
        color: #dcfce7;
        margin-top: 1px;
    }

    .sipadu-bot-header-actions {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .sipadu-bot-icon-btn {
        height: 30px;
        padding: 0 10px;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, .25);
        background: rgba(255, 255, 255, .12);
        color: #ffffff;
        font-family: inherit;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: background .18s ease;
    }

    .sipadu-bot-icon-btn:hover {
        background: rgba(255, 255, 255, .24);
    }

    /* AREA PESAN MINIMALIS */
    .sipadu-bot-messages {
        flex: 1;
        overflow-y: auto;
        padding: 16px;
        background: #f6faf7;
        display: flex;
        flex-direction: column;
        gap: 11px;
    }

    .sipadu-msg {
        max-width: 85%;
        padding: 11px 14px;
        border-radius: 14px;
        font-size: 12.8px;
        line-height: 1.56;
        white-space: pre-line;
        word-break: break-word;
        animation: sipaduMsgIn .2s ease-out;
    }

    @keyframes sipaduMsgIn {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .sipadu-msg.bot {
        align-self: flex-start;
        background: #ffffff;
        color: #163626;
        border: 1px solid #e1ece5;
        border-bottom-left-radius: 4px;
    }

    .sipadu-msg.user {
        align-self: flex-end;
        background: #237c4d;
        color: #ffffff;
        border-bottom-right-radius: 4px;
    }

    /* INDIKATOR MENGETIK (TYPING DOTS) */
    .sipadu-typing {
        align-self: flex-start;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 12px 15px;
        border-radius: 14px;
        border-bottom-left-radius: 4px;
        background: #ffffff;
        border: 1px solid #e1ece5;
    }

    .sipadu-typing span {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #237c4d;
        animation: sipaduTypingBounce 1.1s infinite ease-in-out;
    }

    .sipadu-typing span:nth-child(2) { animation-delay: .18s; }
    .sipadu-typing span:nth-child(3) { animation-delay: .36s; }

    @keyframes sipaduTypingBounce {
        0%, 80%, 100% { transform: translateY(0); opacity: .45; }
        40% { transform: translateY(-4px); opacity: 1; }
    }

    /* QUICK REPLY CHIPS MINIMALIS */
    .sipadu-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 2px;
    }

    .sipadu-chip {
        padding: 7px 12px;
        border-radius: 999px;
        border: 1px solid #bfe3cc;
        background: #ffffff;
        color: #1e6b42;
        font-family: inherit;
        font-size: 11.5px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        transition: background .18s ease, color .18s ease, border-color .18s ease, transform .15s ease;
    }

    .sipadu-chip:hover {
        background: #237c4d;
        border-color: #237c4d;
        color: #ffffff;
        transform: translateY(-1px);
    }

    .sipadu-date-card {
        display: flex;
        gap: 8px;
        align-items: center;
        background: #ffffff;
        padding: 10px;
        border-radius: 12px;
        border: 1px solid #d7e5dc;
    }

    .sipadu-date-card input[type="date"] {
        flex: 1;
        padding: 7px 10px;
        border-radius: 8px;
        border: 1px solid #cbdcd1;
        font-family: inherit;
        font-size: 12.5px;
        color: #163626;
        background: #ffffff;
    }

    .sipadu-date-card button {
        padding: 8px 12px;
        border-radius: 8px;
        border: 0;
        background: #237c4d;
        color: #ffffff;
        font-family: inherit;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
    }

    /* INPUT BAR SOLID */
    .sipadu-bot-input-bar {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 14px;
        background: #ffffff;
        border-top: 1px solid #e1ece5;
        flex-shrink: 0;
    }

    .sipadu-bot-input-bar input {
        flex: 1;
        padding: 10px 13px;
        border-radius: 10px;
        border: 1px solid #d3e2d8;
        background: #f9fcfa;
        font-family: inherit;
        font-size: 12.8px;
        color: #163626;
        outline: none;
        transition: border-color .18s ease, background .18s ease;
    }

    .sipadu-bot-input-bar input:focus {
        background: #ffffff;
        border-color: #237c4d;
        box-shadow: 0 0 0 3px rgba(35, 124, 77, .12);
    }

    .sipadu-bot-send {
        padding: 10px 15px;
        border-radius: 10px;
        border: 0;
        background: #237c4d;
        color: #ffffff;
        font-family: inherit;
        font-size: 12.5px;
        font-weight: 700;
        cursor: pointer;
        transition: background .18s ease;
    }

    .sipadu-bot-send:hover {
        background: #1b663f;
    }

    @media (max-width: 480px) {
        .sipadu-bot-launcher {
            right: 16px;
            bottom: 16px;
            padding: 9px 15px 9px 11px;
        }
        .sipadu-bot-window {
            right: 16px;
            bottom: 74px;
            width: calc(100vw - 32px);
        }
    }
</style>

{{-- TOMBOL ROBOT INTERAKTIF --}}
<button type="button" class="sipadu-bot-launcher" id="sipaduBotLauncher" aria-label="Buka Asisten SIPADU">
    <span class="sipadu-bot-launcher-icon">
        <svg class="sipadu-robot-svg" viewBox="0 0 32 32" fill="none" aria-hidden="true">
            <line x1="16" y1="3" x2="16" y2="8" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round"/>
            <circle class="sipadu-robot-antenna-dot" cx="16" cy="3" r="2.2" fill="#86efac"/>
            <rect x="6" y="8" width="20" height="16" rx="6" fill="#ffffff"/>
            <rect x="9" y="12" width="14" height="8" rx="3.5" fill="#163626"/>
            <circle class="sipadu-robot-eye" cx="13" cy="16" r="1.7" fill="#86efac"/>
            <circle class="sipadu-robot-eye" cx="19" cy="16" r="1.7" fill="#86efac"/>
            <path d="M13 26.5H19" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round"/>
        </svg>
    </span>
    <span>Tanya SIPADU</span>
</button>

{{-- JENDELA CHATBOT MINIMALIS --}}
<div class="sipadu-bot-window" id="sipaduBotWindow" role="dialog" aria-label="Asisten Virtual SIPADU">
    <div class="sipadu-bot-header">
        <div class="sipadu-bot-header-info">
            <div class="sipadu-bot-avatar">
                <svg class="sipadu-robot-svg" viewBox="0 0 32 32" fill="none" aria-hidden="true">
                    <line x1="16" y1="3" x2="16" y2="8" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round"/>
                    <circle class="sipadu-robot-antenna-dot" cx="16" cy="3" r="2.2" fill="#86efac"/>
                    <rect x="6" y="8" width="20" height="16" rx="6" fill="#ffffff"/>
                    <rect x="9" y="12" width="14" height="8" rx="3.5" fill="#163626"/>
                    <circle class="sipadu-robot-eye" cx="13" cy="16" r="1.7" fill="#86efac"/>
                    <circle class="sipadu-robot-eye" cx="19" cy="16" r="1.7" fill="#86efac"/>
                    <path d="M13 26.5H19" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round"/>
                </svg>
            </div>
            <div>
                <h4>Asisten SIPADU</h4>
                <span id="sipaduBotStatus">● Siap membantu Anda</span>
            </div>
        </div>
        <div class="sipadu-bot-header-actions">
            <button type="button" class="sipadu-bot-icon-btn" id="sipaduBotReset" title="Mulai ulang chat">Reset</button>
            <button type="button" class="sipadu-bot-icon-btn" id="sipaduBotClose" title="Tutup">✕</button>
        </div>
    </div>

    <div class="sipadu-bot-messages" id="sipaduBotMessages"></div>

    <form class="sipadu-bot-input-bar" id="sipaduBotForm">
        <input type="text" id="sipaduBotInput" placeholder="Ketik pertanyaan atau jumlah peserta..." autocomplete="off">
        <button type="submit" class="sipadu-bot-send">Kirim</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const launcher = document.getElementById('sipaduBotLauncher');
    const win = document.getElementById('sipaduBotWindow');
    const closeBtn = document.getElementById('sipaduBotClose');
    const resetBtn = document.getElementById('sipaduBotReset');
    const statusText = document.getElementById('sipaduBotStatus');
    const messagesBox = document.getElementById('sipaduBotMessages');
    const form = document.getElementById('sipaduBotForm');
    const input = document.getElementById('sipaduBotInput');
    const endpoint = "{{ route('chatbot.reply') }}";
    const csrfToken = "{{ csrf_token() }}";

    const STORAGE_KEY_HISTORY = 'sipadu_bot_history_v2';
    const STORAGE_KEY_OPEN = 'sipadu_bot_open_v2';

    function formatMarkdownBold(text) {
        return text
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
    }

    function saveHistory() {
        try {
            sessionStorage.setItem(STORAGE_KEY_HISTORY, messagesBox.innerHTML);
        } catch (e) {}
    }

    function setThinkingState(isThinking) {
        win.classList.toggle('is-thinking', isThinking);
        if (statusText) {
            statusText.textContent = isThinking ? '● Sedang mengetik...' : '● Siap membantu Anda';
        }

        const existingTyping = messagesBox.querySelector('.sipadu-typing');
        if (isThinking && !existingTyping) {
            const typing = document.createElement('div');
            typing.className = 'sipadu-typing';
            typing.innerHTML = '<span></span><span></span><span></span>';
            messagesBox.appendChild(typing);
            messagesBox.scrollTop = messagesBox.scrollHeight;
        } else if (!isThinking && existingTyping) {
            existingTyping.remove();
        }
    }

    function appendMessage(text, sender = 'bot') {
        const div = document.createElement('div');
        div.className = 'sipadu-msg ' + sender;
        div.innerHTML = formatMarkdownBold(text);
        messagesBox.appendChild(div);
        messagesBox.scrollTop = messagesBox.scrollHeight;
        saveHistory();
    }

    function appendQuickReplies(replies) {
        if (!Array.isArray(replies) || replies.length === 0) return;
        const wrap = document.createElement('div');
        wrap.className = 'sipadu-chips';

        replies.forEach(item => {
            if (item.redirect_url) {
                const link = document.createElement('a');
                link.className = 'sipadu-chip';
                link.href = item.redirect_url;
                link.textContent = item.label;
                wrap.appendChild(link);
                return;
            }

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'sipadu-chip';
            btn.textContent = item.label;
            btn.dataset.payload = JSON.stringify(item);
            wrap.appendChild(btn);
        });

        messagesBox.appendChild(wrap);
        messagesBox.scrollTop = messagesBox.scrollHeight;
        saveHistory();
    }

    function appendDatePicker(audId, audName) {
        const card = document.createElement('div');
        card.className = 'sipadu-date-card';
        card.dataset.audId = audId || '';
        card.dataset.audName = audName || 'Auditorium';

        const dateInput = document.createElement('input');
        dateInput.type = 'date';
        dateInput.min = new Date().toISOString().split('T')[0];

        const checkBtn = document.createElement('button');
        checkBtn.type = 'button';
        checkBtn.className = 'sipadu-date-check-btn';
        checkBtn.textContent = 'Cek Jadwal';

        card.appendChild(dateInput);
        card.appendChild(checkBtn);
        messagesBox.appendChild(card);
        messagesBox.scrollTop = messagesBox.scrollHeight;
        saveHistory();
    }

    messagesBox.addEventListener('click', function (e) {
        const chipBtn = e.target.closest('button.sipadu-chip');
        if (chipBtn && chipBtn.dataset.payload) {
            const item = JSON.parse(chipBtn.dataset.payload);
            appendMessage(item.label, 'user');
            sendBotPayload({
                message: item.message || item.label,
                action: item.action || '',
                participants: item.participants || null,
                auditorium_id: item.auditorium_id || null,
                auditorium_name: item.auditorium_name || null,
            });
            return;
        }

        const dateBtn = e.target.closest('.sipadu-date-check-btn');
        if (dateBtn) {
            const card = dateBtn.closest('.sipadu-date-card');
            const dateInput = card?.querySelector('input[type="date"]');
            if (!dateInput || !dateInput.value) {
                dateInput?.focus();
                return;
            }
            appendMessage('Cek tanggal ' + dateInput.value, 'user');
            sendBotPayload({
                action: 'check_availability',
                auditorium_id: card.dataset.audId,
                auditorium_name: card.dataset.audName,
                date: dateInput.value,
            });
        }
    });

    async function sendBotPayload(payload) {
        setThinkingState(true);
        try {
            const [res] = await Promise.all([
                fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify(payload),
                }),
                new Promise(resolve => setTimeout(resolve, 350))
            ]);

            const data = await res.json();
            setThinkingState(false);

            if (data.reply) appendMessage(data.reply, 'bot');
            if (data.show_date_picker) appendDatePicker(data.auditorium_id, data.auditorium_name);
            if (data.quick_replies) appendQuickReplies(data.quick_replies);
        } catch (err) {
            setThinkingState(false);
            appendMessage('Koneksi terganggu. Pastikan server Laravel aktif dan coba lagi.', 'bot');
        }
    }

    function resetConversation() {
        messagesBox.innerHTML = '';
        sessionStorage.removeItem(STORAGE_KEY_HISTORY);
        sendBotPayload({ message: 'halo' });
    }

    function toggleChat(openState) {
        const shouldOpen = typeof openState === 'boolean' ? openState : !win.classList.contains('is-open');
        win.classList.toggle('is-open', shouldOpen);
        sessionStorage.setItem(STORAGE_KEY_OPEN, shouldOpen ? '1' : '0');

        if (shouldOpen && messagesBox.innerHTML.trim() === '') {
            sendBotPayload({ message: 'halo' });
        }
        if (shouldOpen) {
            setTimeout(() => input.focus(), 180);
        }
    }

    const savedHistory = sessionStorage.getItem(STORAGE_KEY_HISTORY);
    if (savedHistory) {
        messagesBox.innerHTML = savedHistory;
        messagesBox.querySelector('.sipadu-typing')?.remove();
        messagesBox.scrollTop = messagesBox.scrollHeight;
    }
    if (sessionStorage.getItem(STORAGE_KEY_OPEN) === '1') {
        win.classList.add('is-open');
    }

    launcher?.addEventListener('click', () => toggleChat());
    closeBtn?.addEventListener('click', () => toggleChat(false));
    resetBtn?.addEventListener('click', resetConversation);

    form?.addEventListener('submit', function (e) {
        e.preventDefault();
        const text = (input.value || '').trim();
        if (!text) return;
        input.value = '';
        appendMessage(text, 'user');
        sendBotPayload({ message: text });
    });
});
</script>