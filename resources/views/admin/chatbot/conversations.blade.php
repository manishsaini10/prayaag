@extends('admin.layout')

@section('title', 'Operator Live Chat Panel')

@section('actions')
    <a href="{{ url('/admin/chatbot') }}" class="btn-secondary inline-flex items-center gap-1.5">
        &larr; Back to Settings
    </a>
@endsection

@section('content')
<style>
.chat-panel-container {
    display: flex;
    flex-direction: column;
    height: calc(100vh - 240px);
    min-height: 450px;
    overflow: hidden;
    font-family: 'Outfit', 'Inter', system-ui, sans-serif;
}
@media (min-width: 768px) {
    .chat-panel-container {
        height: calc(100vh - 160px);
        min-height: 520px;
    }
}
.chat-bubble-visitor {
    background: #f1f5f9;
    color: #1e293b;
    border-radius: 16px;
    border-top-left-radius: 2px;
}
.chat-bubble-agent {
    background: var(--primary, #0b2545);
    color: #ffffff;
    border-radius: 16px;
    border-top-right-radius: 2px;
}
</style>

<div class="card p-0 overflow-hidden chat-panel-container" x-data="liveChat(@js($conversations->map(fn($c) => [
    'id' => $c->id,
    'status' => $c->status,
    'operator_id' => $c->operator_id,
    'updated_diff' => $c->updated_at->diffForHumans(null, true),
    'visitor' => [
        'name' => $c->visitor->name ?? 'Visitor',
        'email' => $c->visitor->email ?? 'N/A',
        'device' => $c->visitor->device ?? 'Desktop',
        'browser' => $c->visitor->browser ?? 'Chrome',
        'ip_address' => $c->visitor->ip_address ?? '0.0.0.0',
        'country' => $c->visitor->country ?? 'India',
    ]
])), @js($agents))">
    <div class="grid grid-cols-1 md:grid-cols-4 h-full w-full overflow-hidden">
        
        {{-- 1. Left panel: Conversations List --}}
        <div class="border-r flex flex-col h-full overflow-hidden" 
             :class="{ 'hidden md:flex': mobileView !== 'list', 'flex': mobileView === 'list' }"
             style="border-color:var(--border)">
            <div class="p-4 border-b font-bold text-base flex justify-between items-center shrink-0" style="border-color:var(--border); color:var(--text)">
                <span>Active Inboxes</span>
                <span class="text-xs font-normal text-green-600 flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-green-500 inline-block animate-pulse"></span> Live
                </span>
            </div>
            
            <div class="flex-grow overflow-y-auto divide-y" style="border-color:var(--border)">
                <template x-for="convo in conversations" :key="convo.id">
                    <div class="p-3.5 cursor-pointer hover:bg-surface-2/40 transition" 
                         :class="{'bg-surface-2/70 font-semibold': activeId === convo.id}"
                         @click="selectConvo(convo)">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-semibold" style="color:var(--text)" x-text="convo.visitor.name"></span>
                            <span class="text-xs" style="color:var(--text-muted)" x-text="convo.updated_diff"></span>
                        </div>
                        <div class="text-xs mt-1.5 flex justify-between items-center">
                            <span style="color:var(--text-muted)" x-text="'Status: ' + convo.status.toUpperCase()"></span>
                            <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-surface-2 text-muted" x-text="convo.visitor.device"></span>
                        </div>
                    </div>
                </template>
                <div x-show="conversations.length === 0" class="p-8 text-center text-xs" style="color:var(--text-muted)">
                    No active chats.
                </div>
            </div>
        </div>

        {{-- 2. Center panel: Message logs --}}
        <div class="md:col-span-2 flex flex-col h-full border-r overflow-hidden" 
             :class="{ 'hidden md:flex': mobileView !== 'chat', 'flex': mobileView === 'chat' }"
             style="border-color:var(--border)">
             
            {{-- Active Chat View --}}
            <div x-show="activeId" class="flex flex-col h-full justify-between overflow-hidden">
                {{-- Active Header --}}
                <div class="p-4 border-b flex justify-between items-center shrink-0 bg-surface-2/10" style="border-color:var(--border)">
                    <div class="flex items-center">
                        {{-- Back to Inbox list (Mobile only) --}}
                        <button class="md:hidden text-xs font-semibold px-2.5 py-1.5 bg-surface-2 border rounded-md mr-3 hover:bg-surface-2/80 transition" 
                                style="border-color:var(--border); color:var(--text)" 
                                @click="mobileView = 'list'">
                            &larr; Inbox
                        </button>
                        <div>
                            <span class="font-bold text-sm lg:text-base" style="color:var(--text)" x-text="activeName"></span>
                            <span class="text-xs block lg:inline lg:ml-2 text-green-600 font-semibold" x-text="activeEmail"></span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        {{-- View Info metadata (Mobile only) --}}
                        <button class="md:hidden text-xs font-semibold px-2.5 py-1.5 bg-blue-50 text-blue-700 border border-blue-200 rounded-md hover:bg-blue-100 transition" 
                                @click="mobileView = 'details'">
                            Info
                        </button>
                        <button class="text-xs font-semibold px-2.5 py-1.5 bg-red-100 text-red-800 rounded-md hover:bg-red-200 transition" @click="closeChat()">
                            Close
                        </button>
                    </div>
                </div>

                {{-- Message Logs Scrollable --}}
                <div class="flex-grow overflow-y-auto p-4 space-y-4 bg-surface-2/5 flex flex-col" id="chat-messages-container">
                    <template x-for="msg in messages" :key="msg.id">
                        <div class="flex items-start gap-2.5" :class="msg.sender_type === 'visitor' ? 'justify-start' : 'justify-end'">
                            
                            {{-- Visitor Avatar (Shown on left side of visitor messages) --}}
                            <template x-if="msg.sender_type === 'visitor'">
                                <div class="w-8 h-8 rounded-full bg-slate-200 border flex items-center justify-center text-xs font-bold text-slate-700 shrink-0 select-none">
                                    <span x-text="activeName.charAt(0).toUpperCase()"></span>
                                </div>
                            </template>
                            
                            <div class="flex flex-col max-w-[70%]">
                                <div class="p-3.5 text-sm shadow-sm leading-relaxed" 
                                     :class="msg.sender_type === 'visitor' ? 'chat-bubble-visitor' : 'chat-bubble-agent'">
                                    <div x-text="msg.message_text" class="whitespace-pre-wrap break-words"></div>
                                </div>
                                <span class="text-[10px] mt-1 px-1 text-slate-400" 
                                      :class="msg.sender_type === 'visitor' ? 'text-left' : 'text-right'" 
                                      x-text="formatTime(msg.created_at)"></span>
                            </div>

                            {{-- Agent Avatar (Shown on right side of agent/bot messages) --}}
                            <template x-if="msg.sender_type !== 'visitor'">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white shrink-0 select-none"
                                     :style="msg.sender_type === 'chatbot' ? 'background:#64748b' : 'background:var(--primary)'">
                                    <span x-text="msg.sender_type === 'chatbot' ? 'AI' : 'OP'"></span>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                {{-- Reply Box --}}
                <div class="p-3 border-t space-y-2 bg-surface shrink-0" style="border-color:var(--border)">
                    {{-- Quick reply tags --}}
                    <div class="flex gap-2 pb-1 overflow-x-auto select-none">
                        <button class="text-xs border px-2.5 py-1 rounded-full hover:bg-surface-2 transition shrink-0" style="border-color:var(--border); color:var(--text)" @click="quickSend('Admission details have been sent. How else can we help?')">Admission details</button>
                        <button class="text-xs border px-2.5 py-1 rounded-full hover:bg-surface-2 transition shrink-0" style="border-color:var(--border); color:var(--text)" @click="quickSend('Please share your phone number so we can call you.')">Request Phone</button>
                        <button class="text-xs border px-2.5 py-1 rounded-full hover:bg-surface-2 transition shrink-0" style="border-color:var(--border); color:var(--text)" @click="quickSend('The fee structure for the academic session 2026 has been published.')">Fees</button>
                    </div>
                    
                    <div class="flex gap-2">
                        <input type="text" x-model="replyText" @keydown.enter="sendReply()" placeholder="Type a reply and press enter..." class="flex-grow text-sm">
                        <button class="btn-primary" @click="sendReply()">Send</button>
                    </div>
                </div>
            </div>

            {{-- Empty State View --}}
            <div x-show="!activeId" class="flex-grow flex items-center justify-center p-8 text-center text-sm" style="color:var(--text-muted)">
                Select a conversation from the left menu to start chatting with visitors.
            </div>
        </div>

        {{-- 3. Right panel: Visitor Analytics Details --}}
        <div class="h-full overflow-y-auto p-4 space-y-6 overflow-x-hidden" 
             :class="{ 'hidden md:block': mobileView !== 'details', 'block': mobileView === 'details' }"
             style="color:var(--text); border-color:var(--border)">
             
            {{-- Active details --}}
            <div x-show="activeId" class="space-y-6">
                {{-- Back button (Mobile only) --}}
                <button class="md:hidden text-xs font-semibold px-3 py-1.5 bg-surface-2 border rounded-md mb-2 hover:bg-surface-2/80 transition" 
                        style="border-color:var(--border); color:var(--text)" 
                        @click="mobileView = 'chat'">
                    &larr; Back to Chat
                </button>

                <div>
                    <h4 class="font-bold text-sm mb-3">Visitor Details</h4>
                    <div class="space-y-2.5 text-xs">
                        <div class="flex justify-between"><span style="color:var(--text-muted)">IP Address:</span><span class="font-mono" x-text="visitorMeta.ip_address"></span></div>
                        <div class="flex justify-between"><span style="color:var(--text-muted)">Country:</span><span x-text="visitorMeta.country"></span></div>
                        <div class="flex justify-between"><span style="color:var(--text-muted)">Device:</span><span x-text="visitorMeta.device"></span></div>
                        <div class="flex justify-between"><span style="color:var(--text-muted)">Browser:</span><span x-text="visitorMeta.browser"></span></div>
                    </div>
                </div>

                <hr style="border-color:var(--border)">

                <div>
                    <h4 class="font-bold text-sm mb-3">Assign Operator</h4>
                    <div class="space-y-2">
                        <select class="w-full text-xs" x-model="operatorId" @change="assignChat()">
                            <option value="">Unassigned (AI Queue)</option>
                            <template x-for="agent in agents" :key="agent.id">
                                <option :value="agent.id" x-text="agent.name" :selected="agent.id === operatorId"></option>
                            </template>
                        </select>
                        <p class="text-[10px]" style="color:var(--text-muted)">
                            Assigning to a human agent disables automatic AI responses for this session.
                        </p>
                    </div>
                </div>

                <hr style="border-color:var(--border)">

                <div>
                    <h4 class="font-bold text-sm mb-3">Conversation Status</h4>
                    <div class="flex flex-wrap gap-1.5">
                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded font-semibold" x-text="'Inbox ID: ' + activeId.substr(0, 8)"></span>
                        <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded font-semibold" x-text="'Status: ' + status.toUpperCase()"></span>
                    </div>
                </div>

                <hr style="border-color:var(--border)">

                <div>
                    <h4 class="font-bold text-sm mb-2">Internal Notes</h4>
                    <textarea placeholder="Write private notes..." class="w-full text-xs" rows="4"></textarea>
                </div>
            </div>

            {{-- Empty details state --}}
            <div x-show="!activeId" class="text-center text-xs py-8" style="color:var(--text-muted)">
                Select chat to see metadata.
            </div>
        </div>

    </div>
