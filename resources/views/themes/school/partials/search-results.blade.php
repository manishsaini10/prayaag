{{-- Public search template. Rendered into themes.school.layout by SiteSearchController. --}}
<section class="pb-section">
    <div class="pb-row">
        <div class="pb-col pb-col--12">
            <div class="sec-head" style="margin-bottom:1.6rem">
                <span class="eyebrow">Search</span>
                <h1 class="sec-title">Find what you're looking for</h1>
            </div>

            <form method="GET" action="{{ url('/search') }}" style="display:flex;gap:10px;max-width:620px;margin:0 auto 2.2rem">
                <input type="search" name="q" value="{{ $q }}" placeholder="Search pages…" autofocus
                       style="flex:1;padding:.85rem 1.1rem;border:1px solid var(--line-strong, #cdd3df);border-radius:999px;font:inherit;outline:none">
                <button type="submit" class="btn btn-gold">Search</button>
            </form>

            @if ($q === '')
                <p class="muted" style="text-align:center;color:var(--muted)">Type a keyword above to search the site.</p>
            @elseif ($results->isEmpty())
                <p style="text-align:center;color:var(--muted)">No results found for &ldquo;<strong>{{ $q }}</strong>&rdquo;. Try a different keyword.</p>
            @else
                <p style="text-align:center;color:var(--muted);margin-bottom:1.2rem">{{ $results->count() }} result(s) for &ldquo;<strong>{{ $q }}</strong>&rdquo;</p>
                <div style="max-width:760px;margin:0 auto;display:flex;flex-direction:column;gap:12px">
                    @foreach ($results as $r)
                        @php($href = $r->slug === 'home' ? url('/') : url('/' . ltrim($r->slug, '/')))
                        <a href="{{ $href }}" class="card" style="display:block;padding:16px 20px;border:1px solid var(--line);border-radius:var(--radius-sm);background:#fff;transition:.2s" data-reveal>
                            <strong style="color:var(--navy);font-size:1.05rem">{{ $r->title }}</strong>
                            <div style="color:var(--muted);font-size:.85rem;margin-top:2px">{{ $href }}</div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>
