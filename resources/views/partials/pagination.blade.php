@if ($paginator->hasPages())
<div style="display:flex; align-items:center; justify-content:space-between; margin-top:1rem; font-size:0.9rem; color:#64748b;">
    <div>
        Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results
    </div>
    <div style="display:flex; gap:0.5rem; align-items:center;">
        @if ($paginator->onFirstPage())
            <span class="btn" style="opacity:0.4; cursor:default;">‹ Prev</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="btn btn-edit">‹ Prev</a>
        @endif

        @foreach ($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page => $url)
            @if ($page == $paginator->currentPage())
                <span class="btn btn-primary">{{ $page }}</span>
            @else
                <a href="{{ $url }}" class="btn btn-edit">{{ $page }}</a>
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-edit">Next ›</a>
        @else
            <span class="btn" style="opacity:0.4; cursor:default;">Next ›</span>
        @endif
    </div>
</div>
@endif