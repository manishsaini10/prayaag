@extends('admin.layout')

@section('title', 'Parent Testimonials Moderation')

@section('actions')
    <a href="{{ route('admin.testimonials-console.settings') }}" class="btn-secondary inline-flex items-center gap-1.5">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.1a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
        <span>Testimonial Settings</span>
    </a>
@endsection

@section('content')
<div class="space-y-6" x-data="testimonialConsole()">
    @if(session('success'))
        <div class="px-4 py-3 rounded-xl text-sm" style="background:#dcfce7;color:#166534">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="px-4 py-3 rounded-xl text-sm" style="background:#fee2e2;color:#991b1b">{{ session('error') }}</div>
    @endif

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div class="stat-label">Total Submissions</div>
        </div>
        <div class="stat-card">
            <div class="stat-value text-amber-500">{{ $stats['pending'] }}</div>
            <div class="stat-label">Pending Approval</div>
        </div>
        <div class="stat-card">
            <div class="stat-value text-emerald-600">{{ $stats['approved'] }}</div>
            <div class="stat-label">Approved & Published</div>
        </div>
        <div class="stat-card">
            <div class="stat-value text-rose-600">{{ $stats['rejected'] }}</div>
            <div class="stat-label">Rejected</div>
        </div>
        <div class="stat-card">
            <div class="stat-value text-purple-600">{{ $stats['featured'] }}</div>
            <div class="stat-label">Featured ⭐</div>
        </div>
    </div>

    {{-- Controls Bar --}}
    <div class="flex flex-col md:flex-row gap-4 items-center justify-between card p-4">
        <div class="flex items-center gap-3 flex-wrap">
            {{-- Status Tabs --}}
            <div class="flex flex-wrap gap-1 bg-gray-100 p-1 rounded-lg">
                @foreach([
                    'all' => ['All', $stats['total']],
                    'pending' => ['Pending', $stats['pending']],
                    'approved' => ['Approved', $stats['approved']],
                    'rejected' => ['Rejected', $stats['rejected']],
                    'archived' => ['Archived', $stats['archived']]
                ] as $key => $tabData)
                    @php
                        [$lbl, $count] = $tabData;
                        $isActive = ($status === $key);
                        $isPendingAlert = ($key === 'pending' && $stats['pending'] > 0);
                    @endphp
                    <a href="{{ route('admin.testimonials-console.index', ['status' => $key, 'q' => $search]) }}" 
                       class="relative px-3 py-1.5 text-xs font-semibold rounded-md transition-all flex items-center gap-1.5 
                       {{ $isActive ? 'bg-white shadow-sm text-gray-800' : 'text-gray-500 hover:text-gray-800' }}
                       {{ $isPendingAlert && !$isActive ? 'border border-amber-300 bg-amber-50/50 text-amber-800 hover:bg-amber-100/70' : '' }}">
                        {{ $lbl }}
                        <span class="px-1.5 py-0.5 text-[10px] font-bold rounded-full 
                            {{ $isActive ? 'bg-gray-100 text-gray-700' : ($isPendingAlert ? 'bg-amber-500 text-white animate-pulse' : 'bg-gray-200/70 text-gray-500') }}">
                            {{ $count }}
                        </span>
                        @if($isPendingAlert)
                            <span class="absolute -top-0.5 -right-0.5 flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                            </span>
                        @endif
                    </a>
                @endforeach
            </div>

            {{-- Import / Export --}}
            <div class="flex gap-1">
                <a href="{{ route('admin.testimonials-console.export') }}" class="btn-secondary py-1.5 px-3 text-xs font-semibold inline-flex items-center gap-1">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                    Export CSV
                </a>
                <button type="button" @click="importModal = true" class="btn-secondary py-1.5 px-3 text-xs font-semibold inline-flex items-center gap-1">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/></svg>
                    Import CSV
                </button>
            </div>
        </div>

        {{-- Search Bar --}}
        <form action="{{ route('admin.testimonials-console.index') }}" method="GET" class="flex gap-2 w-full md:w-auto">
            <input type="hidden" name="status" value="{{ $status }}">
            <input type="text" name="q" value="{{ $search }}" placeholder="Search parent, student, text..." class="text-sm px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary w-full md:w-64">
            <button type="submit" class="btn-primary py-2 px-4 text-xs font-semibold">Search</button>
        </form>
    </div>

    {{-- Bulk Action Form --}}
    <form id="bulk-form" action="{{ route('admin.testimonials-console.bulk') }}" method="POST">
        @csrf
        
        <div class="flex items-center gap-2 mb-3 bg-gray-50 p-3 rounded-lg border">
            <span class="text-xs font-semibold text-gray-600">Bulk Actions:</span>
            <select name="action" class="text-xs px-2 py-1.5 border rounded focus:outline-none focus:ring-1 focus:ring-primary" required>
                <option value="">-- Choose Action --</option>
                <option value="approve">Approve & Publish</option>
                <option value="reject">Reject</option>
                <option value="feature">Mark Featured</option>
                <option value="unfeature">Remove Featured</option>
                <option value="delete">Delete Permanently</option>
            </select>
            <button type="submit" class="btn-secondary py-1.5 px-3 text-xs font-bold" onclick="return confirm('Apply this bulk action to selected testimonials?')">Apply</button>
        </div>
    </form>

        {{-- Data Table --}}
        <div class="card p-0 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase">
                            <th class="p-4 w-10">
                                <input type="checkbox" @change="toggleAll($event)">
                            </th>
                            <th class="p-4">Photo</th>
                            <th class="p-4">Parent Details</th>
                            <th class="p-4">Student & Class</th>
                            <th class="p-4">Rating</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Display Areas</th>
                            <th class="p-4">Date</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($testimonials as $t)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="p-4">
                                    <input type="checkbox" name="ids[]" value="{{ $t->id }}" class="bulk-checkbox" form="bulk-form">
                                </td>
                                <td class="p-4">
                                    @if($t->image)
                                        <img src="{{ asset($t->image) }}" class="w-10 h-10 rounded-full object-cover border">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-400 border uppercase">
                                            {{ substr($t->name ?? 'P', 0, 2) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <div class="font-bold text-gray-800">{{ $t->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $t->relation }} · {{ $t->phone }}</div>
                                </td>
                                <td class="p-4">
                                    <div class="text-gray-700 font-semibold">{{ $t->student_name ?: 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">Class: {{ $t->class ?: 'N/A' }}</div>
                                </td>
                                <td class="p-4">
                                    <div class="text-amber-500 font-bold">
                                        {!! str_repeat('★', $t->rating) !!}{!! str_repeat('☆', 5 - $t->rating) !!}
                                    </div>
                                </td>
                                <td class="p-4">
                                    @if($t->status === 'approved')
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">Approved</span>
                                    @elseif($t->status === 'pending')
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-100">Pending</span>
                                    @elseif($t->status === 'rejected')
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-100">Rejected</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">Archived</span>
                                    @endif

                                    @if($t->featured)
                                        <span class="ml-1 px-2 py-0.5 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-100">⭐ Featured</span>
                                    @endif
                                    @if($t->is_verified)
                                        <span class="ml-1 px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">✓ Verified</span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    @if($t->display_location && is_array($t->display_location))
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($t->display_location as $loc)
                                                <span class="bg-gray-100 text-gray-700 text-xs px-2 py-0.5 rounded uppercase">{{ $loc }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400">None</span>
                                    @endif
                                </td>
                                <td class="p-4 text-xs text-gray-500">
                                    {{ $t->created_at->format('M j, Y') }}
                                <td class="p-4 text-right">
                                    <div class="flex justify-end gap-1.5 items-center">
                                        {{-- View Details Trigger --}}
                                        <button type="button" @click="showDetail('{{ $t->id }}')" class="p-1 hover:bg-gray-100 rounded text-gray-600" title="View details">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </button>

                                        @if($t->status !== 'approved')
                                            <form action="{{ route('admin.testimonials-console.approve', $t->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="p-1 text-emerald-600 hover:bg-emerald-50 rounded" title="Approve">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:16px;height:16px"><path d="M20 6 9 17l-5-5"/></svg>
                                                </button>
                                            </form>
                                        @endif

                                        @if($t->status !== 'rejected')
                                            <form action="{{ route('admin.testimonials-console.reject', $t->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="p-1 text-rose-600 hover:bg-rose-50 rounded" title="Reject">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:16px;height:16px"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                                </button>
                                            </form>
                                        @endif

                                        <form action="{{ route('admin.testimonials-console.toggle-verified', $t->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-1 {{ $t->is_verified ? 'text-emerald-600' : 'text-gray-400' }} hover:bg-emerald-50 rounded" title="Toggle Verified">
                                                <svg viewBox="0 0 24 24" fill="{{ $t->is_verified ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2.5" style="width:16px;height:16px"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4 12 14.01l-3-3"/></svg>
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.testimonials-console.toggle-featured', $t->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-1 {{ $t->featured ? 'text-purple-600' : 'text-gray-400' }} hover:bg-purple-50 rounded" title="Toggle Featured">
                                                <svg viewBox="0 0 24 24" fill="{{ $t->featured ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                            </button>
                                        </form>

                                        <a href="{{ route('admin.testimonials-console.edit', $t->id) }}" class="p-1 hover:bg-gray-100 rounded text-gray-600" title="Edit">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </a>

                                        <form action="{{ route('admin.testimonials-console.duplicate', $t->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-1 text-gray-500 hover:bg-gray-100 rounded" title="Duplicate">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.testimonials-console.destroy', $t->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 text-rose-600 hover:bg-rose-50 rounded" title="Delete Permanently" onclick="return confirm('Permanently delete this testimonial?')">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><path d="M3 6h18m-2 0v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6m3 0V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="p-8 text-center text-gray-400">
                                    No testimonials found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $testimonials->appends(request()->query())->links() }}
        </div>

    {{-- Import CSV Modal --}}
    <div x-show="importModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" x-cloak>
        <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-xl border" @click.away="importModal = false">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="font-bold text-lg text-gray-800">Import Testimonials</h3>
                <button type="button" @click="importModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px;height:20px"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('admin.testimonials-console.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold mb-1 text-gray-700">CSV File</label>
                    <input type="file" name="csv_file" accept=".csv,.txt" class="w-full text-sm p-2 border rounded" required>
                    <p class="text-xs text-gray-400 mt-1">CSV header columns: name, phone, email, student_name, class, relation, testimonial, rating, status, featured, verified, display_locations</p>
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="importModal = false" class="btn-secondary text-xs px-4 py-2 font-semibold">Cancel</button>
                    <button type="submit" class="btn-primary text-xs px-4 py-2 font-bold">Upload & Import</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Testimonial Detail Modal --}}
    <div x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" x-cloak>
        <div class="bg-white rounded-2xl max-w-xl w-full p-6 space-y-4 shadow-xl border overflow-hidden" @click.away="modalOpen = false">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="font-bold text-lg text-gray-800">Testimonial Details</h3>
                <button type="button" @click="modalOpen = false" class="text-gray-400 hover:text-gray-600">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px;height:20px"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2">
                {{-- Quote/Testimonial --}}
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <div class="text-amber-500 font-bold mb-2" x-text="'★'.repeat(detail.rating ?? 5) + '☆'.repeat(5 - (detail.rating ?? 5))"></div>
                    <div class="font-bold text-gray-800 mb-1" x-text="detail.title || 'Experience Sharing'"></div>
                    <p class="text-gray-700 italic text-sm whitespace-pre-line" x-text="detail.testimonial"></p>
                </div>

                {{-- Submitter Metadata --}}
                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-gray-400 block font-semibold">Parent Name</span>
                        <span class="text-gray-800 font-bold" x-text="detail.name"></span>
                    </div>
                    <div>
                        <span class="text-gray-400 block font-semibold">Relationship</span>
                        <span class="text-gray-800 font-bold" x-text="detail.relation || 'N/A'"></span>
                    </div>
                    <div>
                        <span class="text-gray-400 block font-semibold">Student Name</span>
                        <span class="text-gray-800 font-bold" x-text="detail.student_name || 'N/A'"></span>
                    </div>
                    <div>
                        <span class="text-gray-400 block font-semibold">Class</span>
                        <span class="text-gray-800 font-bold" x-text="detail.class || 'N/A'"></span>
                    </div>
                    <div>
                        <span class="text-gray-400 block font-semibold">Phone</span>
                        <span class="text-gray-800 font-bold" x-text="detail.phone || 'N/A'"></span>
                    </div>
                    <div>
                        <span class="text-gray-400 block font-semibold">Email</span>
                        <span class="text-gray-800 font-bold" x-text="detail.email || 'N/A'"></span>
                    </div>
                </div>

                <hr>

                {{-- Session/Network Metadata --}}
                <div class="grid grid-cols-1 gap-2 text-[10px] text-gray-400 bg-gray-50 p-3 rounded border">
                    <div><b>IP Address:</b> <span x-text="detail.ip_address || 'N/A'"></span></div>
                    <div><b>Browser:</b> <span x-text="detail.browser || 'N/A'"></span></div>
                    <div><b>Approved At:</b> <span x-text="detail.approved_at ? new Date(detail.approved_at).toLocaleString() : 'Not Approved'"></span></div>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t">
                <button type="button" @click="modalOpen = false" class="btn-secondary text-xs px-4 py-2 font-semibold">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function testimonialConsole() {
    return {
        modalOpen: false,
        importModal: false,
        detail: {},
        toggleAll(e) {
            const boxes = document.querySelectorAll('.bulk-checkbox');
            boxes.forEach(box => box.checked = e.target.checked);
        },
        showDetail(id) {
            fetch(`/admin/testimonials-console/view/${id}`)
                .then(res => res.json())
                .then(data => {
                    this.detail = data;
                    this.modalOpen = true;
                })
                .catch(err => alert('Failed to fetch details.'));
        }
    }
}
</script>
@endsection
