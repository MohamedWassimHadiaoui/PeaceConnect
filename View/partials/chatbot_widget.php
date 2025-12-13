<?php require_once __DIR__ . '/../../config.php'; ?>
<style>
.chatbot-btn {
    position: fixed;
    bottom: 24px;
    right: 24px;
    width: 64px;
    height: 44px;
    border-radius: 999px;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    border: 1px solid rgba(255,255,255,0.12);
    cursor: pointer;
    box-shadow: 0 4px 20px rgba(99, 102, 241, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    font-weight: 700;
    letter-spacing: 0.02em;
    z-index: 1000;
    transition: all 0.3s;
    color: #fff;
}
.chatbot-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 30px rgba(99, 102, 241, 0.5);
}
.chatbot-container {
    position: fixed;
    bottom: 100px;
    right: 24px;
    width: 380px;
    max-height: 500px;
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 20px;
    box-shadow: 0 10px 40px var(--shadow);
    display: none;
    flex-direction: column;
    z-index: 1000;
    overflow: hidden;
}
.chatbot-container.open { display: flex; animation: slideUp 0.3s ease; }
@keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
.chatbot-header {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: #fff;
    padding: 1rem 1.25rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.chatbot-header h3 { margin: 0; font-size: 1rem; display: flex; align-items: center; gap: 0.5rem; }
.chatbot-close, .chatbot-new { background: rgba(255,255,255,0.2); border: none; color: #fff; width: 28px; height: 28px; border-radius: 50%; cursor: pointer; font-size: 0.9rem; }
.chatbot-close:hover, .chatbot-new:hover { background: rgba(255,255,255,0.35); }
.chatbot-messages { flex: 1; padding: 1rem; overflow-y: auto; max-height: 300px; }
.chat-message { margin-bottom: 0.75rem; display: flex; flex-direction: column; }
.chat-message.user { align-items: flex-end; }
.chat-message.bot { align-items: flex-start; }
.chat-bubble { max-width: 80%; padding: 0.75rem 1rem; border-radius: 16px; font-size: 0.9rem; line-height: 1.5; }
.chat-message.user .chat-bubble { background: linear-gradient(135deg, var(--primary), var(--secondary)); color: #fff; border-bottom-right-radius: 4px; }
.chat-message.bot .chat-bubble { background: var(--bg-input); color: var(--text-primary); border-bottom-left-radius: 4px; }
.chatbot-input { padding: 1rem; border-top: 1px solid var(--border-color); display: flex; gap: 0.5rem; }
.chatbot-input input { flex: 1; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: 25px; background: var(--bg-input); color: var(--text-primary); font-size: 0.9rem; }
.chatbot-input input:focus { outline: none; border-color: var(--primary); }
.chatbot-input button { padding: 0 14px; height: 44px; border-radius: 14px; background: linear-gradient(135deg, var(--primary), var(--secondary)); border: none; color: #fff; cursor: pointer; font-size: 0.9rem; font-weight: 700; }
.typing-indicator { display: flex; gap: 4px; padding: 0.5rem; }
.typing-indicator span { width: 8px; height: 8px; background: var(--text-muted); border-radius: 50%; animation: typing 1.4s infinite; }
.typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
.typing-indicator span:nth-child(3) { animation-delay: 0.4s; }
@keyframes typing { 0%, 60%, 100% { transform: translateY(0); opacity: 0.4; } 30% { transform: translateY(-4px); opacity: 1; } }
.chatbot-quick { display: flex; gap: 0.5rem; padding: 0.5rem 1rem; border-top: 1px solid var(--border-color); justify-content: center; }
.chatbot-quick button { width: 40px; height: 40px; border-radius: 12px; border: 1px solid var(--border-color); background: var(--bg-input); cursor: pointer; font-size: 1.1rem; transition: all 0.2s; }
.chatbot-quick button:hover { background: var(--primary); transform: scale(1.1); border-color: var(--primary); }
@media (max-width: 480px) { .chatbot-container { width: calc(100% - 48px); right: 24px; left: 24px; } }
</style>

<button class="chatbot-btn" id="chatbotBtn" title="Support chat">Chat</button>

<div class="chatbot-container" id="chatbotContainer">
    <div class="chatbot-header">
        <h3>🕊️ PeaceConnect</h3>
        <div style="display:flex;gap:0.5rem;align-items:center">
            <button class="chatbot-new" id="chatbotNew" title="New conversation">🔄</button>
            <button class="chatbot-close" id="chatbotClose">✕</button>
        </div>
    </div>
    <div class="chatbot-messages" id="chatbotMessages">
    </div>
    <div class="chatbot-quick" id="chatbotQuick">
        <button data-action="forum" title="Forum">💬</button>
        <button data-action="events" title="Events">📅</button>
        <button data-action="report" title="Report">📋</button>
        <button data-action="help" title="Help">🤝</button>
        <button data-action="resources" title="Resources">📚</button>
    </div>
    <div class="chatbot-input">
        <input type="text" id="chatbotInput" placeholder="Ask me anything..." autocomplete="off">
        <button id="chatbotSend" title="Send">→</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('chatbotBtn');
    const container = document.getElementById('chatbotContainer');
    const closeBtn = document.getElementById('chatbotClose');
    const newBtn = document.getElementById('chatbotNew');
    const input = document.getElementById('chatbotInput');
    const sendBtn = document.getElementById('chatbotSend');
    const messages = document.getElementById('chatbotMessages');

    const STORAGE_KEY = 'peaceconnect_chat_history';
    // Use absolute path for API
    const apiPath = '<?= BASE_URL ?>api/chatbot.php';

    // Navigation routes - use absolute paths
    const baseUrl = '<?= BASE_URL ?>View/frontoffice/';
    const routes = {
        'forum': baseUrl + 'forum.php', 'events': baseUrl + 'events.php', 'resources': baseUrl + 'resources.php',
        'report': baseUrl + 'create_report.php', 'help': baseUrl + 'help_request.php', 'my_reports': baseUrl + 'my_reports.php',
        'profile': baseUrl + 'profile.php', 'login': baseUrl + 'login.php', 'register': baseUrl + 'register.php',
        'organisations': baseUrl + 'organisations.php', 'home': baseUrl + 'index.php'
    };

    // Load chat history
    function loadHistory() {
        messages.innerHTML = '';
        const history = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
        if (history.length === 0) {
            addMessage("👋 Hey! I can help you navigate the site or answer questions. What's up?", 'bot', false);
        } else {
            history.forEach(m => addMessage(m.text, m.sender, false));
        }
    }

    function saveHistory() {
        const bubbles = messages.querySelectorAll('.chat-message');
        const history = [];
        bubbles.forEach(b => {
            const isUser = b.classList.contains('user');
            const text = b.querySelector('.chat-bubble')?.textContent || '';
            if (text && !b.querySelector('.typing-indicator')) {
                history.push({ sender: isUser ? 'user' : 'bot', text });
            }
        });
        // Keep last 50 messages
        localStorage.setItem(STORAGE_KEY, JSON.stringify(history.slice(-50)));
    }

    function clearHistory() {
        localStorage.removeItem(STORAGE_KEY);
        loadHistory();
    }

    // Get current page context
    const currentPage = window.location.pathname.split('/').pop().replace('.php', '') || 'home';
    const isLoggedIn = document.querySelector('a[href*="logout"]') !== null;

    // Event listeners
    btn.addEventListener('click', () => { container.classList.toggle('open'); if (container.classList.contains('open')) input.focus(); });
    closeBtn.addEventListener('click', () => container.classList.remove('open'));
    newBtn.addEventListener('click', clearHistory);
    input.addEventListener('keypress', (e) => { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); } });
    sendBtn.addEventListener('click', sendMessage);

    // Quick action buttons
    document.querySelectorAll('.chatbot-quick button').forEach(btn => {
        btn.addEventListener('click', () => {
            const action = btn.dataset.action;
            if (routes[action]) {
                addMessage(`Take me to ${action}`, 'user');
                addMessage(`Taking you there now! 🚀`, 'bot');
                setTimeout(() => { window.location.href = routes[action]; }, 800);
            }
        });
    });

    function sendMessage() {
        const text = input.value.trim();
        if (!text) return;

        // Check if user wants new chat
        if (/^(new chat|clear|reset|start over)/i.test(text)) {
            clearHistory();
            addMessage("🔄 Fresh start! How can I help?", 'bot');
            input.value = '';
            return;
        }

        addMessage(text, 'user');
        input.value = '';
        sendBtn.disabled = true;

        const typingDiv = document.createElement('div');
        typingDiv.className = 'chat-message bot';
        typingDiv.innerHTML = '<div class="chat-bubble"><div class="typing-indicator"><span></span><span></span><span></span></div></div>';
        messages.appendChild(typingDiv);
        messages.scrollTop = messages.scrollHeight;

        fetch(apiPath, { 
            method: 'POST', 
            headers: { 'Content-Type': 'application/json' }, 
            body: JSON.stringify({ 
                message: text,
                context: { page: currentPage, loggedIn: isLoggedIn }
            }) 
        })
        .then(res => res.json())
        .then(data => {
            typingDiv.remove();
            let reply = data.reply || data.error || 'Sorry, I cannot respond right now.';
            
            const gotoMatch = reply.match(/\[GOTO:(\w+)\]/);
            if (gotoMatch) {
                const page = gotoMatch[1];
                reply = reply.replace(/\s*\[GOTO:\w+\]/, '');
                addMessage(reply, 'bot');
                if (routes[page]) {
                    setTimeout(() => {
                        window.location.href = routes[page];
                    }, 1000);
                }
            } else {
                addMessage(reply, 'bot');
            }
            sendBtn.disabled = false;
        })
        .catch(() => { typingDiv.remove(); addMessage('Connection error. Please try again.', 'bot'); sendBtn.disabled = false; });
    }

    function addMessage(text, sender, save = true) {
        const div = document.createElement('div');
        div.className = 'chat-message ' + sender;
        div.innerHTML = '<div class="chat-bubble">' + escapeHtml(text) + '</div>';
        messages.appendChild(div);
        messages.scrollTop = messages.scrollHeight;
        if (save) saveHistory();
    }

    function escapeHtml(text) { const div = document.createElement('div'); div.textContent = text; return div.innerHTML; }

    // Initialize
    loadHistory();
});
</script>
