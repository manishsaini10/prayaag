/* ============================================================================
   Prayaag International School — Frontend interactions (vanilla, deferred)
   Scroll reveal · counters · sticky header · mobile drawer.
   No dependencies; safe to load with `defer`.
   ========================================================================== */
(function () {
    "use strict";

    /* ---------- Scroll reveal (Intersection Observer) ---------- */
    var revealEls = document.querySelectorAll("[data-reveal]");
    if (revealEls.length) {
        if (!("IntersectionObserver" in window)) {
            revealEls.forEach(function (el) { el.classList.add("is-visible"); });
        } else {
            var io = new IntersectionObserver(function (entries, obs) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("is-visible");
                        obs.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12, rootMargin: "0px 0px -8% 0px" });
            revealEls.forEach(function (el) { io.observe(el); });
        }
    }

    /* ---------- Animated counters ([data-count]) ---------- */
    var counters = document.querySelectorAll("[data-count]");
    if (counters.length && "IntersectionObserver" in window) {
        var cio = new IntersectionObserver(function (entries, obs) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                var el = entry.target;
                var target = parseFloat(el.getAttribute("data-count")) || 0;
                var suffix = el.getAttribute("data-suffix") || "";
                var dur = 1400, start = null;
                function step(ts) {
                    if (!start) start = ts;
                    var p = Math.min((ts - start) / dur, 1);
                    var eased = 1 - Math.pow(1 - p, 3);
                    el.textContent = Math.round(target * eased).toLocaleString() + suffix;
                    if (p < 1) requestAnimationFrame(step);
                }
                requestAnimationFrame(step);
                obs.unobserve(el);
            });
        }, { threshold: 0.5 });
        counters.forEach(function (el) { cio.observe(el); });
    }

    /* ---------- Sticky header shrink ---------- */
    var head = document.querySelector(".site-head");
    if (head) {
        var onScroll = function () {
            if (window.scrollY > 30) head.classList.add("is-stuck");
            else head.classList.remove("is-stuck");
        };
        onScroll();
        window.addEventListener("scroll", onScroll, { passive: true });
    }

    /* ---------- Mobile drawer ---------- */
    var toggle = document.querySelector(".menu-toggle");
    var drawer = document.querySelector(".drawer");
    var backdrop = document.querySelector(".drawer-backdrop");
    var closeBtn = document.querySelector(".drawer-close");
    function openDrawer() { if (drawer) drawer.classList.add("open"); if (backdrop) backdrop.classList.add("open"); document.body.style.overflow = "hidden"; }
    function closeDrawer() { if (drawer) drawer.classList.remove("open"); if (backdrop) backdrop.classList.remove("open"); document.body.style.overflow = ""; }
    if (toggle) toggle.addEventListener("click", openDrawer);
    if (closeBtn) closeBtn.addEventListener("click", closeDrawer);
    if (backdrop) backdrop.addEventListener("click", closeDrawer);

    /* ---------- Drawer accordion (parent items with children) ---------- */
    document.querySelectorAll(".drawer .has-children > a").forEach(function (a) {
        a.addEventListener("click", function (e) {
            var sub = a.parentElement.querySelector(".submenu");
            if (sub) { e.preventDefault(); sub.style.display = sub.style.display === "block" ? "none" : "block"; }
        });
    });

    /* ---------- Hero slider (auto-rotate / crossfade) ---------- */
    document.querySelectorAll(".hero-slides").forEach(function (wrap) {
        var slides = wrap.querySelectorAll(".hero-slide");
        if (slides.length < 2) return;
        var i = 0;
        setInterval(function () {
            slides[i].classList.remove("is-active");
            i = (i + 1) % slides.length;
            slides[i].classList.add("is-active");
        }, 5000);
    });

    /* ---------- Auto-scrolling sliders (seamless marquee) ---------- */
    var reduceMotion = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    if (!reduceMotion) {
        document.querySelectorAll(".tw--slider, .fx--slider .fx-list").forEach(function (track) {
            if (track.getAttribute("data-marquee")) return; // already initialised
            var items = Array.prototype.slice.call(track.children);
            if (items.length < 2) return;

            // Duplicate the items once so the loop is seamless (no visible jump).
            items.forEach(function (node) { track.appendChild(node.cloneNode(true)); });
            track.setAttribute("data-marquee", "1");

            // Use an internal accumulator: reading scrollLeft back can be rounded
            // to an integer by the browser, which would stall a < 1px/frame step.
            var paused = false;
            var pos = 0;
            var speed = 0.6; // px per frame
            function sync() { pos = track.scrollLeft; }

            track.addEventListener("mouseenter", function () { paused = true; });
            track.addEventListener("mouseleave", function () { sync(); paused = false; });
            track.addEventListener("touchstart", function () { paused = true; }, { passive: true });
            track.addEventListener("touchend", function () { sync(); paused = false; }, { passive: true });
            track.addEventListener("wheel", sync, { passive: true });

            function tick() {
                if (!paused) {
                    var half = track.scrollWidth / 2;
                    if (half > 0) {
                        pos += speed;
                        if (pos >= half) { pos -= half; }
                        track.scrollLeft = pos;
                    }
                }
                requestAnimationFrame(tick);
            }
            requestAnimationFrame(tick);
        });
    }
})();
