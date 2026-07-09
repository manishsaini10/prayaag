<!-- Animated Click Detail Modal -->
<div
    class="calendar-modal-backdrop"
    x-data="{
        isOpen: @entangle('isModalOpen'),
        activeTab: 0,
        closeModal() {
            this.isOpen = false;
            @this.closeModal();
        }
    }"
    x-show="isOpen"
    x-cloak
    @keydown.escape.window="closeModal()"
    @open-details-modal.window="activeTab = 0; isOpen = true;"
>
    <!-- Dark background overlay -->
    <div
        class="modal-overlay"
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="closeModal()"
    ></div>

    <!-- Modal Content Box -->
    <div
        class="modal-content-wrapper"
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
    >
        <div class="modal-box" style="border-radius:24px;padding:30px">
            <!-- Modal Header -->
            <div class="modal-header border-b pb-4 mb-5 flex items-start justify-between">
                <div>
                    <h3 class="text-xl font-extrabold text-[#0e2f5e] tracking-tight">
                        Schedule Details
                    </h3>
                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mt-1.5 flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="w-3.5 h-3.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        Date: <span class="text-gray-600 font-bold" x-text="selectedDate ? new Date(selectedDate).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) : ''"></span>
                    </p>
                </div>
                <button @click="closeModal()" class="modal-close-btn text-gray-400 hover:text-gray-600 transition p-1 rounded-lg hover:bg-gray-50">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="w-6 h-6"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                @if(count($dateEntries) > 0)
                    <!-- Multi-entry Tabs -->
                    @if(count($dateEntries) > 1)
                        <div class="modal-tabs flex gap-2 border-b pb-2.5 mb-5 overflow-x-auto">
                            @foreach($dateEntries as $index => $entry)
                                <button
                                    class="tab-btn py-2 px-3.5 text-xs font-bold rounded-xl border transition whitespace-nowrap"
                                    :class="activeTab === {{ $index }} ? 'bg-[#eff6ff] border-[#bfdbfe] text-blue-700 shadow-sm' : 'border-gray-150 text-gray-500 hover:bg-gray-50'"
                                    @click="activeTab = {{ $index }}"
                                >
                                    {{ $entry['title'] }}
                                </button>
                            @endforeach
                        </div>
                    @endif

                    <!-- Detail Cards -->
                    @foreach($dateEntries as $index => $entry)
                        <div x-show="activeTab === {{ $index }}" class="entry-detail-card" x-transition>
                            <div class="flex items-center gap-2 flex-wrap mb-4">
                                <span class="badge {{ $entry['category'] }}">
                                    {{ str_replace('_', ' ', $entry['category']) }}
                                </span>
                                @if($entry['sub_type'])
                                    <span class="badge sub-type-badge shadow-sm">
                                        {{ $entry['sub_type'] }}
                                    </span>
                                @endif
                                @if($entry['class'])
                                    <span class="badge class-badge shadow-sm">
                                        Class: {{ $entry['class']['class_name'] }}
                                    </span>
                                @endif
                                @if(!$entry['is_working_day'])
                                    <span class="badge non-working-badge bg-red-50 text-red-600 border border-red-150">
                                        Non-Working Day
                                    </span>
                                @endif
                            </div>

                            <h4 class="text-2xl font-extrabold text-[#0e2f5e] mb-3 leading-snug">{{ $entry['title'] }}</h4>
                            
                            <div class="meta-info-grid text-[13px] text-gray-500 mb-5 space-y-2 border-l-2 border-gray-150 pl-3">
                                <div>
                                    <span class="font-bold text-gray-600">Academic Session:</span> 
                                    <span class="font-medium text-gray-800">{{ $entry['session']['session_name'] ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="font-bold text-gray-600">Schedule Duration:</span> 
                                    <span class="font-medium text-gray-800">
                                        {{ \Carbon\Carbon::parse($entry['start_date'])->format('M d, Y') }} 
                                        @if($entry['end_date'])
                                            to {{ \Carbon\Carbon::parse($entry['end_date'])->format('M d, Y') }}
                                        @endif
                                    </span>
                                </div>
                            </div>

                            @if($entry['description'])
                                <div class="entry-desc-block bg-slate-50 rounded-2xl p-5 border border-slate-100 mb-5">
                                    <h5 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Description / Circular Memo</h5>
                                    <p class="text-gray-600 text-sm whitespace-pre-line leading-relaxed font-medium">{{ $entry['description'] }}</p>
                                </div>
                            @endif

                            @if($entry['attachment'])
                                <div class="entry-attachment-block mt-5 border-t pt-4">
                                    <h5 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2.5">Notice Circular Attachment</h5>
                                    <a
                                        href="{{ asset('storage/' . $entry['attachment']) }}"
                                        download
                                        target="_blank"
                                        class="inline-flex items-center gap-2.5 text-sm font-bold text-blue-600 hover:text-blue-800 transition bg-blue-50/50 hover:bg-blue-50 border border-blue-100 py-2.5 px-4 rounded-xl"
                                    >
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="w-4 h-4"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
                                        Download Official Memo (PDF/Image)
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <p class="text-center py-8 text-gray-400 font-semibold">No calendar events registered for this date.</p>
                @endif
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer border-t pt-4 mt-5 text-right">
                <button
                    @click="closeModal()"
                    class="btn-close py-2.5 px-5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-xl transition"
                >
                    Dismiss
                </button>
            </div>
        </div>
    </div>
</div>
