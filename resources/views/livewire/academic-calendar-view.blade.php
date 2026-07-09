<div class="academic-calendar-container" x-data="{
    viewMode: @entangle('viewMode'),
    sessionId: @entangle('sessionId'),
    initCalendar() {
        const el = document.getElementById('fullcalendar');
        if (!el) return;

        this.calendar = new FullCalendar.Calendar(el, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: ''
            },
            themeSystem: 'standard',
            events: {
                url: '{{ url('/academic-calendar/feed') }}',
                extraParams: () => {
                    return {
                        session_id: this.sessionId
                    };
                }
            },
            datesSet: (info) => {
                const date = info.view.activeStart;
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const currentMonth = `${year}-${month}`;
                @this.setYearMonth(currentMonth);
            },
            dateClick: (info) => {
                @this.showDateEntries(info.dateStr);
            },
            eventClick: (info) => {
                info.jsEvent.preventDefault();
                @this.showDateEntries(info.event.extendedProps.start_date_raw);
            },
            dayHeaderDidMount: (info) => {
                const day = info.date.getDay();
                let color;
                if (day === 0) color = '#dc2626';
                else if (day === 6) color = '#ea580c';
                else color = '#16a34a';
                info.el.style.color = color;
                info.el.style.fontWeight = '800';
                info.el.style.fontSize = '12px';
            },
            dayCellDidMount: (info) => {
                const date = info.date;
                const day = date.getDay();
                const dateNum = date.getDate();
                
                const isSunday = (day === 0);
                const isSecondSaturday = (day === 6 && dateNum >= 8 && dateNum <= 14);
                const isHoliday = isSunday || isSecondSaturday;
                
                const dowColors = ['#dc2626', '#16a34a', '#16a34a', '#16a34a', '#16a34a', '#16a34a', '#ea580c'];
                const numberEl = info.el.querySelector('.fc-daygrid-day-number');
                if (numberEl) {
                    numberEl.style.backgroundColor = dowColors[day];
                    numberEl.style.color = '#ffffff';
                    numberEl.style.borderRadius = '50%';
                    numberEl.style.width = '28px';
                    numberEl.style.height = '28px';
                    numberEl.style.display = 'inline-flex';
                    numberEl.style.alignItems = 'center';
                    numberEl.style.justifyContent = 'center';
                    numberEl.style.margin = '4px';
                    numberEl.style.fontWeight = '800';
                    numberEl.style.fontSize = '12px';
                }
                if (isHoliday) {
                    info.el.style.backgroundColor = 'rgba(245, 158, 11, 0.03)';
                }
            },
            eventDidMount: (info) => {
                info.el.style.borderRadius = '8px';
                info.el.style.padding = '3px 8px';
                info.el.style.fontSize = '12px';
                info.el.style.fontWeight = '600';
                info.el.style.borderWidth = '1px';
                info.el.style.boxShadow = '0 2px 4px rgba(0,0,0,0.03)';

                // Color code the entire day number circle and day box background
                const dayEl = info.el.closest('.fc-daygrid-day');
                if (dayEl) {
                    const category = info.event.extendedProps.category;
                    let color = '#3b82f6'; // default blue
                    let softBg = 'rgba(59, 130, 246, 0.05)';
                    
                    if (category === 'exam') {
                        color = '#ef4444';
                        softBg = 'rgba(239, 68, 68, 0.05)';
                    } else if (category === 'holiday') {
                        color = '#f59e0b';
                        softBg = 'rgba(245, 158, 11, 0.05)';
                    } else if (category === 'important_date') {
                        color = '#3b82f6';
                        softBg = 'rgba(59, 130, 246, 0.05)';
                    } else if (category === 'working_day_note') {
                        color = '#6b7280';
                        softBg = 'rgba(107, 114, 128, 0.05)';
                    }

                    // Apply soft tint to the full day cell background
                    dayEl.style.backgroundColor = softBg;

                    // Apply solid circular color badge to the day number
                    const numberEl = dayEl.querySelector('.fc-daygrid-day-number');
                    if (numberEl) {
                        numberEl.style.backgroundColor = color;
                        numberEl.style.color = '#ffffff';
                        numberEl.style.borderRadius = '50%';
                        numberEl.style.width = '28px';
                        numberEl.style.height = '28px';
                        numberEl.style.display = 'inline-flex';
                        numberEl.style.alignItems = 'center';
                        numberEl.style.justifyContent = 'center';
                        numberEl.style.margin = '4px';
                        numberEl.style.fontWeight = '800';
                        numberEl.style.fontSize = '12px';
                    }
                }
            }
        });
        
        this.calendar.render();
    },
    refetchEvents() {
        if (this.calendar) {
            this.calendar.refetchEvents();
        }
    },
    calendar: null
}" x-init="
    $nextTick(() => {
        if (viewMode === 'month') {
            initCalendar();
        }
    });

    $watch('viewMode', value => {
        if (value === 'month') {
            $nextTick(() => {
                if (!calendar) initCalendar();
                else calendar.render();
            });
        }
    });

    $watch('sessionId', () => { $nextTick(() => { refetchEvents(); }); });

    window.addEventListener('sessionChanged', () => { $nextTick(() => { refetchEvents(); }); });
