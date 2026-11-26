<style>
    /* TOMBOL FLOATING DI POJOK KANAN BAWAH */
    .sipadu-bot-launcher {
        position: fixed;
        right: 24px;
        bottom: 24px;
        z-index: 99980;
        display: inline-flex;
        align-items: center;
        gap: 9px;
        padding: 12px 18px;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, .45);
        background: linear-gradient(135deg, #237c4d, #163626);
        color: #ffffff;
        font-family: 'Plus Jakarta Sans', 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 12px 30px rgba(22, 54, 38, .28);
        transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
    }

    .sipadu-bot-launcher:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 34px rgba(22, 54, 38, .35);
    }

    .sipadu-bot-launcher svg {
        width: 18px;
        height: 18px;
        flex-shrink: 0;
    }

    /* JENDELA POPUP CHATBOT GLASSMORPHISM */
    .sipadu-bot-window {
        position: fixed;
        right: 24px;
        bottom: 84px;
        z-index: 99985;
        width: min(395px, calc(100vw - 32px));
        height: 525px;
        max-height: calc(100vh - 112px);
        display: none;
        flex-direction: column;
        border-radius: 20px;
        background: linear-gradient(155deg, rgba(255, 255, 255, .95), rgba(244, 250, 246, .90));
        backdrop-filter: blur(20px) saturate(160%);
        -webkit-backdrop-filter: blur(20px) saturate(160%);
        border: 1px solid rgba(255, 255, 255, .80);
        box-shadow: 0 20px 50px rgba(22, 54, 38, .22);
        overflow: hidden;
        font-family: 'Plus Jakarta Sans', 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
        color: #163626;
    }

    .sipadu-bot-window.is-open {
        display: flex;
        animation: sipaduBotSlideUp .22s cubic-bezier(0.22, 1, 0.36, 1);
    }

    @keyframes sipaduBotSlideUp {
        from { opacity: 0; transform: translateY(14px) scale(.97); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    .sipadu-bot-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 15px 18px;
        background: linear-gradient(135deg, #237c4d, #195937);
        color: #ffffff;
        flex-shrink: 0;
    }

    .sipadu-bot-header-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .sipadu-bot-avatar {
        width: 36px;
        height: 36px;
        border-radius: 11px;
        background: rgba(255, 255, 255, .18);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .sipadu-bot-avatar svg {
        width: 20px;
        height: 20px;
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
        opacity: .9;
        margin-top: 1px;
    }

    .sipadu-bot-header-actions {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .sipadu-bot-icon-btn {
        height: 30px;
        padding: 0 9px;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, .26);
        background: rgba(255, 255, 255, .14);
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
        background: rgba(255, 255, 255, .26);
    }

    .sipadu-bot-messages {
        flex: 1;
        overflow-y: auto;
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 11px;
    }

    .sipadu-msg {
        max-width: 86%;
        padding: 11px 14px;
        border-radius: 15px;
        font-size: 12.8px;
        line-height: 1.56;
        white-space: pre-line;
        word-break: break-word;
    }

    .sipadu-msg.bot {
        align-self: flex-start;
        background: #ffffff;
        color: #163626;
        border: 1px solid rgba(22, 54, 38, .09);
        border-bottom-left-radius: 4px;
        box-shadow: 0 4px 12px rgba(22, 54, 38, .04);
    }

    .sipadu-msg.user {
        align-self: flex-end;
        background: #237c4d;
        color: #ffffff;
        border-bottom-right-radius: 4px;
    }

    .sipadu-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 2px;
    }

    .sipadu-chip {
        padding: 7px 12px;
        border-radius: 999px;
        border: 1px solid #86efac;
        background: #dcfce7;
        color: #166534;
        font-family: inherit;
        font-size: 11.5px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        transition: background .18s ease, transform .15s ease;
    }

    .sipadu-chip:hover {
        background: #bbf7d0;
        transform: translateY(-1px);
    }

    .sipadu-date-card {
        display: flex;
        gap: 8px;
        align-items: center;
        background: #ffffff;
        padding: 10px;
        border-radius: 12px;
        border: 1px solid rgba(22, 54, 38, .12);
    }

    .sipadu-date-card input[type="date"] {
        flex: 1;
        padding: 7px 10px;
        border-radius: 8px;
        border: 1px solid rgba(22, 54, 38, .18);
        font-family: inherit;
        font-size: 12.5px;
        color: #163626;
    }

    .sipadu-date-card button {
        padding: 8px 12px;
        border-radius: 8px;
        border: 0;
        background: #237c4d;
        color: #fff;
        font-family: inherit;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
    }

    .sipadu-bot-input-bar {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 14px;
        background: rgba(255, 255, 255, .94);
        border-top: 1px solid rgba(22, 54, 38, .08);
        flex-shrink: 0;
    }

    .sipadu-bot-input-bar input {
        flex: 1;
        padding: 10px 13px;
        border-radius: 11px;
        border: 1px solid rgba(22, 54, 38, .14);
        background: #ffffff;
        font-family: inherit;
        font-size: 12.8px;
        color: #163626;
        outline: none;
    }

    .sipadu-bot-input-bar input:focus {
        border-color: #237c4d;
        box-shadow: 0 0 0 3px rgba(35, 124, 77, .12);
    }

    .sipadu-bot-send {
        padding: 10px 14px;
        border-radius: 11px;
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
            padding: 11px 15px;
        }
        .sipadu-bot-window {
            right: 16px;
            bottom: 74px;
            width: calc(100vw - 32px);
        }
    }
</style>

{{-- TOMBOL MENGAMBANG DI SETIAP MENU --}}
<button type="button" class="sipadu-bot-launcher" id="sipaduBotLauncher" aria-label="Buka Asisten SIPADU">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
    </svg>
    <span>Tanya SIPADU</span>
</button>

{{-- JENDELA POPUP CHATBOT --}}
<div class="sipadu-bot-window" id="sipaduBotWindow" role="dialog" aria-label="Asisten Virtual SIPADU">
    <div class="sipadu-bot-header">
        <div class="sipadu-bot-header-info">
            <div class="sipadu-bot-avatar">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="3" y="11" width="18" height="10" rx="2"/>
                    <circle cx="12" cy="5" r="2"/>
                    <path d="M12 7v4"/>
                    <line x1="8" y1="16" x2="8" y2="16"/>
                    <line x1="16" y1="16" x2="16" y2="16"/>
                </svg>
            </div>
            <div>
                <h4>Asisten SIPADU</h4>
                <span>● Online • Panduan &amp; Cek Jadwal</span>
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
    const messagesBox = document.getElementById('sipaduBotMessages');
    const form = document.getElementById('sipaduBotForm');
    const input = document.getElementById('sipaduBotInput');
    const endpoint = "{{ route('chatbot.reply') }}";
    const csrfToken = "{{ csrf_token() }}";

    const STORAGE_KEY_HISTORY = 'sipadu_bot_history_v1';
    const STORAGE_KEY_OPEN = 'sipadu_bot_open_v1';

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

    /* EVENT DELEGATION AGAR TOMBOL TETAP AKTIF SETELAH PINDAH MENU */
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
        try {
            const res = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify(payload),
            });

            const data = await res.json();
            if (data.reply) appendMessage(data.reply, 'bot');
            if (data.show_date_picker) appendDatePicker(data.auditorium_id, data.auditorium_name);
            if (data.quick_replies) appendQuickReplies(data.quick_replies);
        } catch (err) {
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

    /* PULIHKAN RIWAYAT & STATUS BUKA/TUTUP SAAT PINDAH HALAMAN */
    const savedHistory = sessionStorage.getItem(STORAGE_KEY_HISTORY);
    if (savedHistory) {
        messagesBox.innerHTML = savedHistory;
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