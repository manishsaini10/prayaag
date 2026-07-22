@php
    use Illuminate\Support\Carbon;

    $todayDay  = strtolower(Carbon::now()->format('l'));
    $days      = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
    $dayLabels = ['monday'=>'Monday','tuesday'=>'Tuesday','wednesday'=>'Wednesday',
                  'thursday'=>'Thursday','friday'=>'Friday','saturday'=>'Saturday','sunday'=>'Sunday'];
    $shortDay  = ['monday'=>'Mon','tuesday'=>'Tue','wednesday'=>'Wed',
                  'thursday'=>'Thu','friday'=>'Fri','saturday'=>'Sat','sunday'=>'Sun'];

    function messMenuEmoji(string $n): string {
        $d = strtolower($n);
        if (str_contains($d,'paneer'))  return '🧀';
        if (str_contains($d,'dal'))     return '🫘';
        if (str_contains($d,'rice')||str_contains($d,'pulao')) return '🍚';
        if (str_contains($d,'roti')||str_contains($d,'chapati')||str_contains($d,'poori')||str_contains($d,'naan')) return '🫓';
        if (str_contains($d,'salad'))   return '🥗';
        if (str_contains($d,'custard')||str_contains($d,'halwa')||str_contains($d,'kheer')||str_contains($d,'seviya')) return '🍮';
        if (str_contains($d,'noodle')||str_contains($d,'chowmein')||str_contains($d,'manchurian')) return '🍜';
        if (str_contains($d,'aloo')||str_contains($d,'potato')) return '🥔';
        if (str_contains($d,'rajma'))   return '🫘';
        if (str_contains($d,'raita')||str_contains($d,'kadhi')) return '🥣';
        if (str_contains($d,'veg')||str_contains($d,'gobhi')||str_contains($d,'bhindi')) return '🥦';
        return '🍛';
    }
@endphp

<script type="application/ld+json">{"\u0040context":"https://schema.org","\u0040type":"Menu","name":"{{ $title ?? 'Weekly Mess Menu' }}","description":"Nutritious weekly vegetarian meal schedule at Prayaag International School","inLanguage":"en-IN"}</script>

