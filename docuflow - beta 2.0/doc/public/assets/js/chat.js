const ChatApp = (() => {
  const storageKey = 'docuflow_chat_session_id';
  const historyKey = 'docuflow_chat_history';
  const chatBody = document.getElementById('chatBody');
  const chatForm = document.getElementById('chatForm');
  const chatMessage = document.getElementById('chatMessage');
  const newChatBtn = document.getElementById('newChatBtn');

  function generateSessionId() {
    if (window.crypto && crypto.randomUUID) {
      return crypto.randomUUID().replace(/-/g, '');
    }
    return (Date.now().toString(16) + Math.random().toString(16).slice(2)).slice(0, 32);
  }

  function getSessionId() {
    let id = localStorage.getItem(storageKey);
    if (!id) {
      id = generateSessionId();
      localStorage.setItem(storageKey, id);
    }
    return id;
  }

  function getHistory() {
    try {
      return JSON.parse(localStorage.getItem(historyKey) || '[]');
    } catch {
      return [];
    }
  }

  function saveHistory(items) {
    localStorage.setItem(historyKey, JSON.stringify(items));
  }

  function addMessage(role, text) {
    const messageEl = document.createElement('div');
    messageEl.className = `message ${role}`;
    messageEl.innerHTML = `<div class="bubble"></div>`;
    messageEl.querySelector('.bubble').textContent = text;
    chatBody.appendChild(messageEl);
    scrollToBottom();
  }

  function scrollToBottom() {
    chatBody.scrollTop = chatBody.scrollHeight;
  }

  function loadHistoryToUI() {
    const history = getHistory();
    if (history.length === 0) return;
    history.forEach(item => addMessage(item.role, item.text));
    scrollToBottom();
  }

  function setHistory(role, text) {
    const history = getHistory();
    history.push({ role, text, at: Date.now() });
    saveHistory(history);
  }

  async function sendMessage(text) {
    const sessionId = getSessionId();
    const payload = { sessionId, message: text };

    const res = await fetch('/doc/api/chat_handler.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });

    const data = await res.json();
    return data.reply || 'لم يصل رد من الخدمة.';
  }

  function resetSession() {
    localStorage.removeItem(storageKey);
    localStorage.removeItem(historyKey);
    chatBody.innerHTML = '';
    addMessage('bot', 'تم بدء محادثة جديدة. أرسل التوكن الخاص بك للبدء.');
  }

  function autoResizeTextarea() {
    const computed = window.getComputedStyle(chatMessage);
    const lineHeight = parseFloat(computed.lineHeight) || 20;
    const paddingY = parseFloat(computed.paddingTop) + parseFloat(computed.paddingBottom);
    const maxLines = 6;
    const maxHeight = (lineHeight * maxLines) + paddingY;

    chatMessage.style.height = 'auto';
    chatMessage.style.height = `${Math.min(chatMessage.scrollHeight, maxHeight)}px`;
    chatMessage.style.overflowY = chatMessage.scrollHeight > maxHeight ? 'auto' : 'hidden';
  }

  function bindEvents() {
    chatForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const text = chatMessage.value.trim();
      if (!text) return;

      chatMessage.value = '';
      autoResizeTextarea();
      addMessage('user', text);
      setHistory('user', text);

      addMessage('bot', '... جاري المعالجة');
      try {
        const reply = await sendMessage(text);
        // remove last temp bot message
        chatBody.removeChild(chatBody.lastElementChild);
        addMessage('bot', reply);
        setHistory('bot', reply);
      } catch (err) {
        chatBody.removeChild(chatBody.lastElementChild);
        addMessage('bot', 'حدث خطأ. حاول مرة أخرى.');
      }
    });

    chatMessage.addEventListener('input', autoResizeTextarea);
    chatMessage.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        chatForm.dispatchEvent(new Event('submit'));
      }
    });

    if (newChatBtn) {
      newChatBtn.addEventListener('click', resetSession);
    }
  }

  function init() {
    getSessionId();
    loadHistoryToUI();
    autoResizeTextarea();
    bindEvents();
  }

  return { init };
})();

document.addEventListener('DOMContentLoaded', () => {
  ChatApp.init();
});
