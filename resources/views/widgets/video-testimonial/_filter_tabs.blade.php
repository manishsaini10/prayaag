{{-- Shared Category Filter Tabs Bar --}}
@if($showTabs && isset($availableGroups) && $availableGroups->isNotEmpty())
<div class="vt-filter-wrap flex flex-wrap items-center justify-center gap-2 mb-10" x-data="{ activeTab: 'all' }">
    <button type="button"
            class="vt-tab-btn px-4 py-2 text-xs font-bold rounded-full transition-all duration-300"
            :class="activeTab === 'all' ? 'bg-[#0e2f5e] text-white shadow-md scale-105' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
            @click="activeTab = 'all'; vtFilterCards('all')">
        🌟 All Testimonials
    </button>

    @foreach($availableGroups as $groupName)
    <button type="button"
            class="vt-tab-btn px-4 py-2 text-xs font-bold rounded-full transition-all duration-300 capitalize"
            :class="activeTab === '{{ strtolower($groupName) }}' ? 'bg-[#0e2f5e] text-white shadow-md scale-105' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
            @click="activeTab = '{{ strtolower($groupName) }}'; vtFilterCards('{{ strtolower($groupName) }}')">
        {{ $groupName }}
    </button>
    @endforeach
</div>

<script>
if (typeof vtFilterCards === 'undefined') {
    function vtFilterCards(category) {
        var cards = document.querySelectorAll('.vt-card, .vt-reel-card, .vt-masonry-card, .vt-wall-card');
        cards.forEach(function(card) {
            var tags = card.getAttribute('data-tags') || '';
            if (category === 'all' || tags.toLowerCase().includes(category.toLowerCase())) {
                card.style.display = '';
                card.classList.add('vt-in-view');
            } else {
                card.style.display = 'none';
            }
        });
    }
}
</script>
@endif