<section id="mess-menu-widget" class="mm-section"
    x-data="{
        activeDay: 'all',
        search: '',
        toast: false,
        modal: false,
        day: '', date: '', dishes: [], note: '',
        rowVisible(dayKey, text) {
            if (this.activeDay !== 'all' && this.activeDay !== dayKey) return false;
            if (!this.search.trim()) return true;
            return (dayKey + ' ' + text).toLowerCase().includes(this.search.trim().toLowerCase());
        },
        openModal(d, dt, items, n) { this.day=d; this.date=dt; this.dishes=items; this.note=n; this.modal=true; },
        copyLink() { navigator.clipboard.writeText(location.href); this.toast=true; setTimeout(()=>this.toast=false,2500); }
    }">

    {{-- ── HEADER ── --}}
    <div class="mm-header">
        <div class="mm-header-inner">
            <div>
                <p class="mm-school-name">Prayaag International School</p>
                <h1 class="mm-title">{{ $title ?? 'Weekly Mess Menu' }}</h1>
                @if($menu)
                    <p class="mm-dates">
                        <span class="mm-date-range">{{ $menu->effective_from->format('d M Y') }}</span>
                        @if($menu->effective_to)
                            <span class="mm-date-sep">&nbsp;–&nbsp;</span>
                            <span class="mm-date-range">{{ $menu->effective_to->format('d M Y') }}</span>
                        @endif
                        <span class="mm-date-upd">&nbsp;·&nbsp; Updated {{ $menu->updated_at->diffForHumans() }}</span>
                    </p>
                @endif
            </div>

            @if($menu && !empty($grouped))
            <div class="mm-actions">
                <a href="{{ route('mess-menu.pdf') }}" target="_blank" class="mm-btn mm-btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h4a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                    </svg>
                    Download PDF
                </a>
                <button x-on:click="copyLink()" class="mm-btn mm-btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                    </svg>
                    Share
                </button>
            </div>
            @endif
        </div>
        <div class="mm-divider"></div>
    </div>

    @if(!$menu || empty($grouped))
        {{-- Empty State --}}
        <div class="mm-empty">
            <div class="mm-empty-icon">🍽️</div>
            <h3 class="mm-empty-title">Menu Not Available</h3>
            <p class="mm-empty-text">The weekly mess schedule is being prepared. Please check back soon.</p>
        </div>
    @else

        {{-- ── TOOLBAR ── --}}
        <div class="mm-toolbar">
            <div class="mm-tabs-wrap">
                <button x-on:click="activeDay='all'"
                        :class="activeDay==='all' ? 'mm-tab mm-tab-active' : 'mm-tab mm-tab-inactive'"
                        class="mm-tab">
                    All Days
                </button>
                @foreach($days as $dk)
                    <button x-on:click="activeDay='{{ $dk }}'"
                            :class="activeDay==='{{ $dk }}' ? 'mm-tab mm-tab-active' : 'mm-tab mm-tab-inactive'"
                            class="mm-tab">
                        {{ $shortDay[$dk] }}
                        @if($dk === $todayDay)
                            <span class="mm-tab-dot"></span>
                        @endif
                    </button>
                @endforeach
            </div>
            <div class="mm-search-wrap">
                <svg class="mm-search-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/>
                </svg>
                <input type="text" x-model="search" placeholder="Search dish..." class="mm-search-input">
            </div>
        </div>

        {{-- ── MENU TABLE ── --}}
        <div class="mm-table-wrap">
            <div class="mm-table-scroll">
                <table class="mm-table">
                    <thead>
                        <tr>
                            <th class="mm-th mm-th-day">Day</th>
                            <th class="mm-th mm-th-date">Date</th>
                            <th class="mm-th mm-th-menu">Lunch Menu</th>
                            <th class="mm-th mm-th-status">Status</th>
                            <th class="mm-th mm-th-view">View</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($days as $idx => $day)
                            @php
                                $isToday  = ($day === $todayDay);
                                $dateObj  = $menu->effective_from->copy()->addDays($idx);
                                $mealData = ($isToday && isset($specialOverrides['lunch']))
                                    ? $specialOverrides['lunch']
                                    : ($grouped[$day]['lunch'] ?? ['items' => [], 'notes' => '']);
                                $dishes   = $mealData['items'] ?? [];
                                $note     = $mealData['notes'] ?? '';
                                $hasLunch = !empty($dishes);
                                $rowText  = strtolower(implode(' ', $dishes) . ' ' . $note);
                            @endphp
                            <tr x-show="rowVisible('{{ $day }}', {{ json_encode($rowText) }})"
                                class="mm-row {{ $isToday ? 'mm-row-today' : '' }}">
                                <td class="mm-cell mm-cell-day">
                                    @if($isToday)
                                        <span class="mm-today-badge">Today</span>
                                    @endif
                                    {{ $dayLabels[$day] }}
                                </td>
                                <td class="mm-cell mm-cell-date">{{ $dateObj->format('d M Y') }}</td>
                                <td class="mm-cell mm-cell-dishes">
                                    @if($hasLunch)
                                        <div class="mm-dishes">
                                            @foreach($dishes as $dish)
                                                <span class="mm-dish {{ $isToday ? 'mm-dish-today' : '' }}">
                                                    <span>{{ messMenuEmoji($dish) }}</span>
                                                    <span>{{ $dish }}</span>
                                                </span>
                                            @endforeach
                                        </div>
                                        @if($note)
                                            <p class="mm-note">💡 {{ $note }}</p>
                                        @endif
                                    @else
                                        <span class="mm-na">Not scheduled</span>
                                    @endif
                                </td>
                                <td class="mm-cell mm-cell-status">
                                    @if($isToday)
                                        <span class="mm-status mm-status-serving">
                                            <span class="mm-status-pulse"></span>
                                            Serving Now
                                        </span>
                                    @elseif(!$hasLunch)
                                        <span class="mm-status mm-status-closed">Closed</span>
                                    @else
                                        <span class="mm-status mm-status-scheduled">Scheduled</span>
                                    @endif
                                </td>
                                <td class="mm-cell mm-cell-view">
                                    <button x-on:click="openModal({{ json_encode($dayLabels[$day]) }}, '{{ $dateObj->format('d M Y') }}', {{ json_encode($dishes) }}, {{ json_encode($note) }})"
                                            class="mm-view-btn" title="Quick View">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ── TODAY HIGHLIGHT BANNER ── --}}
        @php
            $todayBanner = ($menu && isset($specialOverrides['lunch']))
                ? $specialOverrides['lunch']
                : ($grouped[$todayDay]['lunch'] ?? ['items' => [], 'notes' => '']);
            $todayItems = $todayBanner['items'] ?? [];
        @endphp
        @if(!empty($todayItems))
            <div class="mm-banner">
                <div class="mm-banner-icon">🍽️</div>
                <div class="mm-banner-content">
                    <p class="mm-banner-label">Today's Lunch · {{ $dayLabels[$todayDay] }}, {{ now()->format('d M Y') }}</p>
                    <div class="mm-banner-dishes">
                        @foreach($todayItems as $ti)
                            <span class="mm-banner-dish">{{ messMenuEmoji($ti) }} {{ $ti }}</span>
                        @endforeach
                    </div>
                </div>
                <a href="{{ route('mess-menu.pdf') }}" target="_blank" class="mm-banner-btn">
                    Full Menu PDF
                </a>
            </div>
        @endif

        {{-- ── FOOTER ── --}}
        <div class="mm-footer">
            <span>ℹ️ Menu subject to change based on ingredient availability.</span>
            <span>Last updated: {{ $menu->updated_at->diffForHumans() }}</span>
        </div>

        {{-- ── DETAIL MODAL ── --}}
        <div x-show="modal"
             x-transition:enter="mm-modal-enter"
             x-transition:enter-start="mm-modal-enter-start"
             x-transition:enter-end="mm-modal-enter-end"
             x-transition:leave="mm-modal-leave"
             x-transition:leave-start="mm-modal-leave-start"
             x-transition:leave-end="mm-modal-leave-end"
             class="mm-modal-overlay"
             style="display: none;"
             x-on:click.self="modal=false"
             x-on:keydown.escape.window="modal=false"
             role="dialog" aria-modal="true">
            <div class="mm-modal-panel">
                <div class="mm-modal-head">
                    <div>
                        <p class="mm-modal-eyebrow">Lunch Menu</p>
                        <h3 class="mm-modal-day" x-text="day"></h3>
                        <p class="mm-modal-date" x-text="date"></p>
                    </div>
                    <button x-on:click="modal=false" class="mm-modal-close" aria-label="Close">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="mm-modal-body">
                    <template x-for="(dish, i) in dishes" :key="i">
                        <div class="mm-modal-dish">
                            <span class="mm-modal-num" x-text="i+1"></span>
                            <span class="mm-modal-name" x-text="dish"></span>
                        </div>
                    </template>
                    <div x-show="note" class="mm-modal-note">
                        💡 <span x-text="note"></span>
                    </div>
                    <div x-show="!dishes.length" class="mm-modal-empty">No dishes scheduled for this day.</div>
                </div>
                <div class="mm-modal-foot">
                    <button x-on:click="modal=false" class="mm-btn mm-btn-primary">Close</button>
                </div>
            </div>
        </div>

        {{-- ── COPY TOAST ── --}}
        <div x-show="toast"
             x-transition:enter="mm-toast-enter"
             x-transition:enter-start="mm-toast-enter-start"
             x-transition:enter-end="mm-toast-enter-end"
             x-transition:leave="mm-toast-leave"
             x-transition:leave-start="mm-toast-leave-start"
             x-transition:leave-end="mm-toast-leave-end"
             class="mm-toast"
             style="display: none;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M5 13l4 4L19 7"/>
            </svg>
            Link copied!
        </div>

    @endif