">
    <div class="calendar-layout-grid">
        <!-- Sidebar Controls -->
        <aside class="calendar-sidebar">
            
            <!-- Today's Event Card (Highlighted, Big badge!) -->
            <div class="premium-card today-highlight-card" style="position:relative; overflow:hidden; border-left: 6px solid #16a34a; background: linear-gradient(180deg, rgba(240, 253, 244, 0.7) 0%, rgba(255, 255, 255, 0.85) 100%);">
                @if(count($todayEntries) > 0)
                    @php $firstToday = $todayEntries->first(); @endphp
                    <div style="position:absolute; top:0; right:0; width:150px; height:150px; background:radial-gradient(circle at 100% 0%, rgba(var(--primary-rgb, 59, 130, 246), 0.08), transparent 70%); pointer-events:none"></div>
                    
                    <h3 class="card-title" style="border-bottom:none; padding-bottom:0; margin-bottom:6px; color:#15803d; font-size:12.5px; font-weight:800; letter-spacing:1px; text-transform:uppercase">
                        ✨ Today's Schedule
                    </h3>

                    <div style="font-size:13px; font-weight:600; color:#64748b; margin-bottom:10px; display:flex; align-items:center; gap:8px">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <span>{{ now()->format('l, F d, Y') }}</span>
                        <span style="color:#94a3b8">|</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span id="current-time">{{ now()->format('h:i A') }}</span>
                    </div>

                    <div style="margin-bottom:14px">
                        <span class="badge {{ $firstToday->category }}" style="font-size:12.5px; font-weight:800; padding:6px 14px; border-radius:99px; box-shadow:var(--shadow-sm);
                            @if($firstToday->category === 'exam') background:#fef3c7;color:#92400e;border-color:#fcd34d;
                            @elseif($firstToday->category === 'holiday') background:#fee2e2;color:#991b1b;border-color:#fca5a5;
                            @elseif($firstToday->category === 'important_date') background:#dbeafe;color:#1e40af;border-color:#93c5fd;
                            @else background:#f3f4f6;color:#374151;border-color:#e5e7eb; @endif
                        ">
                            {{ str_replace('_', ' ', $firstToday->category) }}
                        </span>
                    </div>

                    <h4 class="today-title" style="font-size:18px; font-weight:850; color:#0e2f5e; line-height:1.3; margin-bottom:8px; cursor:pointer" wire:click="showDateEntries('{{ now()->toDateString() }}')">
                        {{ $firstToday->title }}
                    </h4>
                    
                    @if($firstToday->sub_type)
                        <div style="font-size:12px; font-weight:600; color:#475569; margin-bottom:6px">
                            🏷️ {{ $firstToday->sub_type }}
                        </div>
                    @endif

                    @if($firstToday->class)
                        <div style="font-size:12px; font-weight:600; color:#7c3aed; margin-bottom:6px">
                            🎓 Class: {{ $firstToday->class->class_name }}
                        </div>
                    @endif

                    <p style="font-size:12.5px; color:#64748b; line-height:1.5; margin:8px 0 0 0; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden">
                        {{ $firstToday->description ?: 'No detailed circular notice configured for today\'s calendar activity.' }}
                    </p>
                @else
                    <!-- Default Standard Working Day -->
                    <h3 class="card-title" style="border-bottom:none; padding-bottom:0; margin-bottom:6px; color:#15803d; font-size:12.5px; font-weight:800; letter-spacing:1px; text-transform:uppercase">
                        ✨ Today's Schedule
                    </h3>

                    <div style="font-size:13px; font-weight:600; color:#64748b; margin-bottom:10px; display:flex; align-items:center; gap:8px">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <span>{{ now()->format('l, F d, Y') }}</span>
                        <span style="color:#94a3b8">|</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span id="current-time">{{ now()->format('h:i A') }}</span>
                    </div>
                    
                    <div style="margin-bottom:14px">
                        <span class="badge working-day" style="font-size:12.5px; font-weight:800; padding:6px 14px; border-radius:99px; background:#dcfce7; color:#16a34a; border:1px solid #bbf7d0; box-shadow:var(--shadow-sm)">
                            Academic Day
                        </span>
                    </div>

                    <h4 style="font-size:18px; font-weight:850; color:#0e2f5e; line-height:1.3; margin-bottom:6px">
                        Standard Working Day
                    </h4>
                    <p style="font-size:12.5px; color:#64748b; line-height:1.5; margin:0">
                        Regular classes are functioning as scheduled. Standard school uniform and timeline apply. Enjoy your classes!
                    </p>
                @endif
            </div>

            <!-- Upcoming Events (3 items, similar badges) -->
            <div class="premium-card upcoming-card">
                <h3 class="card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:10px;height:10px;color:var(--primary)"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Upcoming Calendar
                </h3>
                
                <div style="display:flex; flex-direction:column; gap:16px">
                    @forelse($upcomingEntries as $entry)
                        <div class="upcoming-item-styled" style="border-left: 4.5px solid; padding-left:14px; transition:all 0.2s ease; cursor:pointer;
                            @if($entry->category === 'exam') border-left-color:var(--exam-color);
                            @elseif($entry->category === 'holiday') border-left-color:var(--holiday-color);
                            @elseif($entry->category === 'important_date') border-left-color:var(--important-color);
                            @else border-left-color:var(--working-color); @endif"
                            wire:click="showDateEntries('{{ $entry->start_date->toDateString() }}')"
                        >
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px">
                                <span class="badge {{ $entry->category }}" style="font-size:9.5px; font-weight:850; padding:1.5px 6px; border-radius:4px; text-transform:uppercase;
                                    @if($entry->category === 'exam') background:#fee2e2;color:#991b1b;
                                    @elseif($entry->category === 'holiday') background:#fef3c7;color:#92400e;
                                    @elseif($entry->category === 'important_date') background:#dbeafe;color:#1e40af;
                                    @else background:#f3f4f6;color:#374151; @endif
                                ">
                                    {{ str_replace('_', ' ', $entry->category) }}
                                </span>
                                <span style="font-size:11.5px; font-weight:700; color:#64748b">
                                    {{ $entry->start_date->format('M d') }}
                                </span>
                            </div>
                            <h4 style="font-size:13.5px; font-weight:700; color:#1e293b; margin:0; line-height:1.3; overflow:hidden; text-overflow:ellipsis; white-space:nowrap" title="{{ $entry->title }}">
                                {{ $entry->title }}
                            </h4>
                        </div>
                    @empty
                        <p style="font-size:12.5px; color:#94a3b8; text-align:center; margin:10px 0 0 0; font-weight:550">No upcoming events scheduled.</p>
                    @endforelse
                </div>
            </div>
            
            <!-- Filter Card -->
            <div class="premium-card filter-card">
                <h3 class="card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:10px;height:10px;color:var(--primary)"><path d="M22 3H2l8 9v6l4 2v-8L22 3z"/></svg>
                    Filter Calendar
                </h3>
                
                <div class="form-group">
                    <label for="session_select">Academic Session</label>
                    <div class="select-wrapper">
                        <select id="session_select" wire:model.live="sessionId">
                            <option value="">— Select Session —</option>
                            @foreach($sessions as $s)
                                <option value="{{ $s->id }}">{{ $s->session_name }} @if($s->is_current)(Active)@endif</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="actions-group" style="margin-top:20px">
                    <a href="{{ url('/academic-calendar/export-pdf?session_id=' . $sessionId) }}" target="_blank" class="btn-pdf-download">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:8px;height:8px"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                        Download PDF Calendar
                    </a>
                </div>
            </div>

            <!-- Working Days Counter Card -->
            <div class="premium-card stats-card">
                <h3 class="card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:10px;height:10px;color:var(--primary)"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Working Days Counter
                </h3>
                <p class="stats-subtitle text-[12px] text-gray-400 font-semibold mb-4 uppercase tracking-wider">
                    For {{ \Carbon\Carbon::parse($currentYearMonth . '-01')->format('F Y') }}
                </p>
                <div class="stats-grid">
                    <div class="stat-item total">
                        <span class="stat-number">{{ $workingDays['total'] }}</span>
                        <span class="stat-label">Total Days</span>
                    </div>
                    <div class="stat-item working">
                        <span class="stat-number">{{ $workingDays['working'] }}</span>
                        <span class="stat-label">Working</span>
                    </div>
                    <div class="stat-item non-working">
                        <span class="stat-number">{{ $workingDays['non_working'] }}</span>
                        <span class="stat-label">Holidays</span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Workspace -->
        <main class="calendar-workspace">
            <!-- View Mode Switcher -->
            <div class="view-mode-tabs shadow-sm">
                <button wire:click="setViewMode('month')" class="tab-button {{ $viewMode === 'month' ? 'active' : '' }}">
                    Month view
                </button>
                <button wire:click="setViewMode('year')" class="tab-button {{ $viewMode === 'year' ? 'active' : '' }}">
                    Full year view
                </button>
            </div>

            <!-- Month View Section -->
            <div class="workspace-section {{ $viewMode === 'month' ? 'active' : 'hidden' }}" x-transition>
                <div class="card" style="padding: 28px; border-radius: var(--radius-lg); border: none;">
                    <div id="fullcalendar" wire:ignore></div>
                </div>
            </div>

            <!-- Full Year View Section -->
            <div class="workspace-section {{ $viewMode === 'year' ? 'active' : 'hidden' }}" x-transition>
                <div class="card" style="padding: 30px; border-radius: var(--radius-lg); border: none;">
                    <div class="year-header border-b pb-5 mb-6">
                        <h2 class="text-2xl font-bold text-[#0e2f5e] tracking-tight">
                            @if($activeSession)
                                {{ $activeSession->session_name }} — Annual Overview
                            @else
                                Year View
                            @endif
                        </h2>
                        <p class="text-gray-500 mt-1 font-medium text-sm">Full 12-month visual grid mapping the session schedule.</p>
                    </div>

                    @if(!$activeSession)
                        <div class="text-center py-12 text-gray-400 font-semibold">No active academic session selected.</div>
                    @else
                        @php
                            $startMonth = \Carbon\Carbon::parse($activeSession->start_date)->startOfMonth();
                            $endMonth = \Carbon\Carbon::parse($activeSession->end_date)->startOfMonth();
                            $months = [];
                            
                            $curr = $startMonth->copy();
                            while($curr->lte($endMonth)) {
                                $months[] = $curr->copy();
                                $curr->addMonth();
                            }

                            $allSessionEntries = \App\Models\AcademicCalendarEntry::where('session_id', $activeSession->id)
                                ->where('status', 'published')
                                ->get();
                        @endphp

                        <div class="year-calendar-grid">
                            @foreach($months as $m)
                                @php
                                    $firstDay = $m->copy()->startOfMonth();
                                    $daysInMonth = $m->daysInMonth;
                                    $startDayOfWeek = $firstDay->dayOfWeek; // 0 (Sun) to 6 (Sat)
                                @endphp
                                <div class="mini-month-card">
                                    <h4 class="mini-month-title font-bold text-[#0e2f5e]">{{ $m->format('F Y') }}</h4>
                                    <div class="mini-days-header-grid text-center text-[11px] font-bold mb-2">
                                        <span style="color:#dc2626">S</span>
                                        <span style="color:#16a34a">M</span>
                                        <span style="color:#16a34a">T</span>
                                        <span style="color:#16a34a">W</span>
                                        <span style="color:#16a34a">T</span>
                                        <span style="color:#16a34a">F</span>
                                        <span style="color:#ea580c">S</span>
                                    </div>
                                    <div class="mini-days-grid text-center text-xs">
                                        @for($i = 0; $i < $startDayOfWeek; $i++)
                                            <span class="mini-day-blank"></span>
                                        @endfor

                                        @for($day = 1; $day <= $daysInMonth; $day++)
                                            @php
                                                $dateString = $m->copy()->day($day)->toDateString();
                                                $dayDate = $m->copy()->day($day);
                                                
                                                $dateEntries = $allSessionEntries->filter(function($entry) use ($dateString) {
                                                    return $entry->start_date->toDateString() === $dateString 
                                                        || ($entry->end_date && $dateString >= $entry->start_date->toDateString() && $dateString <= $entry->end_date->toDateString());
                                                });
                                                
                                                $isSunday = $dayDate->isSunday();
                                                $isSaturday = $dayDate->isSaturday();
                                                $isSecondSaturday = $isSaturday && ($dayDate->day >= 8 && $dayDate->day <= 14);
                                                $isHoliday = $isSunday || $isSecondSaturday;
                                                $hasEntry = $dateEntries->isNotEmpty();
                                                $isToday = $dateString === now()->toDateString();
                                                
                                                $classesStr = 'mini-day';
                                                if($isSunday) $classesStr .= ' day-sunday';
                                                if($isSaturday) $classesStr .= ' day-saturday';
                                                if(!$isSunday && !$isSaturday) $classesStr .= ' day-weekday';
                                                if($isHoliday) $classesStr .= ' holiday';
                                                if($hasEntry) $classesStr .= ' has-entries shadow-sm';
                                                if($isToday) $classesStr .= ' is-today';
                                                
                                                $cats = $dateEntries->pluck('category')->unique();
                                            @endphp
                                            <span class="{{ $classesStr }}" wire:click="showDateEntries('{{ $dateString }}')">
                                                {{ $day }}
                                                @if($hasEntry)
                                                    <span class="mini-dots-container">
                                                        @foreach($cats as $c)
                                                            <span class="mini-dot dot-{{ $c }}"></span>
                                                        @endforeach
                                                    </span>
                                                @endif
                                            </span>
                                        @endfor
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Mini Dot Legend -->
                        <div class="mini-dots-legend flex items-center justify-center gap-6 mt-8 border-t pt-5 flex-wrap">
                            <span class="flex items-center gap-2 text-xs font-semibold text-gray-600"><span class="mini-dot dot-exam" style="position:static;width:7px;height:7px"></span> Exam</span>
                            <span class="flex items-center gap-2 text-xs font-semibold text-gray-600"><span class="mini-dot dot-holiday" style="position:static;width:7px;height:7px"></span> Holiday/Vacation</span>
                            <span class="flex items-center gap-2 text-xs font-semibold text-gray-600"><span class="mini-dot dot-important_date" style="position:static;width:7px;height:7px"></span> Important Date</span>
                            <span class="flex items-center gap-2 text-xs font-semibold text-gray-600"><span class="mini-dot dot-working_day_note" style="position:static;width:7px;height:7px"></span> Working Note</span>
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>

    <!-- Click Interaction Detail Modal -->
    @include('academic-calendar.partials.modal')

    {{-- Live time updater --}}
    <script>
    (function() {
        var el = document.getElementById('current-time');
        if (!el) return;
        function pad(n) { return String(n).padStart(2, '0'); }
        function update() {
            var d = new Date();
            var h = d.getHours();
            var ampm = h >= 12 ? 'PM' : 'AM';
            h = h % 12 || 12;
            el.textContent = pad(h) + ':' + pad(d.getMinutes()) + ' ' + ampm;
        }
        setInterval(update, 10000);
    })();
    </script>
</div>
