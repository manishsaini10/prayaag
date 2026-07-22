{{-- resources/views/admin/partials/ai-content-assistant.blade.php --}}

<style>
/* ── Premium AI Content Assistant Glassmorphism UI ── */
.ai-trigger-badge {
    position: absolute;
    display: none;
    align-items: center;
    gap: 4px;
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    color: #ffffff;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 6px;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.35);
    z-index: 100;
    transition: transform 0.15s ease, opacity 0.15s ease;
    border: 1px solid rgba(255, 255, 255, 0.15);
    user-select: none;
}
.ai-trigger-badge:hover {
    transform: scale(1.05);
}
.ai-trigger-badge svg {
    width: 10px;
    height: 10px;
    animation: pulse 1.5s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); opacity: 0.9; }
    50% { transform: scale(1.15); opacity: 1; }
    100% { transform: scale(1); opacity: 0.9; }
}

/* Modal Styling */
.ai-modal-bg {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.65);
    backdrop-filter: blur(8px);
    z-index: 9999;
    align-items: center;
    justify-content: center;
}
.ai-modal-bg.open {
    display: flex;
}
.ai-modal {
    background: rgba(30, 41, 59, 0.85);
    border: 1px solid rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(16px);
    border-radius: 16px;
    padding: 24px;
    width: 640px;
    max-width: 95vw;
    box-shadow: 0 24px 64px rgba(0, 0, 0, 0.6);
    color: #f1f5f9;
    font-family: system-ui, -apple-system, sans-serif;
    animation: aiModalIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes aiModalIn {
    from { opacity: 0; transform: scale(0.95) translateY(10px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}

.ai-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
.ai-modal-title {
    font-size: 18px;
    font-weight: 700;
    background: linear-gradient(135deg, #a5b4fc 0%, #818cf8 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    display: flex;
    align-items: center;
    gap: 8px;
}
.ai-modal-close {
    background: transparent;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    padding: 4px;
    border-radius: 6px;
    transition: background 0.15s;
}
.ai-modal-close:hover {
    background: rgba(255, 255, 255, 0.08);
    color: #f1f5f9;
}

/* Actions panel */
.ai-actions-grid {
    display: grid;
    grid-template-cols: repeat(5, 1fr);
    gap: 8px;
    margin-bottom: 18px;
}
.ai-action-tab {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    padding: 8px 4px;
    text-align: center;
    font-size: 11px;
    font-weight: 600;
    color: #cbd5e1;
    cursor: pointer;
    transition: all 0.15s;
}
.ai-action-tab:hover, .ai-action-tab.active {
    background: rgba(99, 102, 241, 0.15);
    border-color: rgba(99, 102, 241, 0.4);
    color: #a5b4fc;
}

/* Inputs & Comparison */
.ai-input-group {
    margin-bottom: 16px;
}
.ai-input-group label {
    display: block;
    font-size: 11px;
    font-weight: 600;
    color: #94a3b8;
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.ai-input-text {
    width: 100%;
    background: rgba(15, 23, 42, 0.4);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 8px;
    padding: 10px 12px;
    color: #f1f5f9;
    font-size: 13.5px;
    outline: none;
    transition: border-color 0.2s;
}
.ai-input-text:focus {
    border-color: #6366f1;
}

.ai-preview-container {
    display: grid;
    grid-template-cols: 1fr;
    gap: 12px;
    margin-bottom: 20px;
}
.ai-preview-box {
    background: rgba(15, 23, 42, 0.5);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 10px;
    padding: 14px;
    min-height: 120px;
    max-height: 240px;
    overflow-y: auto;
    font-size: 13.5px;
    line-height: 1.5;
    white-space: pre-wrap;
}

/* Buttons */
.ai-modal-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.ai-btn-generate {
    background: #6366f1;
    color: #ffffff;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 13.5px;
    cursor: pointer;
    transition: background 0.15s;
    display: flex;
    align-items: center;
    gap: 6px;
}
.ai-btn-generate:hover {
    background: #4f46e5;
}
.ai-btn-action {
    background: rgba(255, 255, 255, 0.05);
    color: #cbd5e1;
    border: 1px solid rgba(255, 255, 255, 0.08);
    padding: 8px 14px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.15s;
}
.ai-btn-action:hover {
    background: rgba(255, 255, 255, 0.1);
    color: #f1f5f9;
}
.ai-btn-accept {
    background: #10b981;
    color: #ffffff;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    transition: background 0.15s;
}
.ai-btn-accept:hover {
    background: #059669;
}
.ai-loading-spinner {
    display: none;
    width: 14px;
    height: 14px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top-color: #ffffff;
    border-radius: 50%;
    animation: spinner 0.6s linear infinite;
}
@keyframes spinner {
    to { transform: rotate(360deg); }
}
</style>

<!-- Floating AI trigger badge -->
<div id="ai-assist-badge" class="ai-trigger-badge">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
    <span>AI Assist</span>
</div>

<!-- AI Content Assistant Modal -->
<div class="ai-modal-bg" id="ai-content-assistant-modal">
    <div class="ai-modal">
        <div class="ai-modal-header">
            <h3 class="ai-modal-title">
                <svg style="width:18px;height:18px;color:#a5b4fc;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                <span>Prayaag AI Content Assistant</span>
            </h3>
            <button class="ai-modal-close" onclick="closeAiModal()">
                <svg style="width:20px;height:20px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Action tabs -->
        <div class="ai-actions-grid">
            <div class="ai-action-tab active" data-action="draft">Draft New</div>
            <div class="ai-action-tab" data-action="grammar">Proofread</div>
            <div class="ai-action-tab" data-action="rewrite">Change Tone</div>
            <div class="ai-action-tab" data-action="summarize">Summarize</div>
            <div class="ai-action-tab" data-action="seo_meta">SEO Meta</div>
        </div>

        <!-- Dynamic Inputs -->
        <div class="ai-input-group" id="ai-topic-group">
            <label>Topic / Prompt</label>
            <input type="text" id="ai-topic-input" class="ai-input-text" placeholder="e.g., Notice about upcoming summer vacation scheduled from June 1st...">
        </div>

        <div class="ai-input-group" id="ai-tone-group" style="display:none;">
            <label>Select Tone</label>
            <select id="ai-tone-input" class="ai-input-text">
                <option value="formal">Formal & Polite</option>
                <option value="friendly">Warm & Friendly</option>
                <option value="concise">Short & Direct</option>
                <option value="professional">Professional</option>
                <option value="exciting">Exciting & Inspiring</option>
            </select>
        </div>

        <!-- Preview Pane -->
        <div class="ai-input-group">
            <label id="ai-preview-label">AI Suggestion</label>
            <div class="ai-preview-container">
                <div class="ai-preview-box" id="ai-preview-content">Your generated text will appear here...</div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="ai-modal-footer">
            <button class="ai-btn-action" onclick="closeAiModal()">Discard</button>
            <div style="display:flex;gap:10px;">
                <button class="ai-btn-generate" onclick="generateAiContent()">
                    <span class="ai-loading-spinner" id="ai-spinner"></span>
                    <span id="ai-btn-text">Generate draft</span>
                </button>
                <button class="ai-btn-accept" id="ai-btn-accept" style="display:none;" onclick="acceptAiContent()">Accept</button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    let currentInput = null;
    let selectedAction = 'draft';
    const badge = document.getElementById('ai-assist-badge');
    const modal = document.getElementById('ai-content-assistant-modal');
    const topicGroup = document.getElementById('ai-topic-group');
    const toneGroup = document.getElementById('ai-tone-group');
    const previewContent = document.getElementById('ai-preview-content');
    const spinner = document.getElementById('ai-spinner');
    const btnText = document.getElementById('ai-btn-text');
    const btnAccept = document.getElementById('ai-btn-accept');

    // CSRF Token
    const getCsrf = () => {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.content : (window.__EDITOR__ ? window.__EDITOR__.csrf : '');
    };

    // Position badge on focus of text/textarea inputs
    document.addEventListener('focusin', function(e) {
        const target = e.target;
        if ((target.tagName === 'INPUT' && (target.type === 'text' || target.type === '')) || target.tagName === 'TEXTAREA') {
            if (target.id === 'ai-topic-input') return; // Skip modal inputs
            currentInput = target;
            showBadge(target);
        }
    });

    // Handle badge positioning
    function showBadge(el) {
        const rect = el.getBoundingClientRect();
        // Position at top-right inside the field boundaries
        badge.style.top = (window.scrollY + rect.top + 4) + 'px';
        badge.style.left = (window.scrollX + rect.right - 80) + 'px';
        badge.style.display = 'inline-flex';
    }

    // Hide badge if clicking elsewhere
    document.addEventListener('mousedown', function(e) {
        if (e.target !== badge && !badge.contains(e.target) && e.target !== currentInput) {
            badge.style.display = 'none';
        }
    });

    // Badge click -> Open Modal
    badge.addEventListener('click', function(e) {
        e.preventDefault();
        badge.style.display = 'none';
        if (currentInput) {
            // Set initial state
            previewContent.textContent = currentInput.value || "Let's polish this content or draft a new one...";
            btnAccept.style.display = currentInput.value ? 'inline-block' : 'none';
            
            // Auto select 'grammar' or 'rewrite' if field already has content
            if (currentInput.value.trim().length > 0) {
                switchTab('grammar');
            } else {
                switchTab('draft');
            }
            
            modal.classList.add('open');
        }
    });

    // Tab switching logic
    document.querySelectorAll('.ai-action-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            switchTab(this.dataset.action);
        });
    });

    function switchTab(action) {
        selectedAction = action;
        document.querySelectorAll('.ai-action-tab').forEach(t => t.classList.remove('active'));
        document.querySelector(`.ai-action-tab[data-action="${action}"]`).classList.add('active');

        // Toggle configuration input visibility
        if (action === 'draft') {
            topicGroup.style.display = 'block';
            toneGroup.style.display = 'none';
            btnText.textContent = 'Generate draft';
        } else if (action === 'rewrite') {
            topicGroup.style.display = 'none';
            toneGroup.style.display = 'block';
            btnText.textContent = 'Rewrite tone';
        } else {
            topicGroup.style.display = 'none';
            toneGroup.style.display = 'none';
            btnText.textContent = action === 'grammar' ? 'Proofread content' : (action === 'summarize' ? 'Summarize' : 'Optimize SEO');
        }
    }

    // Call server to generate content
    window.generateAiContent = async function() {
        if (!currentInput) return;
        const topic = document.getElementById('ai-topic-input').value.trim();
        const tone = document.getElementById('ai-tone-input').value;
        const content = currentInput.value;

        if (selectedAction === 'draft' && !topic) {
            alert('Please describe a topic for the draft.');
            return;
        }
        if (selectedAction !== 'draft' && !content) {
            alert('There is no content in the text field to analyze.');
            return;
        }

        // Loading UI state
        spinner.style.display = 'inline-block';
        btnText.textContent = 'Analyzing...';
        previewContent.textContent = 'Thinking and processing... Please wait.';

        try {
            const res = await fetch('/admin/ai-assist', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrf()
                },
                body: JSON.stringify({
                    action: selectedAction,
                    content: content,
                    topic: topic,
                    tone: tone
                })
            });

            const data = await res.json();
            if (res.ok && data.success) {
                // If it is SEO Meta, format JSON nicely
                if (selectedAction === 'seo_meta') {
                    try {
                        const parsed = JSON.parse(data.result);
                        previewContent.textContent = `🔑 SEO TITLE:\n${parsed.title}\n\n📝 SEO DESCRIPTION:\n${parsed.description}`;
                        previewContent.dataset.seoResult = data.result;
                    } catch (e) {
                        previewContent.textContent = data.result;
                    }
                } else {
                    previewContent.textContent = data.result;
                }
                btnAccept.style.display = 'inline-block';
            } else {
                previewContent.textContent = data.message || 'AI request failed.';
            }
        } catch (e) {
            previewContent.textContent = 'An error occurred while calling the AI. Please verify key credentials.';
        } finally {
            spinner.style.display = 'none';
            btnText.textContent = selectedAction === 'draft' ? 'Generate draft' : (selectedAction === 'rewrite' ? 'Rewrite tone' : 'Process');
        }
    };

    // Close Modal
    window.closeAiModal = function() {
        modal.classList.remove('open');
        document.getElementById('ai-topic-input').value = '';
    };

    // Apply AI content changes to target field
    window.acceptAiContent = function() {
        if (!currentInput) return;
        
        if (selectedAction === 'seo_meta' && previewContent.dataset.seoResult) {
            // Special handling to fill both SEO Meta fields if available
            try {
                const parsed = JSON.parse(previewContent.dataset.seoResult);
                
                // Try to find page layout SEO fields
                const seoTitleField = document.getElementById('f_seo_title') || document.querySelector('[name*="seo_title"]') || document.querySelector('[name*="title"]');
                const seoDescField = document.getElementById('f_seo_description') || document.querySelector('[name*="seo_description"]') || document.querySelector('[name*="description"]');
                
                if (seoTitleField) seoTitleField.value = parsed.title;
                if (seoDescField) seoDescField.value = parsed.description;
                
                currentInput.value = parsed.title; // Default fallback to input focused on
            } catch (e) {
                currentInput.value = previewContent.textContent;
            }
        } else {
            currentInput.value = previewContent.textContent;
        }

        // Trigger input event so framework bindings like Vue/Alpine trigger updates
        currentInput.dispatchEvent(new Event('input', { bubbles: true }));
        currentInput.dispatchEvent(new Event('change', { bubbles: true }));

        closeAiModal();
    };
})();
</script>
