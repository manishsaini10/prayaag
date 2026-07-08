@if ($items->count())
    @foreach ($items as $item)
        @php
            $path = '/storage/' . ltrim($item->path, '/');
            $isImage = str_starts_with((string) $item->mime_type, 'image/');
            $size = $item->size;
            $u = ['B','KB','MB','GB']; $k = 0; $b = (int) $size;
            while ($b >= 1024 && $k < 3) { $b /= 1024; $k++; }
            $sizeStr = round($b, 1) . ' ' . $u[$k];
            $dims = ($item->width && $item->height) ? $item->width . '×' . $item->height : '';
            $id = $item->getKey();
            $name = $item->original_name;
            $ext = $item->extension ?: 'file';
        @endphp

        @if (($mode ?? 'grid') === 'grid')
        <div class="media-item" data-media-id="{{ $id }}">
            <div class="media-thumb">
                @if ($isImage)
                    <img src="{{ $path }}" alt="{{ $name }}" loading="lazy" onerror="this.style.display='none';this.parentElement.innerHTML='<span class=\'media-ext\'>{{ $ext }}</span>'">
                @else
                    <span class="media-ext">{{ $ext }}</span>
                @endif
                <div class="media-actions">
                    <button class="ma-btn" onclick="copyUrl('{{ $path }}')" title="Copy URL">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                    </button>
                    <button class="ma-btn del" onclick="deleteItem('{{ $id }}', this)" title="Delete">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                    </button>
                </div>
            </div>
            <div class="media-info">
                <div class="media-name" title="{{ $name }}">{{ $name }}</div>
                <div class="media-meta">{{ $sizeStr }}{{ $dims ? ' · ' . $dims : '' }}</div>
            </div>
        </div>

        @elseif (($mode ?? '') === 'list')
        <div class="media-item" data-media-id="{{ $id }}">
            <div class="media-thumb">
                @if ($isImage)
                    <img src="{{ $path }}" alt="{{ $name }}" loading="lazy" onerror="this.style.display='none';this.parentElement.innerHTML='<span class=\'media-ext\'>{{ $ext }}</span>'">
                @else
                    <span class="media-ext">{{ $ext }}</span>
                @endif
            </div>
            <div class="media-name" title="{{ $name }}">{{ $name }}</div>
            <div class="media-meta">
                <span>{{ $sizeStr }}</span>
                @if ($dims)<span>{{ $dims }}</span>@endif
                <span>{{ $item->mime_type }}</span>
            </div>
            <div class="media-actions">
                <button class="ma-btn" onclick="copyUrl('{{ $path }}')">Copy URL</button>
                <button class="ma-btn del" onclick="deleteItem('{{ $id }}', this)">Delete</button>
            </div>
        </div>

        @else
        <div class="media-item" data-media-id="{{ $id }}">
            <div class="media-thumb">
                @if ($isImage)
                    <img src="{{ $path }}" alt="{{ $name }}" loading="lazy" onerror="this.style.display='none';this.parentElement.innerHTML='<span class=\'media-ext\'>{{ $ext }}</span>'">
                @else
                    <span class="media-ext">{{ $ext }}</span>
                @endif
            </div>
            <div class="media-info">
                <div class="media-name" title="{{ $name }}">{{ $name }}</div>
                <div class="media-meta">
                    <span>{{ $sizeStr }}</span>
                    @if ($dims)<span>{{ $dims }}</span>@endif
                    <span>{{ $item->mime_type }}</span>
                </div>
                <div class="media-actions">
                    <button class="ma-btn" onclick="copyUrl('{{ $path }}')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg> Copy URL</button>
                    <button class="ma-btn del" onclick="deleteItem('{{ $id }}', this)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg> Delete</button>
                </div>
            </div>
        </div>
        @endif

    @endforeach
@endif