</div>

<script>
function liveChat(initConvos, initAgents) {
    return {
        conversations: initConvos || [],
        agents: initAgents || [],
        activeId: null,
        activeName: '',
        activeEmail: '',
        messages: [],
        replyText: '',
        visitorMeta: {},
        status: 'open',
        operatorId: '',
        mobileView: 'list', // list, chat, details

        init() {
            // Keep fetching new messages and chat lists every 5 seconds
            setInterval(() => {
                this.fetchMessages();
                this.refreshConversations();
            }, 5000);
        },

        getCsrfToken() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.getAttribute('content') : '';
        },

        selectConvo(convo) {
            this.activeId = convo.id;
            this.activeName = convo.visitor.name;
            this.activeEmail = convo.visitor.email;
            this.visitorMeta = convo.visitor;
            this.status = convo.status;
            this.operatorId = convo.operator_id || '';
            this.mobileView = 'chat';
            this.fetchMessages();
        },

        fetchMessages() {
            if (!this.activeId) return;
            fetch(`{{ url('/admin/chatbot/conversations') }}/${this.activeId}/messages`, {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                // Only update and scroll if message counts changed or first load
                if (data.length !== this.messages.length) {
                    if (data.length > this.messages.length && this.messages.length > 0) {
                        const lastMsg = data[data.length - 1];
                        if (lastMsg.sender_type === 'visitor') {
                            this.playBell();
                        }
                    }
                    this.messages = data;
                    this.scrollToBottom();
                }
            })
            .catch(err => console.error('Error fetching chat messages:', err));
        },

        refreshConversations() {
            fetch(`{{ url('/admin/chatbot/conversations/list-json') }}`, {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.length > this.conversations.length) {
                    this.playBell();
                }
                this.conversations = data;
                // Keep status & operator_id synced if currently viewing a convo
                if (this.activeId) {
                    const current = data.find(c => c.id === this.activeId);
                    if (current) {
                        this.status = current.status;
                        this.operatorId = current.operator_id || '';
                    }
                }
            })
            .catch(err => console.error('Error syncing inboxes list:', err));
        },

        sendReply() {
            if (!this.replyText.trim() || !this.activeId) return;
            const text = this.replyText;
            this.replyText = '';
            
            fetch(`{{ url('/admin/chatbot/conversations') }}/${this.activeId}/messages`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken()
                },
                body: JSON.stringify({ message: text })
            })
            .then(res => {
                if (!res.ok) {
                    throw new Error('Message sending failed: HTTP ' + res.status);
                }
                return res.json();
            })
            .then(msg => {
                this.messages.push(msg);
                this.scrollToBottom();
                this.refreshConversations();
            })
            .catch(err => {
                console.error('Chat reply transmission error:', err);
                alert('Could not deliver message. Please retry.');
            });
        },

        quickSend(text) {
            this.replyText = text;
            this.sendReply();
        },

        assignChat() {
            if (!this.activeId) return;
            fetch(`{{ url('/admin/chatbot/conversations') }}/${this.activeId}/assign`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken()
                },
                body: JSON.stringify({ operator_id: this.operatorId })
            })
            .then(res => {
                if (!res.ok) throw new Error('Assignment failed');
                this.refreshConversations();
            })
            .catch(err => {
                console.error('Error assigning chat:', err);
                alert('Failed to update operator assignment.');
            });
        },

        closeChat() {
            if (!confirm('Are you sure you want to close this conversation?')) return;
            fetch(`{{ url('/admin/chatbot/conversations') }}/${this.activeId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken()
                },
                body: JSON.stringify({ status: 'closed' })
            })
            .then(res => {
                if (!res.ok) throw new Error('Status update failed');
                this.activeId = null;
                this.mobileView = 'list';
                this.refreshConversations();
            })
            .catch(err => {
                console.error('Close chat action failed:', err);
                alert('Action failed. Try again.');
            });
        },

        scrollToBottom() {
            setTimeout(() => {
                const el = document.getElementById('chat-messages-container');
                if (el) el.scrollTop = el.scrollHeight;
            }, 100);
        },

        formatTime(dateString) {
            const date = new Date(dateString);
            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        },

        playBell() {
            try {
                const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                const osc1 = audioCtx.createOscillator();
                const osc2 = audioCtx.createOscillator();
                const gainNode = audioCtx.createGain();
                
                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(587.33, audioCtx.currentTime); // D5
                osc1.frequency.setValueAtTime(880, audioCtx.currentTime + 0.08); // A5
                
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(293.66, audioCtx.currentTime); // D4
                osc2.frequency.setValueAtTime(440, audioCtx.currentTime + 0.08); // A4
                
                gainNode.gain.setValueAtTime(0.12, audioCtx.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.35);
                
                osc1.connect(gainNode);
                osc2.connect(gainNode);
                gainNode.connect(audioCtx.destination);
                
                osc1.start();
                osc2.start();
                osc1.stop(audioCtx.currentTime + 0.35);
                osc2.stop(audioCtx.currentTime + 0.35);
            } catch (e) {
                console.error('Audio chime error:', e);
            }
        }
    }
}
</script>
@endsection
