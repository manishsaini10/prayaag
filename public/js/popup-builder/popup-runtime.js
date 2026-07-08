/**
 * Popup Builder Runtime Engine v1.0
 * Enterprise-grade popup display, trigger, and analytics engine
 */
(function () {
    'use strict';

    class PopupRuntime {
        constructor() {
            this.popups = new Map();
            this.activePopups = new Set();
            this.initialized = false;
            this.sessionStorage = window.sessionStorage;
            this.localStorage = window.localStorage;
            this.config = {
                apiEndpoint: '/__popup/api',
                trackEndpoint: '/__popup/track',
                leadEndpoint: '/__popup/lead',
                exitIntentThreshold: 50,
                scrollThreshold: 50,
                checkInterval: 100,
            };
        }

        init() {
            if (this.initialized) return;
            this.initialized = true;
            this.loadPopups();
            this.bindEvents();
            this.checkTriggers();
        }

        loadPopups() {
            const popupElements = document.querySelectorAll('[data-popup-id]');
            popupElements.forEach(el => {
                const id = el.dataset.popupId;
                if (!id) return;
                this.popups.set(id, {
                    id: id,
                    element: el,
                    type: el.dataset.type || 'modal',
                    animation: el.dataset.animation || 'fade',
                    position: el.dataset.position || 'center-center',
                    width: parseInt(el.dataset.width) || 600,
                    delay: parseInt(el.dataset.delay) || 0,
                    frequency: el.dataset.frequency || 'once_per_session',
                    config: JSON.parse(el.dataset.config || '{}'),
                    triggered: false,
                    shown: false,
                });
            });

            // Listen for dynamically added popups
            const observer = new MutationObserver(() => {
                document.querySelectorAll('[data-popup-id]:not([data-popup-initialized])').forEach(el => {
                    el.dataset.popupInitialized = 'true';
                    const id = el.dataset.popupId;
                    if (id && !this.popups.has(id)) {
                        this.popups.set(id, {
                            id, element: el,
                            type: el.dataset.type || 'modal',
                            animation: el.dataset.animation || 'fade',
                            position: el.dataset.position || 'center-center',
                            width: parseInt(el.dataset.width) || 600,
                            delay: parseInt(el.dataset.delay) || 0,
                            frequency: el.dataset.frequency || 'once_per_session',
                            config: JSON.parse(el.dataset.config || '{}'),
                            triggered: false, shown: false,
                        });
                        this.checkSinglePopup(this.popups.get(id));
                    }
                });
            });
            observer.observe(document.body, { childList: true, subtree: true });
        }

        bindEvents() {
            // Exit intent
            document.addEventListener('mouseout', (e) => {
                if (e.clientY > this.config.exitIntentThreshold) return;
                this.handleExitIntent();
            });

            // ESC close
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.closeAll();
                }
            });

            // Overlay click close
            document.addEventListener('click', (e) => {
                if (e.target.classList.contains('popup-overlay')) {
                    const id = e.target.dataset.popupId;
                    if (id) this.closePopup(id);
                }
            });

            // Close buttons
            document.addEventListener('click', (e) => {
                const btn = e.target.closest('.popup-close');
                if (btn) {
                    const id = btn.dataset.popupId;
                    if (id) {
                        e.preventDefault();
                        e.stopPropagation();
                        this.closePopup(id);
                    }
                }
            });

            // Form submissions
            document.addEventListener('submit', (e) => {
                const form = e.target.closest('[data-popup-form]');
                if (form) {
                    e.preventDefault();
                    this.handleFormSubmit(form);
                }
            });

            // Scroll trigger
            let scrollTimer;
            window.addEventListener('scroll', () => {
                clearTimeout(scrollTimer);
                scrollTimer = setTimeout(() => this.handleScrollTrigger(), 100);
            }, { passive: true });

            // Visibility change (returning visitor)
            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'visible') {
                    this.handleVisibilityChange();
                }
            });

            // Custom events
            window.addEventListener('popup:show', (e) => {
                if (e.detail && e.detail.id) this.showPopup(e.detail.id);
            });

            window.addEventListener('popup:hide', (e) => {
                if (e.detail && e.detail.id) this.closePopup(e.detail.id);
            });

            window.addEventListener('popup:refresh', () => {
                this.loadPopups();
            });
        }

        checkTriggers() {
            this.popups.forEach((popup) => {
                this.checkSinglePopup(popup);
            });
        }

        checkSinglePopup(popup) {
            if (popup.shown || popup.triggered) return;

            const triggerType = popup.config.trigger || 'page_load';
            const delay = popup.config.delay || popup.delay || 0;

            if (triggerType === 'page_load' || triggerType === 'time_delay' || triggerType === 'after_x_seconds') {
                setTimeout(() => {
                    if (!popup.shown && !popup.triggered) {
                        popup.triggered = true;
                        this.showPopup(popup.id);
                    }
                }, delay);
            }

            if (triggerType === 'click') {
                const selector = popup.config.clickSelector;
                if (selector) {
                    document.querySelectorAll(selector).forEach(el => {
                        el.addEventListener('click', (e) => {
                            e.preventDefault();
                            this.showPopup(popup.id);
                        });
                    });
                }
            }

            if (triggerType === 'scroll_percent') {
                popup.config._scrollCheck = true;
                popup.config._scrollPercent = parseInt(popup.config.scrollPercent) || 50;
            }

            if (triggerType === 'exit_intent') {
                popup.config._exitIntent = true;
            }
        }

        showPopup(id) {
            const popup = this.popups.get(id);
            if (!popup || popup.shown) return;

            // Check frequency rules
            if (!this.checkFrequency(popup)) return;

            const overlay = document.querySelector(`.popup-overlay[data-popup-id="${id}"]`);
            popup.element.style.display = '';
            popup.shown = true;
            this.activePopups.add(id);

            // Show overlay for modal types
            if (overlay && popup.type !== 'floating_bar' && popup.type !== 'announcement_bar') {
                overlay.style.display = '';
            }

            // Position
            this.positionPopup(popup);

            // Animation class
            popup.element.classList.add(`popup-${popup.animation}`);

            // Track impression
            this.trackEvent(id, 'impression');

            // Lock body scroll for modals
            if (popup.type === 'modal') {
                document.body.style.overflow = 'hidden';
            }

            // Dispatch event
            window.dispatchEvent(new CustomEvent('popup:shown', { detail: { id } }));

            // Auto-close for non-modal types
            if (popup.config.autoClose) {
                setTimeout(() => this.closePopup(id), parseInt(popup.config.autoClose) * 1000);
            }
        }

        closePopup(id) {
            const popup = this.popups.get(id);
            if (!popup || !popup.shown) return;

            const overlay = document.querySelector(`.popup-overlay[data-popup-id="${id}"]`);
            popup.element.style.display = 'none';
            if (overlay) overlay.style.display = 'none';
            popup.shown = false;
            this.activePopups.delete(id);

            // Restore body scroll
            if (this.activePopups.size === 0) {
                document.body.style.overflow = '';
            }

            // Track close
            this.trackEvent(id, 'close');

            // Mark as shown in session
            this.markFrequency(popup);

            window.dispatchEvent(new CustomEvent('popup:hidden', { detail: { id } }));
        }

        closeAll() {
            [...this.activePopups].forEach(id => this.closePopup(id));
        }

        positionPopup(popup) {
            if (popup.type === 'floating_bar' || popup.type === 'announcement_bar') {
                popup.element.style.cssText += 'position:fixed;left:0;right:0;z-index:999999;';
                if (popup.position === 'bottom-center' || popup.position === 'bottom-left' || popup.position === 'bottom-right') {
                    popup.element.style.bottom = '0';
                } else {
                    popup.element.style.top = '0';
                }
                return;
            }

            const positions = {
                'center-center': { top: '50%', left: '50%', transform: 'translate(-50%, -50%)' },
                'top-left': { top: '20px', left: '20px' },
                'top-center': { top: '20px', left: '50%', transform: 'translateX(-50%)' },
                'top-right': { top: '20px', right: '20px' },
                'bottom-left': { bottom: '20px', left: '20px' },
                'bottom-center': { bottom: '20px', left: '50%', transform: 'translateX(-50%)' },
                'bottom-right': { bottom: '20px', right: '20px' },
            };

            const pos = positions[popup.position] || positions['center-center'];
            Object.assign(popup.element.style, {
                position: 'fixed',
                zIndex: '999999',
                maxWidth: popup.width + 'px',
                width: '90%',
                top: pos.top || 'auto',
                left: pos.left || 'auto',
                right: pos.right || 'auto',
                bottom: pos.bottom || 'auto',
                transform: pos.transform || 'none',
            });
        }

        handleExitIntent() {
            this.popups.forEach((popup) => {
                if (popup.config._exitIntent && !popup.shown && !popup.triggered) {
                    popup.triggered = true;
                    this.showPopup(popup.id);
                }
            });
        }

        handleScrollTrigger() {
            const scrollPercent = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
            this.popups.forEach((popup) => {
                if (popup.config._scrollCheck && !popup.shown && !popup.triggered && scrollPercent >= popup.config._scrollPercent) {
                    popup.triggered = true;
                    this.showPopup(popup.id);
                }
            });
        }

        handleVisibilityChange() {
            this.popups.forEach((popup) => {
                if (popup.config.trigger === 'after_login' || popup.config.trigger === 'after_logout') {
                    if (!popup.shown && !popup.triggered) {
                        popup.triggered = true;
                        this.showPopup(popup.id);
                    }
                }
            });
        }

        handleFormSubmit(form) {
            const formData = new FormData(form);
            const data = {};
            formData.forEach((value, key) => { data[key] = value; });

            const popupId = form.closest('[data-popup-id]')?.dataset.popupId;
            if (!popupId) return;

            // Submit lead via AJAX
            this.submitLead(popupId, data);

            // Track conversion
            this.trackEvent(popupId, 'conversion');

            // Show success message
            const successMsg = form.dataset.successMessage || 'Thank you! We will get back to you soon.';
            form.innerHTML = `<div class="popup-form-success" style="text-align:center;padding:20px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" style="margin:0 auto 12px;">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <p style="font-size:16px;color:#374151;">${successMsg}</p>
            </div>`;

            // Auto-close after success
            if (form.dataset.autoClose) {
                setTimeout(() => this.closePopup(popupId), parseInt(form.dataset.autoClose) * 1000);
            }
        }

        checkFrequency(popup) {
            const type = popup.frequency || 'once_per_session';
            const key = `popup_${popup.id}`;

            switch (type) {
                case 'once_per_session':
                    if (this.sessionStorage.getItem(key)) return false;
                    break;
                case 'once_per_day':
                    const today = new Date().toDateString();
                    if (this.localStorage.getItem(`${key}_day`) === today) return false;
                    break;
                case 'weekly':
                    const week = this.getWeekNumber();
                    if (this.localStorage.getItem(`${key}_week`) === String(week)) return false;
                    break;
                case 'monthly':
                    const month = `${new Date().getFullYear()}-${new Date().getMonth()}`;
                    if (this.localStorage.getItem(`${key}_month`) === month) return false;
                    break;
                case 'once_only':
                    if (this.localStorage.getItem(`${key}_once`)) return false;
                    break;
                case 'never_again':
                    if (this.localStorage.getItem(`${key}_never`)) return false;
                    break;
            }
            return true;
        }

        markFrequency(popup) {
            const type = popup.frequency || 'once_per_session';
            const key = `popup_${popup.id}`;

            switch (type) {
                case 'once_per_session':
                    this.sessionStorage.setItem(key, 'true');
                    break;
                case 'once_per_day':
                    this.localStorage.setItem(`${key}_day`, new Date().toDateString());
                    break;
                case 'weekly':
                    this.localStorage.setItem(`${key}_week`, String(this.getWeekNumber()));
                    break;
                case 'monthly':
                    this.localStorage.setItem(`${key}_month`, `${new Date().getFullYear()}-${new Date().getMonth()}`);
                    break;
                case 'once_only':
                    this.localStorage.setItem(`${key}_once`, 'true');
                    break;
                case 'never_again':
                    this.localStorage.setItem(`${key}_never`, 'true');
                    break;
            }
        }

        trackEvent(popupId, eventType) {
            const payload = {
                popup_id: popupId,
                event_type: eventType,
                url: window.location.href,
                referrer: document.referrer || '',
                user_agent: navigator.userAgent,
                session_id: this.sessionStorage.getItem('popup_session_id') || this.generateSessionId(),
                timestamp: new Date().toISOString(),
            };

            // Send beacon (non-blocking)
            if (navigator.sendBeacon) {
                navigator.sendBeacon(this.config.trackEndpoint, JSON.stringify(payload));
            } else {
                fetch(this.config.trackEndpoint, {
                    method: 'POST',
                    body: JSON.stringify(payload),
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.getCsrfToken() },
                    keepalive: true,
                }).catch(() => {});
            }
        }

        submitLead(popupId, data) {
            const payload = {
                popup_id: popupId,
                form_data: data,
                url: window.location.href,
                referrer: document.referrer || '',
                user_agent: navigator.userAgent,
                session_id: this.sessionStorage.getItem('popup_session_id'),
            };

            fetch(this.config.leadEndpoint, {
                method: 'POST',
                body: JSON.stringify(payload),
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.getCsrfToken() },
            }).catch(() => {});
        }

        getWeekNumber() {
            const now = new Date();
            const start = new Date(now.getFullYear(), 0, 1);
            const diff = now - start + (start.getTimezoneOffset() - now.getTimezoneOffset()) * 60000;
            return Math.ceil((diff / 86400000 + start.getDay() + 1) / 7);
        }

        generateSessionId() {
            const id = 'ps_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            this.sessionStorage.setItem('popup_session_id', id);
            return id;
        }

        getCsrfToken() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.getAttribute('content') : '';
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            window.PopupRuntime = new PopupRuntime();
            window.PopupRuntime.init();
        });
    } else {
        window.PopupRuntime = new PopupRuntime();
        window.PopupRuntime.init();
    }

    // Export for module systems
    if (typeof module !== 'undefined' && module.exports) {
        module.exports = PopupRuntime;
    }
})();
