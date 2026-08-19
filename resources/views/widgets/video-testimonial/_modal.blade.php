{{-- Shared Lightbox Modal Partial --}}
<div id="vtModal" class="vt-modal" role="dialog" aria-modal="true" aria-label="Video testimonial player" style="display:none">
    <div class="vt-modal-backdrop" onclick="vtCloseModal()"></div>
    <div class="vt-modal-box">
        <button class="vt-modal-close" onclick="vtCloseModal()" aria-label="Close video">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="22" height="22">
                <path d="M18 6L6 18M6 6l12 12"/>
            </svg>
        </button>

        <div class="vt-modal-video-wrap">
            <iframe id="vtIframe" class="vt-iframe" frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
                loading="lazy"
                title="Video testimonial"
                src="about:blank"></iframe>
        </div>

        <div class="vt-modal-meta">
            <div>
                <p class="vt-modal-name" id="vtModalName"></p>
                <p class="vt-modal-grade" id="vtModalGrade"></p>
                <p class="vt-modal-title" id="vtModalTitle"></p>
            </div>
            <div id="vtModalCta" style="display:none">
                <a id="vtModalCtaLink" href="#" class="vt-cta-btn" target="_blank" rel="noopener"></a>
            </div>
        </div>
    </div>
</div>

<script>
function vtOpenModal(card) {
    var m = document.getElementById('vtModal');
    if (!m) return;
    var iframe = document.getElementById('vtIframe');
    var embedUrl = card.getAttribute('data-embed');
    if (embedUrl) {
        // Enforce autoplay with sound when opened in lightbox modal
        iframe.src = embedUrl.replace(/[?&]autoplay=\d/, '') + (embedUrl.includes('?') ? '&' : '?') + 'autoplay=1&mute=0';
    }
    document.getElementById('vtModalName').textContent  = card.getAttribute('data-student') || '';
    document.getElementById('vtModalGrade').textContent = card.getAttribute('data-grade') || '';
    document.getElementById('vtModalTitle').textContent = card.getAttribute('data-title') || '';
    var ctaLabel = card.getAttribute('data-cta-label');
    var ctaUrl   = card.getAttribute('data-cta-url');
    var ctaWrap  = document.getElementById('vtModalCta');
    var ctaLink  = document.getElementById('vtModalCtaLink');
    if (ctaLabel && ctaUrl) {
        ctaLink.textContent = ctaLabel;
        ctaLink.href = ctaUrl;
        ctaWrap.style.display = 'block';
    } else {
        ctaWrap.style.display = 'none';
    }
    m.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function vtCloseModal() {
    var m = document.getElementById('vtModal');
    if (!m) return;
    var iframe = document.getElementById('vtIframe');
    if (iframe) {
        iframe.src = 'about:blank'; // Destroy iframe video/audio stream 100% instantly
    }
    m.style.display = 'none';
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') vtCloseModal();
});
</script>