</section>

<style>
/* ============================================================================
   MESS MENU WIDGET — Prayaag International School
   Uses school design tokens (navy + gold) for brand consistency.
   ========================================================================== */

/* ── Section Container ── */
.mm-section {
    font-family: var(--font-body, 'Poppins', system-ui, sans-serif);
    padding: 2.5rem 1rem;
    max-width: 1200px;
    margin: 0 auto;
    color: var(--ink, #18202f);
}

@media (min-width: 640px) { .mm-section { padding: 2.5rem 1.5rem; } }
@media (min-width: 1024px) { .mm-section { padding: 2.5rem 2rem; } }

/* ── Header ── */
.mm-header { margin-bottom: 2rem; }
.mm-header-inner {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}
@media (min-width: 640px) {
    .mm-header-inner {
        flex-direction: row;
        align-items: flex-end;
        justify-content: space-between;
    }
}
.mm-school-name {
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--gold, #c79a3b);
    margin: 0 0 0.25rem;
}
.mm-title {
    font-family: var(--font-head, 'Playfair Display', serif);
    font-size: 1.875rem;
    font-weight: 700;
    color: var(--navy, #0b2545);
    margin: 0;
    line-height: 1.2;
    letter-spacing: -0.02em;
}
@media (min-width: 640px) { .mm-title { font-size: 2.25rem; } }
.mm-dates {
    margin: 0.5rem 0 0;
    font-size: 0.875rem;
    color: var(--muted, #6b7588);
}
.mm-date-range { font-weight: 600; color: var(--body, #3b4658); }
.mm-date-sep { color: var(--muted, #6b7588); }
.mm-date-upd { color: var(--muted, #6b7588); }
.mm-divider {
    margin-top: 1.5rem;
    height: 1px;
    background: linear-gradient(90deg, var(--gold, #c79a3b), var(--gold-soft, #f5ecd4), transparent);
}

/* ── Action Buttons ── */
.mm-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-shrink: 0;
}
@media print { .mm-actions { display: none !important; } }
.mm-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.6rem 1rem;
    border-radius: var(--radius-sm, 9px);
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
    text-decoration: none;
    line-height: 1;
    border: none;
}
.mm-btn svg { width: 16px; height: 16px; flex-shrink: 0; }
.mm-btn-primary {
    background: var(--navy, #0b2545);
    color: #fff;
    box-shadow: var(--shadow-sm, 0 1px 3px rgba(0,0,0,.07));
}
.mm-btn-primary:hover { background: var(--navy-3, #1c3a6e); }
.mm-btn-primary:active { transform: scale(0.97); }
.mm-btn-secondary {
    background: #fff;
    color: var(--body, #3b4658);
    border: 1px solid var(--line, #e6e9f0);
}
.mm-btn-secondary:hover { background: var(--bg-soft, #f6f8fc); }
.mm-btn-secondary:active { transform: scale(0.97); }

/* ── Empty State ── */
.mm-empty {
    text-align: center;
    padding: 6rem 2rem;
    background: #fff;
    border: 1px solid var(--line, #e6e9f0);
    border-radius: var(--radius-lg, 22px);
    box-shadow: var(--shadow-sm, 0 1px 3px rgba(0,0,0,.07));
}
.mm-empty-icon { font-size: 3rem; margin-bottom: 1rem; }
.mm-empty-title { font-size: 1.125rem; font-weight: 600; color: var(--body, #3b4658); margin: 0 0 0.25rem; }
.mm-empty-text { font-size: 0.875rem; color: var(--muted, #6b7588); margin: 0; }

/* ── Toolbar ── */
.mm-toolbar {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    margin-bottom: 1.25rem;
}
@media (min-width: 640px) {
    .mm-toolbar {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
    }
}
@media print { .mm-toolbar { display: none !important; } }

.mm-tabs-wrap {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    overflow-x: auto;
    padding-bottom: 0.25rem;
    -webkit-overflow-scrolling: touch;
}
.mm-tabs-wrap::-webkit-scrollbar { display: none; }
.mm-tab {
    flex-shrink: 0;
    padding: 0.4rem 0.75rem;
    border-radius: var(--radius-sm, 9px);
    font-size: 0.75rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    white-space: nowrap;
}
.mm-tab-active {
    background: var(--navy, #0b2545);
    color: #fff;
    box-shadow: var(--shadow-sm, 0 1px 3px rgba(0,0,0,.07));
}
.mm-tab-inactive {
    background: #fff;
    color: var(--muted, #6b7588);
    border: 1px solid var(--line, #e6e9f0);
}
.mm-tab-inactive:hover {
    border-color: var(--gold, #c79a3b);
    color: var(--navy, #0b2545);
}
.mm-tab-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--gold-2, #e0b94e);
    display: inline-block;
}

.mm-search-wrap {
    position: relative;
    width: 100%;
}
@media (min-width: 640px) { .mm-search-wrap { width: 13rem; } }
.mm-search-icon {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--muted, #6b7588);
    pointer-events: none;
}
.mm-search-input {
    width: 100%;
    padding: 0.5rem 0.75rem 0.5rem 2.25rem;
    border: 1px solid var(--line, #e6e9f0);
    border-radius: var(--radius-sm, 9px);
    background: #fff;
    color: var(--ink, #18202f);
    font-size: 0.8125rem;
    font-family: inherit;
    outline: none;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}
.mm-search-input::placeholder { color: var(--muted, #6b7588); opacity: 0.8; }
.mm-search-input:focus {
    border-color: var(--gold, #c79a3b);
    box-shadow: 0 0 0 3px rgba(199, 154, 59, 0.2);
}

/* ── Table ── */
.mm-table-wrap {
    overflow: hidden;
    border: 1px solid var(--line, #e6e9f0);
    border-radius: var(--radius, 14px);
    box-shadow: var(--shadow-sm, 0 1px 3px rgba(0,0,0,.07));
    background: #fff;
}
.mm-table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }
.mm-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
    min-width: 600px;
}
.mm-table thead tr {
    background: var(--navy, #0b2545);
    color: #fff;
}
.mm-th {
    padding: 0.875rem 1.25rem;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    text-align: left;
    white-space: nowrap;
}
.mm-th-day { width: 9rem; }
.mm-th-date { width: 7rem; }
.mm-th-status { width: 8rem; text-align: center; }
.mm-th-view { width: 5rem; text-align: center; }

/* ── Table Rows ── */
.mm-row { border-bottom: 1px solid var(--line-soft, #eef1f6); transition: background 0.1s ease; }
.mm-row:last-child { border-bottom: none; }
.mm-row:hover { background: var(--bg-soft, #f6f8fc); }
.mm-row-today { background: rgba(199, 154, 59, 0.05); }
.mm-row-today:hover { background: rgba(199, 154, 59, 0.1); }

.mm-cell {
    padding: 1rem 1.25rem;
    vertical-align: middle;
    line-height: 1.5;
}
.mm-cell-day {
    font-weight: 600;
    color: var(--ink, #18202f);
    white-space: nowrap;
}
.mm-cell-date {
    color: var(--muted, #6b7588);
    white-space: nowrap;
    font-size: 0.8125rem;
    font-weight: 500;
}

.mm-today-badge {
    display: inline-block;
    padding: 0.125rem 0.5rem;
    border-radius: 0.375rem;
    background: var(--navy, #0b2545);
    color: #fff;
    font-size: 0.625rem;
    font-weight: 700;
    text-transform: uppercase;
    margin-right: 0.375rem;
    letter-spacing: 0.03em;
}

/* ── Dishes ── */
.mm-dishes {
    display: flex;
    flex-wrap: wrap;
    gap: 0.375rem;
}
.mm-dish {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.625rem;
    border-radius: var(--radius-sm, 9px);
    font-size: 0.8125rem;
    font-weight: 500;
    background: var(--bg-soft, #f6f8fc);
    border: 1px solid var(--line, #e6e9f0);
    color: var(--body, #3b4658);
    transition: background 0.15s ease;
}
.mm-dish-today {
    background: var(--gold-soft, #f5ecd4);
    border-color: var(--gold, #c79a3b);
    color: var(--navy, #0b2545);
}
.mm-na {
    font-size: 0.8125rem;
    color: var(--muted, #6b7588);
    font-style: italic;
}
.mm-note {
    margin: 0.375rem 0 0;
    font-size: 0.75rem;
    color: var(--gold, #c79a3b);
    font-weight: 500;
}

/* ── Status Badges ── */
.mm-cell-status { text-align: center; }
.mm-status {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.25rem 0.75rem;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 600;
    white-space: nowrap;
}
.mm-status-serving {
    background: var(--navy, #0b2545);
    color: #fff;
}
.mm-status-pulse {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--gold-2, #e0b94e);
    animation: mm-pulse 1.5s ease-in-out infinite;
}
@keyframes mm-pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.2); }
}
.mm-status-scheduled {
    background: rgba(199, 154, 59, 0.1);
    color: var(--gold, #c79a3b);
    border: 1px solid var(--gold-soft, #f5ecd4);
}
.mm-status-closed {
    background: var(--bg-soft, #f6f8fc);
    color: var(--muted, #6b7588);
    border: 1px solid var(--line, #e6e9f0);
}

/* ── View Button ── */
.mm-cell-view { text-align: center; }
@media print { .mm-cell-view { display: none !important; } }
.mm-view-btn {
    padding: 0.5rem;
    border-radius: var(--radius-sm, 9px);
    color: var(--muted, #6b7588);
    background: none;
    border: none;
    cursor: pointer;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.mm-view-btn:hover { color: var(--gold, #c79a3b); background: var(--gold-soft, #f5ecd4); }
.mm-view-btn svg { width: 16px; height: 16px; }

/* ── Today Highlight Banner ── */
.mm-banner {
    margin-top: 1.25rem;
    padding: 1.25rem;
    border-radius: var(--radius, 14px);
    background: var(--navy, #0b2545);
    color: #fff;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
}
@media (min-width: 640px) {
    .mm-banner {
        flex-direction: row;
        align-items: center;
    }
}
@media print { .mm-banner { display: none !important; } }
.mm-banner-icon {
    flex-shrink: 0;
    width: 2.5rem;
    height: 2.5rem;
    border-radius: var(--radius-sm, 9px);
    background: rgba(255,255,255,0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}
.mm-banner-content { flex: 1; min-width: 0; }
.mm-banner-label {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--gold-2, #e0b94e);
    margin: 0 0 0.375rem;
}
.mm-banner-dishes {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}
.mm-banner-dish {
    padding: 0.25rem 0.75rem;
    border-radius: var(--radius-sm, 9px);
    background: rgba(255,255,255,0.15);
    color: #fff;
    font-size: 0.8125rem;
    font-weight: 500;
    border: 1px solid rgba(255,255,255,0.2);
}
.mm-banner-btn {
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.5rem 1rem;
    border-radius: var(--radius-sm, 9px);
    background: #fff;
    color: var(--navy, #0b2545);
    font-size: 0.75rem;
    font-weight: 700;
    text-decoration: none;
    transition: background 0.15s ease;
}
.mm-banner-btn:hover { background: var(--gold-soft, #f5ecd4); }

/* ── Footer ── */
.mm-footer {
    margin-top: 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    font-size: 0.75rem;
    color: var(--muted, #6b7588);
}
@media (min-width: 640px) {
    .mm-footer {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
    }
}

/* ── Modal ── */
.mm-modal-overlay {
    position: fixed;
    inset: 0;
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    background: rgba(11, 37, 69, 0.5);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}
@media print { .mm-modal-overlay { display: none !important; } }
.mm-modal-panel {
    background: #fff;
    border-radius: var(--radius, 14px);
    box-shadow: var(--shadow-lg, 0 24px 60px rgba(0,0,0,.16));
    width: 100%;
    max-width: 28rem;
    overflow: hidden;
    animation: mm-modal-in 0.2s ease;
}
@keyframes mm-modal-in {
    from { opacity: 0; transform: translateY(-10px) scale(0.97); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}
.mm-modal-head {
    padding: 1.25rem 1.5rem 1rem;
    border-bottom: 1px solid var(--line-soft, #eef1f6);
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
}
.mm-modal-eyebrow {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--gold, #c79a3b);
    margin: 0 0 0.125rem;
}
.mm-modal-day {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--ink, #18202f);
    margin: 0;
}
.mm-modal-date {
    font-size: 0.75rem;
    color: var(--muted, #6b7588);
    font-weight: 500;
    margin: 0.125rem 0 0;
}
.mm-modal-close {
    padding: 0.375rem;
    border-radius: var(--radius-sm, 9px);
    color: var(--muted, #6b7588);
    background: none;
    border: none;
    cursor: pointer;
    transition: all 0.15s ease;
    flex-shrink: 0;
}
.mm-modal-close:hover { color: var(--ink, #18202f); background: var(--bg-soft, #f6f8fc); }
.mm-modal-close svg { width: 16px; height: 16px; }
.mm-modal-body { padding: 1.25rem 1.5rem; }
.mm-modal-dish {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    border-radius: var(--radius-sm, 9px);
    background: var(--bg-soft, #f6f8fc);
    border: 1px solid var(--line-soft, #eef1f6);
    margin-bottom: 0.5rem;
}
.mm-modal-dish:last-child { margin-bottom: 0; }
.mm-modal-num {
    width: 1.75rem;
    height: 1.75rem;
    border-radius: var(--radius-sm, 9px);
    background: var(--gold-soft, #f5ecd4);
    color: var(--navy, #0b2545);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 700;
    flex-shrink: 0;
}
.mm-modal-name {
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--ink, #18202f);
}
.mm-modal-note {
    margin-top: 0.5rem;
    padding: 0.75rem;
    border-radius: var(--radius-sm, 9px);
    background: var(--gold-soft, #f5ecd4);
    border: 1px solid var(--gold, #c79a3b);
    font-size: 0.75rem;
    color: var(--navy, #0b2545);
    font-weight: 500;
}
.mm-modal-empty {
    text-align: center;
    padding: 1rem 0;
    font-size: 0.875rem;
    color: var(--muted, #6b7588);
}
.mm-modal-foot {
    padding: 1rem 1.5rem;
    background: var(--bg-soft, #f6f8fc);
    border-top: 1px solid var(--line-soft, #eef1f6);
    display: flex;
    justify-content: flex-end;
}

/* ── Toast ── */
.mm-toast {
    position: fixed;
    bottom: 1.5rem;
    right: 1.5rem;
    z-index: 1001;
    display: flex;
    align-items: center;
    gap: 0.625rem;
    padding: 0.75rem 1rem;
    border-radius: var(--radius-sm, 9px);
    background: var(--navy, #0b2545);
    color: #fff;
    font-size: 0.75rem;
    font-weight: 600;
    box-shadow: var(--shadow-lg, 0 24px 60px rgba(0,0,0,.16));
}
.mm-toast svg { color: var(--gold-2, #e0b94e); width: 16px; height: 16px; }
@media print { .mm-toast { display: none !important; } }

/* ── Transition helpers (Alpine.js) ── */
.mm-modal-enter, .mm-toast-enter { transition: all 0.2s ease-out; }
.mm-modal-enter-start { opacity: 0; }
.mm-modal-enter-end { opacity: 1; }
.mm-modal-leave, .mm-toast-leave { transition: all 0.15s ease-in; }
.mm-modal-leave-start { opacity: 1; }
.mm-modal-leave-end { opacity: 0; }
.mm-toast-enter-start { opacity: 0; transform: translateY(0.5rem); }
.mm-toast-enter-end { opacity: 1; transform: translateY(0); }
.mm-toast-leave-start { opacity: 1; }
.mm-toast-leave-end { opacity: 0; }

/* ── Print Styles ── */
@media print {
    .mm-section { padding: 0.5in; max-width: none; }
    .mm-toolbar, .mm-banner, .mm-view-btn, .mm-actions { display: none !important; }
    .mm-table-wrap { border: 1px solid #ccc; box-shadow: none; border-radius: 0; }
    .mm-table thead tr { background: #0b2545 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .mm-th { color: #fff !important; }
    .mm-row-today { background: #f5ecd4 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .mm-dish-today { background: #f5ecd4 !important; border-color: #c79a3b !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .mm-status { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .mm-status-serving { background: #0b2545 !important; color: #fff !important; }
    .mm-status-scheduled { background: rgba(199,154,59,0.1) !important; color: #c79a3b !important; }
    .mm-today-badge { background: #0b2545 !important; color: #fff !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .mm-footer { font-size: 0.625rem; }
}
</style>
