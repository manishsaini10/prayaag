(function () {
    class ChatbotWidget {
        constructor() {
            this.config = {};
            this.sessionId = localStorage.getItem('chatbot_session_id');
            this.visitorName = localStorage.getItem('chatbot_visitor_name');
            this.departmentId = localStorage.getItem('chatbot_department_id');
            this.conversationId = null;
            this.renderedMessageIds = new Set();
            this.pollingInterval = null;
            this.pusher = null;
            this.pusherChannel = null;
            this.mediaRecorder = null;
            this.audioChunks = [];
            this.isRecording = false;
            this.typingTimeout = null;
            this.container = null;
            this.launcher = null;
            this.windowEl = null;
            this.bodyContent = null;
            this.messagesEl = null;
            this.inputField = null;
            this.sendBtn = null;
            this.endChatBtn = null;
        }

        async init() {
            try {
                const res = await fetch('/chatbot/widget/config');
                if (!res.ok) throw new Error('Config HTTP ' + res.status);
                this.config = await res.json();
                if (!this.config.enable_chatbot) return;

                this.isDarkMode = localStorage.getItem('chatbot_dark_mode') === 'true' && this.config.enable_dark_mode;
                this.isRTL = document.documentElement.dir === 'rtl' || document.documentElement.lang?.startsWith('ar') || document.documentElement.lang?.startsWith('he');
                this.renderDOM();
                this.bindLauncher();
                this.showScreen();
                this.initTracking();
            } catch (e) {
                console.error('Chatbot Widget Init Error:', e);
            }
        }

        initTracking() {
            if (!this.config.enable_visitor_tracking) return;
            this.trackToken = localStorage.getItem('chatbot_track_token');
            this.identifyVisitor();
            this.startHeartbeat();
            this.bindPageTracking();
        }

        identifyVisitor() {
            fetch('/chatbot/track/identify', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.getCsrfToken() },
                body: JSON.stringify({
                    session_id: this.trackToken || this.sessionId || localStorage.getItem('chatbot_session_id'),
                    landing_page: window.location.pathname,
                    referrer: document.referrer || '',
                    current_page: window.location.href,
                    utm_source: this.getParam('utm_source'),
                    utm_medium: this.getParam('utm_medium'),
                    utm_campaign: this.getParam('utm_campaign'),
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.session_token) {
                    this.trackToken = data.session_token;
                    localStorage.setItem('chatbot_track_token', data.session_token);
                }
                this.trackPageView();
            })
            .catch(() => {});
        }

        trackPageView() {
            if (!this.trackToken) return;
            fetch('/chatbot/track/page', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.getCsrfToken() },
                body: JSON.stringify({
                    session_token: this.trackToken,
                    url: window.location.href,
                    title: document.title,
                    referrer: document.referrer || '',
                })
            }).catch(() => {});
        }

        trackEvent(eventType, category, label, value) {
            if (!this.trackToken) return;
            fetch('/chatbot/track/event', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.getCsrfToken() },
                body: JSON.stringify({
                    session_token: this.trackToken,
                    event_type: eventType,
                    event_category: category || 'interaction',
                    event_label: label || '',
                    event_value: value || '',
                })
            }).catch(() => {});
        }

        startHeartbeat() {
            if (this.heartbeatInterval) clearInterval(this.heartbeatInterval);
            this.trackHeartbeat();
            this.heartbeatInterval = setInterval(() => this.trackHeartbeat(), 30000);
        }

        trackHeartbeat() {
            if (!this.trackToken) return;
            fetch('/chatbot/track/heartbeat', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.getCsrfToken() },
                body: JSON.stringify({ session_token: this.trackToken })
            }).catch(() => {});
        }

        bindPageTracking() {
            let lastUrl = window.location.href;
            const checkUrl = () => {
                if (window.location.href !== lastUrl) {
                    lastUrl = window.location.href;
                    this.trackPageView();
                }
            };
            window.addEventListener('popstate', checkUrl);
            window.addEventListener('hashchange', checkUrl);
            const origPushState = history.pushState;
            history.pushState = function() { origPushState.apply(this, arguments); checkUrl(); };
            const origReplaceState = history.replaceState;
            history.replaceState = function() { origReplaceState.apply(this, arguments); checkUrl(); };
            if (window.navigation) { window.navigation.addEventListener('navigate', checkUrl); }
            window.addEventListener('beforeunload', () => {
                if (this.trackToken) {
                    navigator.sendBeacon('/chatbot/track/end', new URLSearchParams({ session_token: this.trackToken }));
                }
            });
        }

        getParam(name) {
            const p = new URLSearchParams(window.location.search);
            return p.get(name) || '';
        }

        renderDOM() {
            const primaryColor = this.config.primary_color || '#0b2545';
            document.documentElement.style.setProperty('--chatbot-primary', primaryColor);
            if (this.isDarkMode) document.documentElement.classList.add('chatbot-dark');

            const posClass = this.config.widget_position === 'bottom-left' ? 'left-aligned' : '';
            this.container = document.createElement('div');
            this.container.id = 'chatbot-widget-container';
            if (posClass) this.container.classList.add(posClass);
            if (this.isRTL) this.container.setAttribute('dir', 'rtl');

            this.container.innerHTML = `
                <div class="chatbot-launcher" style="background:${primaryColor}">
                    <svg class="chatbot-launcher-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    <svg class="chatbot-launcher-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </div>
                <div class="chatbot-window">
                    <div class="chatbot-header" style="background:${primaryColor}">
                        <div class="chatbot-header-info">
                            <div class="chatbot-avatar">P</div>
                            <div>
                                <div class="chatbot-header-title">Prayaag Help Desk</div>
                                <div class="chatbot-header-status"><span class="chatbot-status-dot"></span> <span class="chatbot-status-text">Online</span></div>
                            </div>
                        </div>
                        <div class="chatbot-header-actions">
                            <button class="chatbot-btn-icon chatbot-dark-toggle" title="Toggle dark mode" style="display:${this.config.enable_dark_mode ? 'flex' : 'none'}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                            </button>
                            <button class="chatbot-btn-icon chatbot-end-chat-btn" title="End chat" style="display:none">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            </button>
                            <button class="chatbot-btn-icon chatbot-close-btn" title="Minimize">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px"><path d="M18 15l-6-6-6 6"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="chatbot-body-content"></div>
                </div>
            `;

            document.body.appendChild(this.container);
            this.launcher = this.container.querySelector('.chatbot-launcher');
            this.windowEl = this.container.querySelector('.chatbot-window');
            this.bodyContent = this.container.querySelector('.chatbot-body-content');
            this.endChatBtn = this.container.querySelector('.chatbot-end-chat-btn');

            this.container.querySelector('.chatbot-close-btn').addEventListener('click', () => this.closeWindow());
            this.container.querySelector('.chatbot-dark-toggle')?.addEventListener('click', () => this.toggleDarkMode());
        }

        bindLauncher() {
            this.launcher.addEventListener('click', () => {
                const isOpen = this.windowEl.classList.contains('open');
                if (isOpen) {
                    this.closeWindow();
                } else {
                    this.openWindow();
                }
            });
        }

        openWindow() {
            this.windowEl.classList.add('open');
            this.launcher.querySelector('.chatbot-launcher-icon').style.display = 'none';
            this.launcher.querySelector('.chatbot-launcher-close').style.display = 'flex';
        }

        closeWindow() {
            this.windowEl.classList.remove('open');
            this.launcher.querySelector('.chatbot-launcher-icon').style.display = 'flex';
            this.launcher.querySelector('.chatbot-launcher-close').style.display = 'none';
        }

        showScreen() {
            if (this.config.enable_departments && !this.departmentId) {
                this.renderDepartmentSelector();
            } else if (!this.sessionId || !this.visitorName) {
                this.renderPreChatForm();
            } else {
                this.initChatSession();
            }
        }

        renderDepartmentSelector() {
            this.bodyContent.innerHTML = `
                <div class="chatbot-departments">
                    <div class="chatbot-departments-header">
                        <h4>How can we help you?</h4>
                        <p>Select a department to get started</p>
                    </div>
                    <div class="chatbot-departments-list" id="chatbot-dept-list"></div>
                </div>
            `;
            const list = this.bodyContent.querySelector('#chatbot-dept-list');
            const deps = this.config.departments || [];

            if (deps.length === 0) {
                this.departmentId = 'general';
                this.renderPreChatForm();
                return;
            }

            deps.forEach(dept => {
                const card = document.createElement('button');
                card.className = 'chatbot-dept-card';
                card.style.setProperty('--dept-color', dept.color || this.config.primary_color);
                card.innerHTML = `
                    <span class="chatbot-dept-dot" style="background:${dept.color || this.config.primary_color}"></span>
                    <div class="chatbot-dept-info">
                        <strong>${this.escHtml(dept.name)}</strong>
                        ${dept.description ? `<small>${this.escHtml(dept.description)}</small>` : ''}
                    </div>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;flex-shrink:0"><path d="m9 18 6-6-6-6"/></svg>
                `;
                card.addEventListener('click', () => {
                    this.departmentId = dept.id;
                    localStorage.setItem('chatbot_department_id', dept.id);
                    this.renderPreChatForm();
                });
                list.appendChild(card);
            });
        }

        renderPreChatForm() {
            if (this.pollingInterval) { clearInterval(this.pollingInterval); this.pollingInterval = null; }
            if (this.endChatBtn) this.endChatBtn.style.display = 'none';

            const dept = this.config.departments?.find(d => d.id === this.departmentId);
            const deptLabel = dept ? ` — ${this.escHtml(dept.name)}` : '';

            this.bodyContent.innerHTML = `
                <form class="chatbot-prechat-form">
                    ${this.config.enable_departments && this.departmentId ? `
                    <div class="chatbot-back-bar">
                        <button type="button" class="chatbot-back-btn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><path d="m15 18-6-6 6-6"/></svg>
                            Change Department
                        </button>
                    </div>
                    ` : ''}
                    <h4>Start Chat${deptLabel}</h4>
                    <p class="chatbot-form-subtitle">Please introduce yourself to start the chat assistant.</p>
                    <input type="text" name="name" placeholder="Full Name" required value="${this.escHtml(this.visitorName || '')}">
                    <input type="email" name="email" placeholder="Email Address (Optional)">
                    <input type="text" name="phone" placeholder="Phone Number (Optional)">
                    <select name="class">
                        <option value="">Select Admission Class (Optional)</option>
                        <option value="nursery">Nursery / KG</option>
                        <option value="primary">Primary (1 - 5)</option>
                        <option value="middle">Middle (6 - 8)</option>
                        <option value="secondary">Secondary (9 - 12)</option>
                    </select>
                    <button type="submit" style="background:${this.config.primary_color};color:#ffffff">Start Conversation</button>
                </form>
            `;

            const backBtn = this.bodyContent.querySelector('.chatbot-back-btn');
            if (backBtn) {
                backBtn.addEventListener('click', () => {
                    this.departmentId = null;
                    localStorage.removeItem('chatbot_department_id');
                    this.renderDepartmentSelector();
                });
            }

            const form = this.bodyContent.querySelector('.chatbot-prechat-form');
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                const fd = new FormData(form);
                const localSid = 'sess_' + Math.random().toString(36).substr(2, 9);
                const btn = form.querySelector('button[type="submit"]');
                if (btn) { btn.disabled = true; btn.innerText = 'Connecting...'; }

                fetch('/chatbot/widget/lead', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.getCsrfToken() },
                    body: JSON.stringify({
                        session_id: localSid,
                        name: fd.get('name'),
                        email: fd.get('email'),
                        phone: fd.get('phone'),
                        class: fd.get('class'),
                        interest: dept ? dept.name : 'Admission Inquiry',
                    })
                })
                .then(r => { if (!r.ok) return r.json().then(e => { throw new Error(e.message || 'Server error'); }); return r.json(); })
                .then(() => {
                    localStorage.setItem('chatbot_session_id', localSid);
                    localStorage.setItem('chatbot_visitor_name', fd.get('name'));
                    this.sessionId = localSid;
                    this.visitorName = fd.get('name');
                    this.initChatSession();
                })
                .catch(err => {
                    console.error('Lead Error:', err);
                    alert('Failed to start chat: ' + err.message);
                    if (btn) { btn.disabled = false; btn.innerText = 'Start Conversation'; }
                });
            });
        }

        initChatSession() {
            if (this.endChatBtn) this.endChatBtn.style.display = 'flex';
            this.renderedMessageIds = new Set();

            this.bodyContent.innerHTML = `
                <div class="chatbot-messages"></div>
                <div class="chatbot-quick-replies">
                    <button class="chatbot-quick-reply-btn" data-text="Admissions Info">Admissions</button>
                    <button class="chatbot-quick-reply-btn" data-text="Fee Structure 2026">Fees</button>
                    <button class="chatbot-quick-reply-btn" data-text="Contact School Office">Contact</button>
                    <button class="chatbot-quick-reply-btn" data-text="School Timing and Holiday List">Timings</button>
                </div>
                <div class="chatbot-input-container">
                    <div class="chatbot-input-actions">
                        <button class="chatbot-btn-icon chatbot-emoji-btn" title="Emoji" style="display:${this.config.enable_emoji ? 'flex' : 'none'}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
                        </button>
                        <button class="chatbot-btn-icon chatbot-gif-btn" title="GIF" style="display:${this.config.enable_gif ? 'flex' : 'none'}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><polyline points="14 7 14 17"/><line x1="9" y1="10" x2="9" y2="14"/><line x1="11" y1="10" x2="13" y2="14"/><line x1="11" y1="14" x2="13" y2="10"/></svg>
                        </button>
                        <button class="chatbot-btn-icon chatbot-attach-btn" title="Attach file" style="display:${this.config.enable_file_upload ? 'flex' : 'none'}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
                        </button>
                    </div>
                    <input type="text" class="chatbot-input-field" placeholder="Type a message...">
                    <button class="chatbot-btn-icon chatbot-voice-btn" title="Voice message" style="display:${this.config.enable_voice_messages ? 'flex' : 'none'}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" y1="19" x2="12" y2="23"/><line x1="8" y1="23" x2="16" y2="23"/></svg>
                    </button>
                    <button class="chatbot-send-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px;height:20px"><path d="m22 2-7 20-4-9-9-4Z"/></svg>
                    </button>
                </div>
                <div class="chatbot-emoji-picker" style="display:none"></div>
                <div class="chatbot-gif-picker" style="display:none"></div>
                <input type="file" class="chatbot-file-input" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.csv,.mp3,.wav" multiple style="display:none">
            `;

            this.messagesEl = this.bodyContent.querySelector('.chatbot-messages');
            this.inputField = this.bodyContent.querySelector('.chatbot-input-field');
            this.sendBtn = this.bodyContent.querySelector('.chatbot-send-btn');

            this.bindChatEvents();
            this.initSession();
        }

        bindChatEvents() {
            this.sendBtn.addEventListener('click', () => { const t = this.inputField.value; this.inputField.value = ''; this.sendMessage(t); });
            this.inputField.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    const t = this.inputField.value;
                    this.inputField.value = '';
                    this.sendMessage(t);
                }
            });
            this.inputField.addEventListener('input', () => {
                if (this.typingTimeout) clearTimeout(this.typingTimeout);
                this.typingTimeout = setTimeout(() => this.emitTyping(), 500);
            });

            this.bodyContent.querySelectorAll('.chatbot-quick-reply-btn').forEach(btn => {
                btn.addEventListener('click', () => this.sendMessage(btn.getAttribute('data-text')));
            });

            const emojiBtn = this.bodyContent.querySelector('.chatbot-emoji-btn');
            const gifBtn = this.bodyContent.querySelector('.chatbot-gif-btn');
            const attachBtn = this.bodyContent.querySelector('.chatbot-attach-btn');
            const voiceBtn = this.bodyContent.querySelector('.chatbot-voice-btn');
            const fileInput = this.bodyContent.querySelector('.chatbot-file-input');

            if (emojiBtn) emojiBtn.addEventListener('click', () => this.toggleEmojiPicker());
            if (gifBtn) gifBtn.addEventListener('click', () => this.toggleGifPicker());
            if (voiceBtn) voiceBtn.addEventListener('click', () => this.toggleVoiceRecording());
            if (attachBtn) attachBtn.addEventListener('click', () => fileInput.click());
            if (fileInput) fileInput.addEventListener('change', (e) => this.handleFileUpload(e));

            this.endChatBtn?.addEventListener('click', (e) => {
                e.stopPropagation();
                if (confirm('Are you sure you want to end this chat session?')) this.endChatSession();
            });
        }

        initSession() {
            fetch('/chatbot/widget/init', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.getCsrfToken() },
                body: JSON.stringify({
                    session_id: this.sessionId,
                    landing_page: window.location.pathname,
                    referrer: document.referrer,
                    department_id: this.departmentId,
                })
            })
            .then(r => { if (!r.ok) throw new Error('Init HTTP ' + r.status); return r.json(); })
            .then(data => {
                this.conversationId = data.conversation_id;
                if (data.messages.length === 0) {
                    this.appendMessage('bot', this.config.settings_data?.greetings?.welcome || 'Hello! Welcome to Prayaag School. How can I help you today?');
                } else {
                    data.messages.forEach(msg => this.appendMessage(msg.sender_type === 'visitor' ? 'visitor' : 'bot', msg.message_text, msg.id, msg.message_type, msg.metadata));
                }
                this.connectRealtime();
                this.pollingInterval = setInterval(() => this.pollNewMessages(), 3000);
            })
            .catch(err => {
                console.error('Init Error:', err);
                this.appendMessage('bot', 'System offline. Please try again later.');
            });
        }

        connectRealtime() {
            if (!this.conversationId || typeof Pusher === 'undefined') return;
            const rt = this.config.realtime;
            if (!rt || rt.broadcast_driver === 'log' || rt.broadcast_driver === 'null') return;
            try {
                this.pusher = new Pusher(rt.broadcast_key || 'app-key', {
                    cluster: 'mt1',
                    wsHost: rt.ws_host || window.location.hostname,
                    wsPort: rt.ws_port || 8080,
                    wssPort: rt.ws_port || 443,
                    wsPath: '/app',
                    forceTLS: rt.ws_scheme === 'https',
                    disableStats: true,
                    enabledTransports: ['ws', 'wss'],
                });
                this.pusherChannel = this.pusher.subscribe('chatbot.conversation.' + this.conversationId);
                this.pusherChannel.bind('message.sent', (data) => {
                    if (data.sender_type !== 'visitor' && !this.renderedMessageIds.has(data.id)) {
                        this.removeTypingIndicator();
                        this.appendMessage('bot', data.message_text, data.id, data.message_type, data.metadata);
                        if (this.config.enable_sound_notification) this.playNotificationSound();
                    }
                });
                this.pusherChannel.bind('agent.typing', () => {
                    this.showTypingIndicator();
                    setTimeout(() => this.removeTypingIndicator(), 4000);
                });
                this.pusherChannel.bind('message.read', () => {});
            } catch (e) {
                console.error('Realtime connect error:', e);
            }
        }

        disconnectRealtime() {
            if (this.pusherChannel) { this.pusherChannel.unbind_all(); this.pusherChannel.unsubscribe(); }
            if (this.pusher) { this.pusher.disconnect(); }
            this.pusher = null;
            this.pusherChannel = null;
        }

        emitTyping() {
            if (!this.conversationId) return;
            fetch('/chatbot/widget/typing', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.getCsrfToken() },
                body: JSON.stringify({ conversation_id: this.conversationId })
            }).catch(() => {});
        }

        sendMessage(text) {
            if (!text.trim() || !this.conversationId) return;
            const tempId = 'temp_' + Date.now();
            this.appendMessage('visitor', text, tempId);
            this.showTypingIndicator();

            fetch('/chatbot/widget/send', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.getCsrfToken() },
                body: JSON.stringify({ conversation_id: this.conversationId, message: text })
            })
            .then(r => { if (!r.ok) throw new Error('Send HTTP ' + r.status); return r.json(); })
            .then(data => {
                this.removeTypingIndicator();
                if (data.visitor_message) {
                    this.renderedMessageIds.delete(tempId);
                    this.renderedMessageIds.add(data.visitor_message.id);
                }
                if (data.bot_message) {
                    this.appendMessage('bot', data.bot_message.message_text, data.bot_message.id);
                    if (this.config.enable_sound_notification) this.playNotificationSound();
                }
            })
            .catch(err => {
                console.error('Send Error:', err);
                this.removeTypingIndicator();
                this.appendMessage('bot', 'Failed to deliver message. Connection lost.');
            });
        }

        appendMessage(sender, text, id = null, type = 'text', metadata = null) {
            if (id && this.renderedMessageIds.has(id)) return;
            if (id) this.renderedMessageIds.add(id);

            const bubble = document.createElement('div');
            bubble.className = `chatbot-msg-bubble ${sender}`;
            bubble.setAttribute('data-type', type || 'text');

            if (type === 'file' && metadata) {
                const fileName = metadata.file_name || text;
                const fileUrl = metadata.file_url;
                const fileType = (metadata.file_type || '').toLowerCase();
                const icon = fileType.startsWith('image') ? '🖼️' : fileType.startsWith('audio') ? '🎵' : fileType.startsWith('video') ? '🎬' : '📎';
                if (fileType.startsWith('image') && fileUrl) {
                    bubble.innerHTML = `<img src="${fileUrl}" alt="${this.escHtml(fileName)}" class="chatbot-msg-img" loading="lazy">`;
                } else {
                    bubble.innerHTML = `<span class="chatbot-file-badge">${icon} <a href="${fileUrl}" target="_blank" rel="noopener">${this.escHtml(fileName)}</a></span>`;
                }
            } else {
                bubble.innerText = text;
            }

            this.messagesEl.appendChild(bubble);
            this.messagesEl.scrollTop = this.messagesEl.scrollHeight;
            this.renderMessageTime(bubble);
        }

        renderMessageTime(bubble) {
            const time = document.createElement('span');
            time.className = 'chatbot-msg-time';
            time.innerText = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            bubble.appendChild(time);
        }

        showTypingIndicator() {
            const existing = document.getElementById('chatbot-typing-indicator');
            if (existing) return;
            const typing = document.createElement('div');
            typing.className = 'chatbot-typing';
            typing.id = 'chatbot-typing-indicator';
            typing.innerHTML = '<span></span><span></span><span></span>';
            this.messagesEl.appendChild(typing);
            this.messagesEl.scrollTop = this.messagesEl.scrollHeight;
        }

        removeTypingIndicator() {
            const el = document.getElementById('chatbot-typing-indicator');
            if (el) el.remove();
        }

        pollNewMessages() {
            if (!this.conversationId) return;
            fetch(`/chatbot/widget/conversations/${this.conversationId}/messages`, { headers: { 'Accept': 'application/json' } })
                .then(r => { if (!r.ok) throw new Error('Poll HTTP ' + r.status); return r.json(); })
                .then(messages => {
                    let playedSound = false;
                    messages.forEach(msg => {
                        if (!this.renderedMessageIds.has(msg.id)) {
                            this.appendMessage(msg.sender_type === 'visitor' ? 'visitor' : 'bot', msg.message_text, msg.id, msg.message_type, msg.metadata);
                            if (msg.sender_type !== 'visitor' && this.config.enable_sound_notification && !playedSound) {
                                this.playNotificationSound();
                                playedSound = true;
                            }
                        }
                    });
                })
                .catch(err => console.error('Poll Error:', err));
        }

        toggleDarkMode() {
            this.isDarkMode = !this.isDarkMode;
            document.documentElement.classList.toggle('chatbot-dark');
            localStorage.setItem('chatbot_dark_mode', this.isDarkMode);
        }

        toggleEmojiPicker() {
            const picker = this.bodyContent.querySelector('.chatbot-emoji-picker');
            const gifPicker = this.bodyContent.querySelector('.chatbot-gif-picker');
            if (gifPicker) gifPicker.style.display = 'none';
            if (picker.style.display === 'flex') { picker.style.display = 'none'; return; }
            picker.style.display = 'flex';
            if (picker.children.length === 0) this.buildEmojiPicker(picker);
        }

        buildEmojiPicker(picker) {
            const categories = [
                { name: 'Smileys', emojis: ['😀','😃','😄','😁','😆','😅','🤣','😂','🙂','😊','😇','🥰','😍','🤩','😘','😗','😚','😙','🥲','😋','😛','😜','🤪','😝','🤑','🤗','🤭','🫢','🫣','🤫','🤔','🫡','🤐','🤨','😐','😑','😶','🫥','😏','😒','🙄','😬','🤥','😌','😔','😪','🤤','😴','😷','🤒','🤕','🤢','🤮','🥴','😵','🤯','🥳','🥺','😢','😭','😤','😡','🤬','👋','🤚','🖐','✋','🖖','👌','🤌','🤏','✌','🤞','🫰','🤟','🤘','🤙','👈','👉','👆','🖕','👇','👍','👎','✊','👊','🤛','🤜','👏','🙌','🫶','👐','🤲','🤝','🙏','✍','💅','🤳','💪','🦵','🦶','👂','🦻','👃','🧠','🫀','🫁','🦷','🦴','👀','👁','👅','👄'] },
                { name: 'Objects', emojis: ['❤️','🧡','💛','💚','💙','💜','🖤','🤍','🤎','💔','❣️','💕','💞','💓','💗','💖','💘','💝','💟','☮️','✝️','☪️','🕉️','☸️','✡️','🔯','🕎','☯️','🦸','🦹','🧙','🧚','🧛','🧜','🧝','🧞','🧟','🧌','💼','🎓','👑','👒','🎩','🎓','🧢','⛑','💄','💋','👣','👤','👥','🫂','👶','👧','🧒','👦','👩','🧑','👨','👩‍🦱','🧑‍🦱','👩‍🦰','🧑‍🦰'] },
                { name: 'Nature', emojis: ['🐶','🐱','🐭','🐹','🐰','🦊','🐻','🐼','🐨','🐯','🦁','🐮','🐷','🐸','🐵','🐔','🐧','🐦','🐤','🐣','🐥','🦆','🦅','🦉','🦇','🐺','🐗','🐴','🦄','🐝','🐛','🦋','🐌','🐞','🐜','🦟','🦗','🪲','🐢','🐍','🦎','🦖','🦕','🐙','🦑','🦐','🦞','🦀','🐡','🐠','🐟','🐬','🐳','🐋','🦈','🐊','🐅','🐆','🦓','🦍','🦧','🐘','🦛','🦏','🐪','🐫','🦒','🦘','🦬','🐃','🐂','🐄','🐎','🐖','🐏','🐑','🦙','🐐','🦌','🐕','🐩','🦮','🐕‍🦺','🐈','🐈‍⬛','🪶','🐓','🦃','🦤','🦚','🦜','🦢','🦩','🕊','🐇','🦝','🦨','🦡','🦫','🦦','🦥','🐁','🐀','🐿','🦔','🐾','🐉','🐲','🌵','🎄','🌲','🌳','🌴','🌱','🌿','☘️','🍀','🎍','🍃','🍂','🍁','🍄','🌾','💐','🌷','🌹','🥀','🌺','🌸','🌼','🌻','🌞','🌝','🌛','🌜','🌚','🌕','🌖','🌗','🌘','🌑','🌒','🌓','🌔','🌙','🌎','🌍','🌏','🪐','💫','⭐','🌟','✨','⚡','☄','💥','🔥','🌪','🌈','☀️','🌤','⛅','🌥','☁️','🌦','🌧','⛈','🌩','🌨','❄️','☃️','⛄','🌬','💨','💧','💦','🫧','☔','☂️','🌊','🌫'] },
                { name: 'Food', emojis: ['🍏','🍎','🍐','🍊','🍋','🍌','🍉','🍇','🍓','🫐','🍈','🍒','🍑','🥭','🍍','🥥','🥝','🍅','🍆','🥑','🥦','🥬','🥒','🌶','🫑','🌽','🥕','🫒','🧄','🧅','🥔','🍠','🫘','🥐','🍞','🥖','🥨','🧀','🥚','🍳','🧈','🥞','🧇','🥓','🥩','🍗','🍖','🦴','🌭','🍔','🍟','🍕','🫓','🥪','🥙','🧆','🌮','🌯','🫔','🥗','🥘','🫕','🥫','🍝','🍜','🍲','🍛','🍣','🍱','🥟','🦪','🍤','🍙','🍚','🍘','🍥','🥠','🥮','🍢','🍡','🍧','🍨','🍩','🍪','🎂','🍰','🧁','🥧','🍫','🍬','🍭','🍮','🍯','🍼','🥛','☕','🫖','🍵','🍶','🍾','🍷','🍸','🍹','🍺','🍻','🥂','🥃','🫗','🧊','🥤','🧋','🧃','🧉','🍽','🍴','🥄','🔪','🫙','🏺'] },
                { name: 'Activity', emojis: ['⚽','🏀','🏈','⚾','🥎','🎾','🏐','🏉','🥏','🎱','🪀','🏓','🏸','🏒','🏑','🥍','🏏','🪃','🥅','⛳','🪁','🏹','🎣','🤿','🥊','🥋','🎯','🛹','🛼','🛷','⛸','🥌','🎿','⛷','🏂','🪂','🏋','🤼','🤸','🤺','⛹','🤾','🏌','🏇','🧘','🏄','🏊','🤽','🚣','🧗','🚵','🚴','🎪','🎭','🎨','🎬','🎤','🎧','🎼','🎹','🥁','🪘','🎷','🎺','🪗','🎸','🪕','🎻','🎲','♟','🎯','🎳','🎮','🕹','🎰'] },
            ];

            categories.forEach(cat => {
                const row = document.createElement('div');
                row.className = 'chatbot-emoji-row';
                cat.emojis.forEach(emoji => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'chatbot-emoji-btn';
                    btn.textContent = emoji;
                    btn.addEventListener('click', () => {
                        this.inputField.value += emoji;
                        this.inputField.focus();
                    });
                    row.appendChild(btn);
                });
                picker.appendChild(row);
            });
        }

        toggleGifPicker() {
            const picker = this.bodyContent.querySelector('.chatbot-gif-picker');
            const emojiPicker = this.bodyContent.querySelector('.chatbot-emoji-picker');
            if (emojiPicker) emojiPicker.style.display = 'none';
            if (picker.style.display === 'flex') { picker.style.display = 'none'; return; }
            picker.style.display = 'flex';
            if (picker.children.length === 0) this.buildGifPicker(picker);
        }

        buildGifPicker(picker) {
            const searchBar = document.createElement('div');
            searchBar.className = 'chatbot-gif-search';
            searchBar.innerHTML = `
                <input type="text" class="chatbot-gif-search-input" placeholder="Search GIFs...">
                <button type="button" class="chatbot-btn-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></button>
            `;
            const grid = document.createElement('div');
            grid.className = 'chatbot-gif-grid';

            const searchInput = searchBar.querySelector('input');
            const searchBtn = searchBar.querySelector('button');

            const sampleGifs = [
                'https://media.tenor.com/8JpXY2mJ2eIAAAAd/hello-hi.gif',
                'https://media.tenor.com/Wxflb44KkF4AAAAd/wave-hello.gif',
                'https://media.tenor.com/GfSX-u7lGM4AAAAC/cat-wave.gif',
                'https://media.tenor.com/8gJ1HhSl8sEAAAAd/thank-you-thanks.gif',
                'https://media.tenor.com/neJPNdrFMwkAAAAd/laughing-laugh.gif',
                'https://media.tenor.com/9Vo0jMv6G1YAAAAC/clapping-applause.gif',
                'https://media.tenor.com/VK7A_T7E8V8AAAAd/good-morning-good-day.gif',
                'https://media.tenor.com/IhF7Dvq_nsUAAAAd/sorry-apologize.gif',
            ];

            sampleGifs.forEach(url => {
                const img = document.createElement('img');
                img.className = 'chatbot-gif-item';
                img.src = url;
                img.alt = 'GIF';
                img.loading = 'lazy';
                img.addEventListener('click', () => {
                    this.sendMessage(img.src);
                    picker.style.display = 'none';
                });
                grid.appendChild(img);
            });

            const loadFunc = () => {
                const q = searchInput.value.trim();
                if (!q) return;
                grid.innerHTML = '<div class="chatbot-gif-loading">Searching...</div>';
                const apiKey = this.config.gif_api_key || '';
                const url = apiKey
                    ? `https://tenor.googleapis.com/v2/search?q=${encodeURIComponent(q)}&key=${apiKey}&limit=12`
                    : `https://api.giphy.com/v1/gifs/search?api_key=${apiKey || 'l3K9b3P0X4S0Q6Y7jW'}&q=${encodeURIComponent(q)}&limit=12`;
                if (!apiKey) {
                    grid.innerHTML = '<div class="chatbot-gif-loading">GIF search requires API key configuration in settings.</div>';
                    return;
                }
                fetch(url).then(r => r.json()).then(data => {
                    grid.innerHTML = '';
                    const results = data.results || data.data || [];
                    results.forEach(item => {
                        const imgUrl = item.media_formats?.gif?.url || item.media?.[0]?.gif?.url || item.images?.fixed_height?.url;
                        if (!imgUrl) return;
                        const img = document.createElement('img');
                        img.className = 'chatbot-gif-item';
                        img.src = imgUrl;
                        img.alt = 'GIF';
                        img.loading = 'lazy';
                        img.addEventListener('click', () => {
                            this.sendMessage(imgUrl);
                            picker.style.display = 'none';
                        });
                        grid.appendChild(img);
                    });
                }).catch(() => {
                    grid.innerHTML = '<div class="chatbot-gif-loading">Failed to load GIFs</div>';
                });
            };

            searchBtn.addEventListener('click', loadFunc);
            searchInput.addEventListener('keydown', (e) => { if (e.key === 'Enter') loadFunc(); });

            picker.appendChild(searchBar);
            picker.appendChild(grid);
        }

        toggleVoiceRecording() {
            if (this.isRecording) { this.stopVoiceRecording(); return; }
            if (!navigator.mediaDevices?.getUserMedia) { alert('Voice recording not supported in this browser.'); return; }

            navigator.mediaDevices.getUserMedia({ audio: true })
                .then(stream => {
                    this.isRecording = true;
                    const btn = this.bodyContent.querySelector('.chatbot-voice-btn');
                    if (btn) btn.classList.add('recording');
                    this.audioChunks = [];
                    this.mediaRecorder = new MediaRecorder(stream, { mimeType: MediaRecorder.isTypeSupported('audio/webm') ? 'audio/webm' : 'audio/ogg' });
                    this.mediaRecorder.ondataavailable = (e) => { if (e.data.size > 0) this.audioChunks.push(e.data); };
                    this.mediaRecorder.onstop = () => {
                        stream.getTracks().forEach(t => t.stop());
                        const blob = new Blob(this.audioChunks, { type: this.mediaRecorder.mimeType });
                        this.uploadAudio(blob);
                    };
                    this.mediaRecorder.start();
                })
                .catch(() => alert('Microphone access denied.'));
        }

        stopVoiceRecording() {
            if (this.mediaRecorder && this.mediaRecorder.state !== 'inactive') {
                this.mediaRecorder.stop();
            }
            this.isRecording = false;
            const btn = this.bodyContent.querySelector('.chatbot-voice-btn');
            if (btn) btn.classList.remove('recording');
        }

        uploadAudio(blob) {
            if (!this.conversationId) return;
            const fd = new FormData();
            fd.append('file', blob, 'voice-' + Date.now() + '.webm');
            fd.append('conversation_id', this.conversationId);

            this.appendMessage('visitor', '🎵 Voice message', 'voice_' + Date.now());
            this.showTypingIndicator();

            fetch('/chatbot/widget/upload', {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.getCsrfToken() },
                body: fd
            })
            .then(r => r.json())
            .then(data => {
                this.removeTypingIndicator();
                if (data.message) {
                    this.renderedMessageIds.add(data.message.id);
                }
            })
            .catch(err => {
                console.error('Upload Error:', err);
                this.removeTypingIndicator();
            });
        }

        handleFileUpload(e) {
            const files = e.target.files;
            if (!files.length || !this.conversationId) return;
            const fd = new FormData();
            fd.append('file', files[0]);
            fd.append('conversation_id', this.conversationId);

            this.appendMessage('visitor', '📎 ' + files[0].name, 'file_' + Date.now());
            this.showTypingIndicator();

            fetch('/chatbot/widget/upload', {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.getCsrfToken() },
                body: fd
            })
            .then(r => r.json())
            .then(data => {
                this.removeTypingIndicator();
                if (data.message) this.renderedMessageIds.add(data.message.id);
            })
            .catch(err => {
                console.error('Upload Error:', err);
                this.removeTypingIndicator();
            });

            e.target.value = '';
        }

        playNotificationSound() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc1 = ctx.createOscillator();
                const osc2 = ctx.createOscillator();
                const gain = ctx.createGain();
                osc1.type = 'sine'; osc1.frequency.setValueAtTime(587.33, ctx.currentTime); osc1.frequency.setValueAtTime(880, ctx.currentTime + 0.08);
                osc2.type = 'sine'; osc2.frequency.setValueAtTime(293.66, ctx.currentTime); osc2.frequency.setValueAtTime(440, ctx.currentTime + 0.08);
                gain.gain.setValueAtTime(0.12, ctx.currentTime); gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.35);
                osc1.connect(gain); osc2.connect(gain); gain.connect(ctx.destination);
                osc1.start(); osc2.start(); osc1.stop(ctx.currentTime + 0.35); osc2.stop(ctx.currentTime + 0.35);
            } catch (e) { console.error('Audio error:', e); }
        }

        endChatSession() {
            if (!this.sessionId) return;
            const cleanup = () => {
                this.disconnectRealtime();
                if (this.pollingInterval) { clearInterval(this.pollingInterval); this.pollingInterval = null; }
                if (this.heartbeatInterval) { clearInterval(this.heartbeatInterval); this.heartbeatInterval = null; }
                localStorage.removeItem('chatbot_session_id');
                localStorage.removeItem('chatbot_visitor_name');
                localStorage.removeItem('chatbot_department_id');
                localStorage.removeItem('chatbot_track_token');
                if (this.trackToken) {
                    navigator.sendBeacon('/chatbot/track/end', new URLSearchParams({ session_token: this.trackToken }));
                }
                this.sessionId = null;
                this.visitorName = null;
                this.departmentId = null;
                this.conversationId = null;
                this.trackToken = null;
                this.renderPreChatForm();
            };

            if (this.conversationId) {
                fetch(`/chatbot/widget/conversations/${this.conversationId}/close`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.getCsrfToken() }
                }).finally(() => cleanup());
            } else {
                cleanup();
            }
        }

        escHtml(str) {
            if (!str) return '';
            const d = document.createElement('div');
            d.textContent = str;
            return d.innerHTML;
        }

        getCsrfToken() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.getAttribute('content') : '';
        }
    }

    const widget = new ChatbotWidget();
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => widget.init());
    } else {
        widget.init();
    }
})();
