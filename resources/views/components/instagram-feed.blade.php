<div class="instagram-feed-widget" data-account="{{ $accountId }}" data-limit="{{ $limit }}" data-layout="{{ $layout }}" data-filter="{{ $filterType ?? 'all' }}">
    @if ($heading || $subheading)
        <div class="sec-head" data-reveal>
            @if ($heading)<h2 class="sec-title">{{ $heading }}</h2>@endif
            @if ($subheading)<p class="sec-sub">{{ $subheading }}</p>@endif
        </div>
    @endif

    @if ($profile && $showButton)
        <div class="instagram-profile" style="display:flex;align-items:center;gap:14px;margin-bottom:18px;padding:14px 18px;background:var(--surface);border:1px solid var(--border);border-radius:12px;max-width:420px">
            <div style="width:46px;height:46px;border-radius:50%;background:linear-gradient(135deg,#f58529,#dd2a7b,#8134af);display:grid;place-items:center;color:#fff;font-weight:700;font-size:18px;flex-shrink:0;text-transform:uppercase">{{ substr($profile['username'] ?? 'IG', 0, 2) }}</div>
            <div style="flex:1;min-width:0">
                <div style="font-weight:600;font-size:14px;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $profile['username'] }}</div>
                <div style="font-size:12px;color:var(--text-muted)">{{ number_format($profile['followers']) }} followers · {{ $profile['media_count'] }} posts</div>
            </div>
            <a href="https://www.instagram.com/{{ $profile['username'] }}/" target="_blank" rel="noopener" class="btn primary" style="font-size:12px;padding:6px 14px;border-radius:8px;white-space:nowrap">Follow</a>
        </div>
    @endif

    @if ($filterType === null)
    <div class="ig-filter-bar" style="display:flex;gap:6px;margin-bottom:14px;flex-wrap:wrap">
        <button class="ig-filter-btn active" data-filter="all" style="padding:5px 14px;border-radius:20px;border:1px solid var(--border);background:var(--primary);color:#fff;font-size:12px;cursor:pointer">All</button>
        <button class="ig-filter-btn" data-filter="IMAGE" style="padding:5px 14px;border-radius:20px;border:1px solid var(--border);background:var(--surface);color:var(--text);font-size:12px;cursor:pointer">Images</button>
        <button class="ig-filter-btn" data-filter="VIDEO" style="padding:5px 14px;border-radius:20px;border:1px solid var(--border);background:var(--surface);color:var(--text);font-size:12px;cursor:pointer">Reels</button>
    </div>
    @endif

    @php
        $colStyle = match($layout) {
            'carousel' => 'display:flex;gap:8px;overflow-x:auto;scroll-snap-type:x mandatory;-webkit-overflow-scrolling:touch;padding-bottom:8px;scrollbar-width:thin',
            'masonry' => 'columns:' . $columnsDesktop . ';gap:8px',
            'highlight' => 'display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:12px',
            default => 'display:grid;grid-template-columns:repeat(' . $columnsDesktop . ',1fr);gap:8px',
        };
    @endphp

    <div class="ig-feed-grid ig-layout-{{ $layout }}" style="{{ $colStyle }}" data-desktop="{{ $columnsDesktop }}" data-tablet="{{ $columnsTablet }}" data-mobile="{{ $columnsMobile }}">
        @forelse ($feed as $item)
            <div class="ig-feed-item" style="position:relative;overflow:hidden;border-radius:10px;cursor:pointer;{{ $layout === 'carousel' ? 'flex:0 0 auto;scroll-snap-align:start;width:' . (100 / max(1, min($columnsDesktop, 3))) . '%' : ($layout === 'masonry' ? 'break-inside:avoid;margin-bottom:8px' : 'aspect-ratio:1') }}" data-type="{{ $item['media_type'] }}"@if($showPopup) onclick="igPopup('{{ $item['media_url'] }}','{{ $item['media_type'] }}','{{ $item['permalink'] }}','{{ addslashes($item['caption'] ?? '') }}','{{ $item['timestamp'] }}','{{ $item['likes'] }}')"@endif>
                @if ($item['media_type'] === 'VIDEO')
                    <span style="position:absolute;top:6px;right:6px;z-index:2;background:rgba(0,0,0,.6);color:#fff;font-size:10px;padding:3px 9px;border-radius:5px">▶ Reel</span>
                @elseif ($item['media_type'] === 'CAROUSEL_ALBUM')
                    <span style="position:absolute;top:6px;right:6px;z-index:2;background:rgba(0,0,0,.6);color:#fff;font-size:10px;padding:3px 9px;border-radius:5px">📷 Album</span>
                @endif
                <img src="{{ $item['thumbnail_url'] ?? $item['media_url'] }}" alt="{{ strip_tags($item['caption'] ?? 'Instagram post') }}" loading="lazy" style="width:100%;height:100%;object-fit:cover;display:block;transition:transform .4s">
                @if ($showCaption && $item['caption'])
                    <div style="position:absolute;bottom:0;left:0;right:0;padding:8px 10px;background:linear-gradient(transparent,rgba(0,0,0,.7));color:#fff;font-size:12px;line-height:1.3;opacity:0;transition:opacity .3s" class="ig-caption">{{ Str::limit($item['caption'], 80) }}</div>
                @endif
            </div>
        @empty
            <div style="grid-column:1/-1;text-align:center;padding:40px 20px;color:var(--text-muted);font-size:14px">
                <div style="font-size:48px;margin-bottom:10px">📸</div>
                <p>No Instagram posts yet. Connect your account in the admin panel.</p>
            </div>
        @endforelse
    </div>

    @if ($hasMore && $infiniteScroll)
        <div style="text-align:center;margin-top:16px">
            <button class="btn ig-load-more" style="font-size:13px;cursor:pointer" data-offset="{{ count($feed) }}">Load More</button>
        </div>
    @endif

    @if ($showPopup)
    <div id="ig-popup" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,.88);z-index:99999;justify-content:center;align-items:center;padding:20px" onclick="if(event.target===this)igPopupClose()">
        <div style="position:relative;max-width:600px;width:100%;background:var(--surface);border-radius:14px;overflow:hidden" onclick="event.stopPropagation()">
            <button onclick="igPopupClose()" style="position:absolute;top:10px;right:10px;z-index:10;background:rgba(0,0,0,.5);border:none;color:#fff;width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:18px">✕</button>
            <div id="ig-popup-media" style="background:#000;text-align:center;max-height:500px;overflow:hidden">
                <img id="ig-popup-img" src="" alt="" style="max-width:100%;max-height:500px;object-fit:contain;display:block;margin:auto">
                <video id="ig-popup-video" src="" controls style="max-width:100%;max-height:500px;display:none;margin:auto"></video>
            </div>
            <div style="padding:14px 16px;font-size:13px;color:var(--text)">
                <div id="ig-popup-caption" style="margin-bottom:8px"></div>
                <div style="display:flex;gap:12px;font-size:12px;color:var(--text-muted)">
                    <span id="ig-popup-date"></span>
                    <span id="ig-popup-likes">❤ 0</span>
                </div>
                <a id="ig-popup-link" href="#" target="_blank" rel="noopener" style="display:inline-block;margin-top:10px;color:var(--primary);font-weight:600">View on Instagram →</a>
            </div>
        </div>
    </div>
    <script>
    function igPopup(url,type,link,caption,date,likes){
        var p=document.getElementById('ig-popup'),i=document.getElementById('ig-popup-img'),v=document.getElementById('ig-popup-video');
        if(type==='VIDEO'||type==='REEL'){i.style.display='none';v.style.display='block';v.src=url;v.load();}else{v.style.display='none';i.style.display='block';i.src=url;}
        document.getElementById('ig-popup-caption').textContent=caption||'';
        document.getElementById('ig-popup-date').textContent=date?'📅 '+new Date(date).toLocaleDateString('en-US',{year:'numeric',month:'short',day:'numeric'}):'';
        document.getElementById('ig-popup-likes').textContent='❤ '+(likes||0);
        document.getElementById('ig-popup-link').href=link||'#';
        p.style.display='flex';document.body.style.overflow='hidden';
    }
    function igPopupClose(){var p=document.getElementById('ig-popup'),v=document.getElementById('ig-popup-video');p.style.display='none';v.pause();document.body.style.overflow='';}
    document.addEventListener('keydown',function(e){if(e.key==='Escape')igPopupClose();});
    // Hover + filter
    document.querySelectorAll('.ig-feed-item').forEach(function(el){el.addEventListener('mouseenter',function(){var c=this.querySelector('.ig-caption');if(c)c.style.opacity='1';});el.addEventListener('mouseleave',function(){var c=this.querySelector('.ig-caption');if(c)c.style.opacity='0';});});
    document.querySelectorAll('.ig-filter-btn').forEach(function(b){b.addEventListener('click',function(){var t=this.dataset.filter;document.querySelectorAll('.ig-filter-btn').forEach(function(x){x.style.background=x===this?'var(--primary)':'var(--surface)';x.style.color=x===this?'#fff':'var(--text)';}.bind(this));document.querySelectorAll('.ig-feed-item').forEach(function(i){i.style.display=t==='all'||i.dataset.type===t?'':'none';});});});
    // Load more
    document.querySelectorAll('.ig-load-more').forEach(function(b){b.addEventListener('click',function(){var btn=this;btn.disabled=true;btn.textContent='Loading...';var w=btn.closest('.instagram-feed-widget');var p=new URLSearchParams({account:w.dataset.account,limit:w.dataset.limit,offset:btn.dataset.offset,filter:w.dataset.filter});fetch('/__ig/feed?'+p.toString()).then(function(r){return r.json();}).then(function(d){if(d.data.length){var g=w.querySelector('.ig-feed-grid');d.data.forEach(function(item){g.insertAdjacentHTML('beforeend','<div class="ig-feed-item" style="position:relative;overflow:hidden;border-radius:10px;cursor:pointer;aspect-ratio:1" data-type="'+item.media_type+'" onclick="igPopup(\''+item.media_url+'\',\''+item.media_type+'\',\''+item.permalink+'\',\''+(item.caption||'')+'\',\''+(item.timestamp||'')+'\',\''+item.likes+'\')"><img src="'+(item.thumbnail_url||item.media_url)+'" alt="" loading="lazy" style="width:100%;height:100%;object-fit:cover;display:block"></div>');});btn.dataset.offset=parseInt(btn.dataset.offset)+d.data.length;if(!d.has_more){btn.parentElement.remove();}else{btn.disabled=false;btn.textContent='Load More';}}});});});
    </script>
    @endif
</div>
