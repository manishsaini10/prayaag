/**
 * Popup Builder Runtime Engine v2.0
 * Enterprise-grade responsive popup display with touch support
 */
(function () {
    'use strict';

    class PopupRuntime {
        constructor() {
            this.popups = new Map();
            this.activePopups = new Set();
            this.initialized = false;
            this.isLowEndDevice = false;
            this.storage = window.sessionStorage;
            this.localStorage = window.localStorage;
            this.config = {
                apiEndpoint: '/api/v1/popup',
                trackEndpoint: '/api/v1/popup/track',
                leadEndpoint: '/api/v1/popup/lead',
                exitIntentThreshold: 50,
                scrollThreshold: 50,
                checkInterval: 100,
            };
        }

        init() {
            if (this.initialized) return;
            this.initialized = true;

            // Detect low-end devices for animation optimization
            this.detectDeviceCapability();

            this.loadPopups();
            this.bindEvents();
            this.checkTriggers();
        }

        detectDeviceCapability() {
            const isMobile = /Android|iPhone|iPad|iPod|webOS|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            const hasLowMemory = navigator.deviceMemory && navigator.deviceMemory < 4;
            const hasSlowCPU = navigator.hardwareConcurrency && navigator.hardwareConcurrency < 4;
            this.isLowEndDevice = isMobile && (hasLowMemory || hasSlowCPU);

            // Reduce animation duration on low-end devices
            if (this.isLowEndDevice) {
                document.documentElement.style.setProperty('--popup-anim-duration', '0.15s');
            }
        }

        loadPopups() {
            document.querySelectorAll('.popup[data-popup-id]').forEach(el => {
                const id = el.dataset.popupId;
                if (!id || this.popups.has(id)) return;
                this.popups.set(id, {
                    id, element: el,
                    type: el.dataset.type || 'modal',
                    animation: el.dataset.animation || 'fade',
                    position: el.dataset.position || 'center-center',
                    width: parseInt(el.dataset.width) || 700,
                    delay: parseInt(el.dataset.delay) || 0,
                    frequency: el.dataset.frequency || 'once_per_session',
                    config: JSON.parse(el.dataset.config || '{}'),
                    triggered: false, shown: false,
                });
                
                // Track view event when popup is loaded on the page
                this.trackEvent(id, 'view');
            });

            // Watch for dynamic popups (scoped to popup-builder-output to avoid global perf hit)
            const observer = new MutationObserver(() => {
                document.querySelectorAll('.popup[data-popup-id]:not([data-popup-init])').forEach(el => {
                    el.dataset.popupInit = '1';
                    const id = el.dataset.popupId;
                    if (id && !this.popups.has(id)) {
                        this.popups.set(id, {
                            id, element: el, type: el.dataset.type || 'modal',
                            animation: el.dataset.animation || 'fade',
                            position: el.dataset.position || 'center-center',
                            width: parseInt(el.dataset.width) || 700,
                            delay: parseInt(el.dataset.delay) || 0,
                            frequency: el.dataset.frequency || 'once_per_session',
                            config: JSON.parse(el.dataset.config || '{}'),
                            triggered: false, shown: false,
                        });
                        this.checkSinglePopup(this.popups.get(id));
                        
                        // Track view event for dynamic popups
                        this.trackEvent(id, 'view');
                    }
                });
            });
            var target = document.querySelector('.popup-builder-output') || document.body;
            observer.observe(target, { childList: true, subtree: true });
        }

        bindEvents() {
            // Exit intent
            document.addEventListener('mouseout', (e) => {
                if (e.clientY > this.config.exitIntentThreshold) return;
                this.handleExitIntent();
            });

            // ESC close + focus trap
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') this.closeAll();
                if (e.key === 'Tab' && this.activePopups.size > 0) {
                    const activeId = [...this.activePopups][this.activePopups.size - 1];
                    const popup = this.popups.get(activeId);
                    if (popup && popup._firstFocusable && popup._lastFocusable) {
                        if (e.shiftKey && document.activeElement === popup._firstFocusable) {
                            e.preventDefault();
                            popup._lastFocusable.focus();
                        } else if (!e.shiftKey && document.activeElement === popup._lastFocusable) {
                            e.preventDefault();
                            popup._firstFocusable.focus();
                        }
                    }
                }
            });

            // Overlay click close
            document.addEventListener('click', (e) => {
                const overlay = e.target.closest('.popup-overlay');
                if (overlay) {
                    const id = overlay.dataset.popupId;
                    if (id) this.closePopup(id);
                }
            });

            // Close buttons (touch + click)
            const closeHandler = (e) => {
                const btn = e.target.closest('.popup-close');
                if (btn) {
                    const id = btn.dataset.popupId || btn.closest('[data-popup-id]')?.dataset.popupId;
                    if (id) { e.preventDefault(); e.stopPropagation(); this.closePopup(id); }
                }
            };
            document.addEventListener('click', closeHandler);
            document.addEventListener('touchstart', closeHandler, { passive: true });

            // Track link clicks inside popups for click analytics
            document.addEventListener('click', (e) => {
                const link = e.target.closest('[data-popup-id] a');
                if (link) {
                    if (link.closest('.popup-close') || link.closest('[data-popup-form]')) {
                        return;
                    }
                    const popupEl = link.closest('[data-popup-id]');
                    const id = popupEl ? popupEl.dataset.popupId : null;
                    if (id) {
                        this.trackEvent(id, 'click');
                    }
                }
            });

            // Form submissions
            document.addEventListener('submit', (e) => {
                const form = e.target.closest('[data-popup-form]');
                if (form) { e.preventDefault(); this.handleFormSubmit(form); }
            });

            // Scroll trigger (passive + throttled)
            let scrollTimer;
            window.addEventListener('scroll', () => {
                clearTimeout(scrollTimer);
                scrollTimer = setTimeout(() => this.handleScrollTrigger(), 150);
            }, { passive: true });

            // Resize handler
            let resizeTimer;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(() => this.handleResize(), 200);
            }, { passive: true });

            // Visibility change
            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'visible') this.handleVisibilityChange();
            });

            // Custom events
            window.addEventListener('popup:show', (e) => { if (e.detail?.id) this.showPopup(e.detail.id); });
            window.addEventListener('popup:hide', (e) => { if (e.detail?.id) this.closePopup(e.detail.id); });
            window.addEventListener('popup:refresh', () => this.loadPopups());
        }

        checkTriggers() {
            this.popups.forEach(p => this.checkSinglePopup(p));
        }

        checkSinglePopup(popup) {
            if (popup.shown || popup.triggered) return;
            const triggerType = popup.config.trigger || 'page_load';
            const delay = popup.config.delay || popup.delay || 0;

            if (['page_load', 'time_delay', 'after_x_seconds'].includes(triggerType)) {
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
                        el.addEventListener('click', (e) => { e.preventDefault(); this.showPopup(popup.id); });
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

            if (!this.checkFrequency(popup)) return;

            const overlay = document.querySelector(`.popup-overlay[data-popup-id="${id}"]`);

            // Show modal types with overlay
            if (overlay && !['floating_bar', 'announcement_bar'].includes(popup.type)) {
                overlay.style.display = '';
            }

            popup.element.style.display = '';
            popup.shown = true;
            this.activePopups.add(id);

            // Apply position
            this.positionPopup(popup);

            // Apply animation class
            if (!this.isLowEndDevice) {
                popup.element.classList.add(`popup-${popup.animation}`);
            } else {
                // Simple fade for low-end devices
                popup.element.style.animation = 'popup-fade-in 0.15s ease-out';
            }

            // Handle safe areas on mobile
            this.applySafeArea(popup);

            // Track impression
            this.trackEvent(id, 'impression');

            // Mark frequency on show (catches navigations away before close)
            this.markFrequency(popup);

            // Reveal lazy-loaded images
            popup.element.querySelectorAll('.popup-image[loading="lazy"]').forEach(function (img) {
                if (img.complete) { img.classList.add('loaded'); }
                else { img.addEventListener('load', function () { img.classList.add('loaded'); }); }
            });

            // Focus trap: move focus to popup and save previous
            popup._prevFocus = document.activeElement;
            const focusable = popup.element.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
            if (focusable.length) { focusable[0].focus(); }
            popup._firstFocusable = focusable[0];
            popup._lastFocusable = focusable[focusable.length - 1];

            // Screen reader announcement
            let srAnnounce = document.getElementById('popup-sr-announce');
            if (!srAnnounce) {
                srAnnounce = document.createElement('div');
                srAnnounce.id = 'popup-sr-announce';
                srAnnounce.setAttribute('aria-live', 'polite');
                srAnnounce.setAttribute('aria-atomic', 'true');
                srAnnounce.style.cssText = 'position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0;';
                document.body.appendChild(srAnnounce);
            }
            srAnnounce.textContent = ''; // Reset
            setTimeout(function () { srAnnounce.textContent = 'Popup opened. Press Escape to close.'; }, 100);

            // Lock body scroll for modals
            if (popup.type === 'modal' || popup.type === 'fullscreen') {
                document.body.style.overflow = 'hidden';
                document.body.style.touchAction = 'none';
            }

            // Handle fullscreen on mobile
            if (popup.type === 'fullscreen') {
                this.handleFullscreen(popup);
            }

            window.dispatchEvent(new CustomEvent('popup:shown', { detail: { id } }));

            // Auto-close
            if (popup.config.autoClose) {
                setTimeout(() => this.closePopup(id), parseInt(popup.config.autoClose) * 1000);
            }
        }

        closePopup(id) {
            const popup = this.popups.get(id);
            if (!popup || !popup.shown) return;

            const overlay = document.querySelector(`.popup-overlay[data-popup-id="${id}"]`);
            if (overlay) overlay.style.display = 'none';
            popup.element.style.display = 'none';
            popup.shown = false;
            this.activePopups.delete(id);

            // Restore body scroll
            if (this.activePopups.size === 0) {
                document.body.style.overflow = '';
                document.body.style.touchAction = '';
            }

            this.trackEvent(id, 'close');
            this.markFrequency(popup);

            // Restore focus to previous element
            if (popup._prevFocus && popup._prevFocus.focus) {
                popup._prevFocus.focus();
            }

            window.dispatchEvent(new CustomEvent('popup:hidden', { detail: { id } }));
        }

        closeAll() {
            [...this.activePopups].forEach(id => this.closePopup(id));
        }

        positionPopup(popup) {
            const isMobile = window.innerWidth <= 768;
            const gutter = window.innerWidth <= 480 ? 16 : (isMobile ? 20 : 32);
            const width = Math.min(popup.width, window.innerWidth - gutter);
            const content = popup.element.querySelector('.popup-content');

            popup.element.classList.add(`popup-type-${popup.type}`);
            popup.element.style.setProperty('--popup-runtime-width', width + 'px');
            if (content) {
                content.style.setProperty('--popup-runtime-width', width + 'px');
            }

            if (['floating_bar', 'announcement_bar'].includes(popup.type)) {
                Object.assign(popup.element.style, {
                    position: 'fixed', left: '0', right: '0',
                    zIndex: '999999',
                    top: popup.position?.includes('bottom') ? 'auto' : '0',
                    bottom: popup.position?.includes('bottom') ? '0' : 'auto',
                    maxWidth: '100%',
                    width: '100%',
                });
                return;
            }

            if (popup.type === 'slide_in') {
                const slideWidth = isMobile ? '100%' : width + 'px';
                Object.assign(popup.element.style, {
                    position: 'fixed',
                    zIndex: '999999',
                    bottom: '0',
                    left: isMobile ? '0' : 'auto',
                    right: isMobile ? '0' : '24px',
                    maxWidth: slideWidth,
                    width: slideWidth,
                });
                return;
            }

            if (popup.type === 'fullscreen') {
                Object.assign(popup.element.style, {
                    position: 'fixed', top: '0', left: '0', right: '0', bottom: '0',
                    zIndex: '999999', display: 'flex', alignItems: 'center', justifyContent: 'center',
                });
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
                zIndex: popup.config?.z_index || '999999',
                maxWidth: width + 'px',
                width: isMobile ? `calc(100vw - ${gutter}px)` : '90%',
                top: pos.top || 'auto', left: pos.left || 'auto',
                right: pos.right || 'auto', bottom: pos.bottom || 'auto',
                transform: pos.transform || 'none',
            });

            // On very small screens, force center
            if (window.innerWidth <= 480) {
                Object.assign(popup.element.style, {
                    top: '50%', left: '50%',
                    transform: 'translate(-50%, -50%)',
                    right: 'auto', bottom: 'auto',
                });
            }
        }

        applySafeArea(popup) {
            if (popup.type === 'fullscreen' && CSS.supports('padding', 'max(0px)')) {
                popup.element.style.paddingTop = 'max(20px, env(safe-area-inset-top))';
                popup.element.style.paddingBottom = 'max(20px, env(safe-area-inset-bottom))';
                popup.element.style.paddingLeft = 'max(16px, env(safe-area-inset-left))';
                popup.element.style.paddingRight = 'max(16px, env(safe-area-inset-right))';
            }
        }

        handleFullscreen(popup) {
            // Ensure fullscreen popup fills the viewport on mobile
            if (window.innerWidth <= 768) {
                const content = popup.element.querySelector('.popup-content');
                if (content) {
                    content.style.maxWidth = '100%';
                    content.style.width = '100%';
                    content.style.maxHeight = '100vh';
                    content.style.borderRadius = '0';
                    content.style.margin = '0';
                }
            }
        }

        handleResize() {
            this.activePopups.forEach(id => {
                const popup = this.popups.get(id);
                if (popup) this.positionPopup(popup);
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
                if (['after_login', 'after_logout'].includes(popup.config.trigger) && !popup.shown && !popup.triggered) {
                    popup.triggered = true;
                    this.showPopup(popup.id);
                }
            });
        }

        handleFormSubmit(form) {
            const formData = new FormData(form);
            const data = {};
            formData.forEach((value, key) => { data[key] = value; });

            const popupId = form.closest('[data-popup-id]')?.dataset.popupId;
            if (!popupId) return;

            this.submitLead(popupId, data);
            this.trackEvent(popupId, 'conversion');

            const successMsg = form.dataset.successMessage || 'Thank you! We will get back to you soon.';
            form.innerHTML = `<div class="popup-form-success" style="text-align:center;padding:20px">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" style="margin:0 auto 12px">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <p style="font-size:16px;color:#374151">${successMsg}</p>
            </div>`;

            if (form.dataset.autoClose) {
                setTimeout(() => this.closePopup(popupId), parseInt(form.dataset.autoClose) * 1000);
            }
        }

        checkFrequency(popup) {
            const type = popup.frequency || 'once_per_session';
            const key = `popup_${popup.id}`;
            switch (type) {
                case 'once_per_session': return !this.storage.getItem(key);
                case 'once_per_day': return this.localStorage.getItem(`${key}_day`) !== new Date().toDateString();
                case 'weekly': return this.localStorage.getItem(`${key}_week`) !== String(this.getWeekNumber());
                case 'monthly': return this.localStorage.getItem(`${key}_month`) !== `${new Date().getFullYear()}-${new Date().getMonth()}`;
                case 'once_only': return !this.localStorage.getItem(`${key}_once`);
                case 'never_again': return !this.localStorage.getItem(`${key}_never`);
                default: return true;
            }
        }

        markFrequency(popup) {
            const type = popup.frequency || 'once_per_session';
            const key = `popup_${popup.id}`;
            switch (type) {
                case 'once_per_session': this.storage.setItem(key, '1'); break;
                case 'once_per_day': this.localStorage.setItem(`${key}_day`, new Date().toDateString()); break;
                case 'weekly': this.localStorage.setItem(`${key}_week`, String(this.getWeekNumber())); break;
                case 'monthly': this.localStorage.setItem(`${key}_month`, `${new Date().getFullYear()}-${new Date().getMonth()}`); break;
                case 'once_only': this.localStorage.setItem(`${key}_once`, '1'); break;
                case 'never_again': this.localStorage.setItem(`${key}_never`, '1'); break;
            }
        }

        trackEvent(popupId, eventType) {
            const payload = {
                popup_id: popupId, event_type: eventType,
                url: window.location.href, referrer: document.referrer || '',
                user_agent: navigator.userAgent,
                session_id: this.storage.getItem('popup_session_id') || this.generateSessionId(),
                timestamp: new Date().toISOString(),
                device_type: this.getDeviceType(),
            };
            if (navigator.sendBeacon) {
                const body = new Blob([JSON.stringify(payload)], { type: 'application/json' });
                navigator.sendBeacon(this.config.trackEndpoint, body);
            } else {
                fetch(this.config.trackEndpoint, {
                    method: 'POST', body: JSON.stringify(payload),
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.getCsrfToken() },
                    keepalive: true,
                }).catch(() => {});
            }
        }

        submitLead(popupId, data) {
            const payload = {
                popup_id: popupId, form_data: data,
                url: window.location.href, referrer: document.referrer || '',
                user_agent: navigator.userAgent,
                session_id: this.storage.getItem('popup_session_id'),
            };
            fetch(this.config.leadEndpoint, {
                method: 'POST', body: JSON.stringify(payload),
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.getCsrfToken() },
            }).catch(() => {});
        }

        getDeviceType() {
            const ua = navigator.userAgent;
            if (/iPad|Android(?!.*Mobile)|Tablet/i.test(ua)) return 'tablet';
            if (/Mobile|iPhone|Android.*Mobile/i.test(ua)) return 'mobile';
            return 'desktop';
        }

        getWeekNumber() {
            const now = new Date();
            const start = new Date(now.getFullYear(), 0, 1);
            return Math.ceil(((now - start + (start.getTimezoneOffset() - now.getTimezoneOffset()) * 60000) / 86400000 + start.getDay() + 1) / 7);
        }

        generateSessionId() {
            const id = 'ps_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            this.storage.setItem('popup_session_id', id);
            return id;
        }

        getCsrfToken() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.getAttribute('content') : '';
        }
    }

    // Initialize
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            window.PopupRuntime = new PopupRuntime();
            window.PopupRuntime.init();
        });
    } else {
        window.PopupRuntime = new PopupRuntime();
        window.PopupRuntime.init();
    }

    if (typeof module !== 'undefined' && module.exports) {
        module.exports = PopupRuntime;
    }
})();
