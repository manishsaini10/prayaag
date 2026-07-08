@extends('admin.layout')

@php
    $actions = $def['actions'] ?? ['index', 'create', 'store', 'edit', 'update', 'destroy'];
    $canCreate = in_array('create', $actions, true);
    $canEdit = in_array('edit', $actions, true);
    $canDelete = in_array('destroy', $actions, true);
@endphp

@section('title', $def['label'])
@section('subtitle', $items->total() . ' ' . \Illuminate\Support\Str::plural('record', $items->total()))

@section('actions')
    <div class="flex items-center gap-2 flex-wrap">
        @if (! empty($def['search']))
            <form method="GET" action="{{ url('/admin/m/'.$resource) }}" class="flex items-center gap-2">
                <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Search…" class="inline-select" style="min-width:200px;padding:8px 12px">
                <button class="btn-sm" type="submit">Search</button>
                @if (!empty($q))<a class="btn-sm" href="{{ url('/admin/m/'.$resource) }}">Clear</a>@endif
            </form>
        @endif
        @if ($canCreate)
            <a href="{{ url('/admin/m/'.$resource.'/create') }}" class="btn primary"><x-admin.icon name="plus"/> New {{ strtolower($def['singular'] ?? 'item') }}</a>
        @endif
    </div>
@endsection

@section('content')

<style>
    .media-thumb-wrap{width:44px;height:44px;border-radius:8px;overflow:hidden;background:var(--surface-hover);display:grid;place-items:center}
    .media-thumb{width:100%;height:100%;object-fit:cover}
    .media-ext{font-weight:700;font-size:11px;color:var(--text-muted);text-transform:uppercase}
    .copy-url-btn{display:inline-flex;align-items:center;gap:4px;padding:4px 8px;border-radius:6px;border:1px solid var(--border);background:var(--surface);color:var(--text-muted);font-size:11.5px;cursor:pointer;transition:.15s;line-height:1}
    .copy-url-btn:hover{border-color:var(--primary);color:var(--primary)}
    .copy-url-btn.copied{background:var(--success-soft);border-color:var(--success);color:var(--success)}
</style>

@if (session('status'))
    <div class="card" style="border-color:var(--success);background:var(--success-soft);color:var(--success);padding:12px 16px;margin-bottom:16px;font-size:13.5px;font-weight:500">
        {{ session('status') }}
    </div>
@endif

<div class="card" style="overflow:hidden">
    <table>
        <thead>
            <tr>
                @foreach ($def['columns'] as $col)
                    <th>{{ $col['label'] }}</th>
                @endforeach
                @if ($canEdit || $canDelete)<th style="text-align:right">Actions</th>@endif
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    @foreach ($def['columns'] as $col)
                        @php
                            $type = $col['type'] ?? 'text';
                            $key = $col['key'];
                            $val = $type === 'relation' ? null : data_get($item, $key);
                        @endphp
                        <td>
                            @switch($type)
                                @case('relation')
                                    {{ optional($item->{$key})->{$col['attr'] ?? 'name'} ?? '—' }}
                                    @break
                                @case('badge')
                                    @if ($val !== null && $val !== '')
                                        <span class="badge {{ \Illuminate\Support\Str::slug((string) $val) }}">{{ str_replace('_', ' ', $val) }}</span>
                                    @else — @endif
                                    @break
                                @case('bool')
                                    <span class="badge {{ $val ? 'published' : 'archived' }}">{{ $val ? 'Yes' : 'No' }}</span>
                                    @break
                                @case('image')
                                    @php
                                        $disk = $col['disk'] ?? 'public';
                                        $path = $item->path ?? '';
                                        $imgUrl = $path ? '/storage/' . ltrim($path, '/') : '';
                                    @endphp
                                    @if ($imgUrl && str_starts_with((string) $item->mime_type, 'image/'))
                                        <div class="media-thumb-wrap"><img class="media-thumb" src="{{ $imgUrl }}" alt="" loading="lazy" onerror="this.style.display='none'"></div>
                                    @else
                                        <span class="media-ext">{{ $item->extension ?: 'file' }}</span>
                                    @endif
                                    @break
                                @case('datetime')
                                    <span class="muted">{{ $val ? \Illuminate\Support\Carbon::parse($val)->diffForHumans() : '—' }}</span>
                                    @break
                                @case('date')
                                    <span class="muted">{{ $val ? \Illuminate\Support\Carbon::parse($val)->toFormattedDateString() : '—' }}</span>
                                    @break
                                @case('bytes')
                                    @php $b=(int) $val; $u=['B','KB','MB','GB']; $k=0; while($b>=1024 && $k<3){$b/=1024;$k++;} @endphp
                                    {{ $val ? round($b,1).' '.$u[$k] : '0 B' }}
                                    @break
                                @default
                                    {{ ($val !== null && $val !== '') ? \Illuminate\Support\Str::limit((string) $val, 70) : '—' }}
                            @endswitch
                        </td>
                    @endforeach
                    @if ($canEdit || $canDelete || isset($item->path))
                        <td style="text-align:right;white-space:nowrap">
                            @if (isset($item->path))
                                @php $copyUrl = '/storage/' . ltrim($item->path, '/'); @endphp
                                <button class="copy-url-btn" onclick="copyMediaUrl(this, '{{ $copyUrl }}')" title="Copy URL"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg> Copy URL</button>
                            @endif
                            @if ($canEdit)
                                <a class="btn-sm" href="{{ url('/admin/m/'.$resource.'/'.$item->getKey().'/edit') }}">Edit</a>
                            @endif
                            @if ($canDelete)
                                <form method="POST" action="{{ url('/admin/m/'.$resource.'/'.$item->getKey()) }}" style="display:inline" onsubmit="return confirm('Delete this {{ strtolower($def['singular'] ?? 'item') }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn-sm" type="submit" style="color:var(--danger)">Delete</button>
                                </form>
                            @endif
                        </td>
                    @endif
                </tr>
            @empty
                <tr><td colspan="{{ count($def['columns']) + 1 }}" class="empty">No {{ strtolower($def['label']) }} yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($items->lastPage() > 1)
    <div class="flex items-center justify-between mt-4 text-[13px]" style="color:var(--text-muted)">
        <span>Page {{ $items->currentPage() }} of {{ $items->lastPage() }} · {{ $items->total() }} total</span>
        <div class="flex items-center gap-2">
            @if ($items->onFirstPage())
                <span class="btn-sm" style="opacity:.5">Previous</span>
            @else
                <a class="btn-sm" href="{{ $items->previousPageUrl() }}">Previous</a>
            @endif
            @if ($items->hasMorePages())
                <a class="btn-sm" href="{{ $items->nextPageUrl() }}">Next</a>
            @else
                <span class="btn-sm" style="opacity:.5">Next</span>
            @endif
        </div>
    </div>
@endif

<script>
    function copyMediaUrl(btn, url) {
        navigator.clipboard.writeText(window.location.origin + url).then(() => {
            btn.classList.add('copied');
            const orig = btn.innerHTML;
            btn.innerHTML = 'Copied!';
            setTimeout(() => { btn.classList.remove('copied'); btn.innerHTML = orig; }, 1500);
        }).catch(() => {
            btn.innerHTML = 'Failed';
            setTimeout(() => { btn.innerHTML = orig; }, 1500);
        });
    }
</script>

@endsection
