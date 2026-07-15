<?php

namespace App\Http\Controllers\Cms;

use App\Core\Chatbot\Repositories\ChatbotRepository;
use App\Http\Controllers\Controller;
use App\Models\Chatbot\ChatbotFormField;
use App\Models\Chatbot\Enterprise\Department;
use Illuminate\Support\Facades\Cache;

class ChatbotEmbedController extends Controller
{
    public function __construct(
        private readonly ChatbotRepository $repository
    ) {}

    public function embedJs()
    {
        $settings = $this->repository->getSettings();
        if (!$settings->enable_chatbot) {
            return response('/* Chatbot disabled */', 200)
                ->header('Content-Type', 'application/javascript')
                ->header('Access-Control-Allow-Origin', '*');
        }

        $primaryColor = $settings->primary_color ?? '#0b2545';
        $position = $settings->widget_position ?? 'bottom-right';
        $shape = $settings->widget_shape ?? 'rounded';
        $launcherStyle = $settings->launcher_style ?? 'icon';
        $enableDarkMode = $settings->enable_dark_mode ?? false;
        $enableSound = $settings->enable_sound_notification ?? false;
        $enableDepartments = $settings->enable_departments ?? false;
        $enableVisitorTracking = $settings->enable_visitor_tracking ?? false;

        $departments = [];
        if ($enableDepartments) {
            $departments = Department::where('is_active', true)
                ->orderBy('sort_order')
                ->get(['id', 'name', 'description', 'color'])
                ->toArray();
        }

        $baseUrl = url('/');
        $configJson = json_encode([
            'enable_chatbot' => true,
            'primary_color' => $primaryColor,
            'widget_position' => $position,
            'widget_shape' => $shape,
            'launcher_style' => $launcherStyle,
            'enable_dark_mode' => $enableDarkMode,
            'enable_sound_notification' => $enableSound,
            'enable_departments' => $enableDepartments,
            'enable_visitor_tracking' => $enableVisitorTracking,
            'departments' => $departments,
        ], JSON_UNESCAPED_SLASHES);

        $js = <<<JS
(function() {
    var BASE_URL = '{$baseUrl}';
    var CONFIG = {$configJson};
    var renderedIds = new Set();
    var pollingInterval = null;

    function loadCSS() {
        var link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = BASE_URL + '/css/chatbot/chatbot-runtime.css';
        document.head.appendChild(link);
    }

    function escHtml(str) {
        if (!str) return '';
        var d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

    function getCsrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function fetchApi(path, body) {
        return fetch(BASE_URL + path, {
            method: body ? 'POST' : 'GET',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
            body: body ? JSON.stringify(body) : undefined,
        }).then(function(r) { if (!r.ok) throw new Error('API error ' + r.status); return r.json(); });
    }

    var W = {
        config: CONFIG,
        sessionId: localStorage.getItem('prayaag_chat_session') || 'embed_' + Date.now() + '_' + Math.random().toString(36).slice(2, 8),
        visitorName: localStorage.getItem('prayaag_chat_name') || '',
        departmentId: localStorage.getItem('prayaag_chat_dept') || '',
        conversationId: null,
        container: null,
        launcher: null,
        windowEl: null,
        bodyContent: null,
        messagesEl: null,
        inputField: null,
        sendBtn: null,

        init: function() {
            loadCSS();
            var posClass = CONFIG.widget_position === 'bottom-left' ? 'left-aligned' : '';
            var container = document.createElement('div');
            container.id = 'chatbot-widget-container';
            if (posClass) container.classList.add(posClass);
            container.innerHTML =
                '<div class="chatbot-launcher" style="background:' + CONFIG.primary_color + '">' +
                '<svg class="chatbot-launcher-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>' +
                '<svg class="chatbot-launcher-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none"><path d="M18 6 6 18M6 6l12 12"/></svg>' +
                '</div>' +
                '<div class="chatbot-window">' +
                '<div class="chatbot-header" style="background:' + CONFIG.primary_color + '">' +
                '<div class="chatbot-header-info"><div class="chatbot-avatar">P</div><div><div class="chatbot-header-title">Prayaag Help Desk</div><div class="chatbot-header-status"><span class="chatbot-status-dot"></span> <span class="chatbot-status-text">Online</span></div></div></div>' +
                '<div class="chatbot-header-actions"><button class="chatbot-btn-icon chatbot-close-btn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px"><path d="M18 15l-6-6-6 6"/></svg></button></div>' +
                '</div><div class="chatbot-body-content"></div></div>';
            document.body.appendChild(container);

            this.container = container;
            this.launcher = container.querySelector('.chatbot-launcher');
            this.windowEl = container.querySelector('.chatbot-window');
            this.bodyContent = container.querySelector('.chatbot-body-content');

            var self = this;
            this.launcher.addEventListener('click', function() {
                var open = self.windowEl.classList.contains('open');
                self.windowEl.classList.toggle('open');
                self.launcher.querySelector('.chatbot-launcher-icon').style.display = open ? 'flex' : 'none';
                self.launcher.querySelector('.chatbot-launcher-close').style.display = open ? 'none' : 'flex';
                if (!open && !self.conversationId) self.showScreen();
            });

            container.querySelector('.chatbot-close-btn').addEventListener('click', function() {
                self.closeWindow();
            });
        },

        closeWindow: function() {
            this.windowEl.classList.remove('open');
            this.launcher.querySelector('.chatbot-launcher-icon').style.display = 'flex';
            this.launcher.querySelector('.chatbot-launcher-close').style.display = 'none';
        },

        showScreen: function() {
            if (CONFIG.enable_departments && !this.departmentId) {
                this.renderDepartments();
            } else {
                this.renderForm();
            }
        },

        renderDepartments: function() {
            var self = this;
            this.bodyContent.innerHTML =
                '<div class="chatbot-departments"><div class="chatbot-departments-header"><h4>How can we help you?</h4><p>Select a department</p></div><div class="chatbot-departments-list" id="embed-dept-list"></div></div>';
            var list = document.getElementById('embed-dept-list');
            if (!list) return;
            var deps = CONFIG.departments || [];
            if (!deps.length) { this.departmentId = 'general'; this.renderForm(); return; }
            deps.forEach(function(dept) {
                var btn = document.createElement('button');
                btn.className = 'chatbot-dept-card';
                btn.innerHTML = '<span class="chatbot-dept-dot" style="background:' + (dept.color || CONFIG.primary_color) + '"></span><div class="chatbot-dept-info"><strong>' + escHtml(dept.name) + '</strong>' + (dept.description ? '<small>' + escHtml(dept.description) + '</small>' : '') + '</div><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;flex-shrink:0"><path d="m9 18 6-6-6-6"/></svg>';
                btn.addEventListener('click', function() {
                    self.departmentId = dept.id;
                    localStorage.setItem('prayaag_chat_dept', dept.id);
                    self.renderForm();
                });
                list.appendChild(btn);
            });
        },

        renderForm: function() {
            var self = this;
            if (pollingInterval) { clearInterval(pollingInterval); pollingInterval = null; }

            fetch(BASE_URL + '/chatbot/widget/form-fields', { headers: { 'Accept': 'application/json' } })
                .then(function(r) { if (!r.ok) throw new Error('Fields error'); return r.json(); })
                .then(function(fields) {
                    self.renderDynamicForm(fields);
                })
                .catch(function() {
                    self.renderDynamicForm([]);
                });
        },

        renderDynamicForm: function(fields) {
            var self = this;

            var fieldsHtml = '';
            if (fields.length === 0) {
                fieldsHtml =
                    '<input type="text" name="name" placeholder="Full Name" required value="' + escHtml(this.visitorName) + '">' +
                    '<input type="email" name="email" placeholder="Email (Optional)">' +
                    '<input type="text" name="phone" placeholder="Phone (Optional)">' +
                    '<select name="class"><option value="">Select Class (Optional)</option><option value="nursery">Nursery/KG</option><option value="primary">Primary (1-5)</option><option value="middle">Middle (6-8)</option><option value="secondary">Secondary (9-10)</option><option value="senior">Senior (11-12)</option></select>';
            } else {
                fields.forEach(function(f) {
                    var reqAttr = f.is_required ? 'required' : '';
                    var val = f.field_key === 'name' && self.visitorName ? escHtml(self.visitorName) : '';
                    var ph = f.placeholder ? escHtml(f.placeholder) : '';
                    var nameAttr = 'name="' + escHtml(f.field_key) + '"';
                    var idAttr = 'id="field-' + escHtml(f.field_key) + '"';

                    if (f.field_type === 'select') {
                        var opts = '<option value="">' + (ph || 'Select ' + f.label) + '</option>';
                        (f.options || []).forEach(function(o) {
                            opts += '<option value="' + escHtml(o) + '">' + escHtml(o) + '</option>';
                        });
                        fieldsHtml += '<select ' + nameAttr + ' ' + idAttr + ' ' + reqAttr + '>' + opts + '</select>';
                    } else if (f.field_type === 'textarea') {
                        fieldsHtml += '<textarea ' + nameAttr + ' ' + idAttr + ' placeholder="' + ph + '" ' + reqAttr + '></textarea>';
                    } else {
                        var inputType = f.field_type === 'email' ? 'email' : f.field_type === 'tel' ? 'tel' : f.field_type === 'number' ? 'number' : 'text';
                        fieldsHtml += '<input type="' + inputType + '" ' + nameAttr + ' ' + idAttr + ' placeholder="' + ph + '" ' + reqAttr + ' value="' + val + '">';
                    }
                });
            }

            this.bodyContent.innerHTML =
                '<form class="chatbot-prechat-form" id="embed-form">' +
                (CONFIG.enable_departments && this.departmentId ? '<div class="chatbot-back-bar"><button type="button" class="chatbot-back-btn" id="embed-back-dept"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><path d="m15 18-6-6 6-6"/></svg> Change Department</button></div>' : '') +
                '<h4>Start Chat</h4><p class="chatbot-form-subtitle">Please introduce yourself.</p>' +
                fieldsHtml +
                '<button type="submit" class="chatbot-start-btn" style="background:' + CONFIG.primary_color + '">Start Chat</button></form>';

            var backBtn = document.getElementById('embed-back-dept');
            if (backBtn) backBtn.addEventListener('click', function() { self.departmentId = ''; localStorage.removeItem('prayaag_chat_dept'); self.renderDepartments(); });

            document.getElementById('embed-form').addEventListener('submit', function(e) {
                e.preventDefault();
                var btn = e.target.querySelector('button[type="submit"]');
                if (btn) { btn.disabled = true; btn.innerText = 'Connecting...'; }

                var fd = new FormData(document.getElementById('embed-form'));
                var formData = {};
                fd.forEach(function(value, key) { formData[key] = value; });

                self.visitorName = formData['name'] || formData[Object.keys(formData)[0]] || 'Visitor';
                localStorage.setItem('prayaag_chat_name', self.visitorName);

                fetchApi('/chatbot/widget/lead', {
                    session_id: self.sessionId,
                    form_data: formData,
                    interest: 'Admission Inquiry'
                }).catch(function() {});

                self.startChat();
            });
        },

        startChat: function() {
            var self = this;
            fetchApi('/chatbot/widget/init', {
                session_id: this.sessionId,
                landing_page: window.location.pathname,
                referrer: document.referrer || '',
            }).then(function(data) {
                self.conversationId = data.conversation_id;
                self.renderChat();
                self.startPolling();
                if (data.messages && data.messages.length) {
                    data.messages.forEach(function(m) {
                        var sender = m.sender_type === 'visitor' ? 'visitor' : 'bot';
                        self.appendMessage(sender, m.message_text, m.id, m.message_type, m.metadata);
                    });
                }
                if (data.messages.length === 0) {
                    self.appendMessage('bot', 'Hello! Welcome to Prayaag International School. How can I help you today?');
                }
            }).catch(function() {
                self.bodyContent.innerHTML = '<div class="chatbot-departments" style="padding:40px;text-align:center;color:var(--cb-text-secondary)">Failed to connect. Please refresh and try again.</div>';
            });
        },

        renderChat: function() {
            var self = this;
            this.bodyContent.innerHTML =
                '<div class="chatbot-messages" id="embed-messages"></div>' +
                '<div class="chatbot-input-bar">' +
                '<input type="text" class="chatbot-input" id="embed-input" placeholder="Type a message...">' +
                '<div class="chatbot-input-actions">' +
                '<button class="chatbot-btn-icon chatbot-send-btn" id="embed-send"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px"><path d="M22 2 11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg></button>' +
                '</div></div>';

            this.messagesEl = document.getElementById('embed-messages');
            this.inputField = document.getElementById('embed-input');
            this.sendBtn = document.getElementById('embed-send');

            this.sendBtn.addEventListener('click', function() { self.sendMessage(); });
            this.inputField.addEventListener('keydown', function(e) { if (e.key === 'Enter') self.sendMessage(); });
        },

        sendMessage: function() {
            var text = this.inputField ? this.inputField.value.trim() : '';
            if (!text || !this.conversationId) return;
            this.inputField.value = '';
            var tempId = 'temp_' + Date.now();
            this.appendMessage('visitor', text, tempId);

            var self = this;
            fetchApi('/chatbot/widget/send', { conversation_id: this.conversationId, message: text })
                .then(function(data) {
                    renderedIds.delete(tempId);
                    if (data.visitor_message) renderedIds.add(data.visitor_message.id);
                    if (data.bot_message) self.appendMessage('bot', data.bot_message.message_text, data.bot_message.id);
                })
                .catch(function() {
                    renderedIds.delete(tempId);
                    self.appendMessage('bot', 'Failed to deliver message.');
                });
        },

        appendMessage: function(sender, text, id, type, metadata) {
            if (id && renderedIds.has(id)) return;
            if (id) renderedIds.add(id);
            if (!this.messagesEl) return;

            var bubble = document.createElement('div');
            bubble.className = 'chatbot-msg-bubble ' + sender;

            if (type === 'file' && metadata) {
                var fileUrl = metadata.file_url || '';
                var fileName = metadata.file_name || text;
                var fileType = (metadata.file_type || '').toLowerCase();
                var icon = fileType.startsWith('image') ? '🖼️' : fileType.startsWith('audio') ? '🎵' : fileType.startsWith('video') ? '🎬' : '📎';
                if (fileType.startsWith('image') && fileUrl) {
                    bubble.innerHTML = '<img src="' + escHtml(fileUrl) + '" alt="' + escHtml(fileName) + '" class="chatbot-msg-img" loading="lazy">';
                } else {
                    bubble.innerHTML = '<span class="chatbot-file-badge">' + icon + ' <a href="' + escHtml(fileUrl) + '" target="_blank" rel="noopener">' + escHtml(fileName) + '</a></span>';
                }
            } else {
                bubble.innerText = text;
            }

            this.messagesEl.appendChild(bubble);
            this.messagesEl.scrollTop = this.messagesEl.scrollHeight;
        },

        startPolling: function() {
            var self = this;
            if (pollingInterval) clearInterval(pollingInterval);
            pollingInterval = setInterval(function() {
                if (!self.conversationId) return;
                fetchApi('/chatbot/widget/conversations/' + self.conversationId + '/messages')
                    .then(function(messages) {
                        messages.forEach(function(m) {
                            var sender = m.sender_type === 'visitor' ? 'visitor' : 'bot';
                            if (m.sender_type !== 'visitor' && !renderedIds.has(m.id)) {
                                self.appendMessage(sender, m.message_text, m.id, m.message_type, m.metadata);
                            }
                        });
                    }).catch(function() {});
            }, 3000);
        },
    };

    W.init();
})();
JS;

        return response($js)
            ->header('Content-Type', 'application/javascript')
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, X-CSRF-TOKEN')
            ->header('Cache-Control', 'public, max-age=3600');
    }
}
