@if ($paginator->hasPages())
    <nav class="admin-pagination" role="navigation" aria-label="Səhifələmə Naviqasiyası">
        <div class="pagination-info">
            <span>Göstərilir: <strong>{{ $paginator->firstItem() ?? 0 }}-{{ $paginator->lastItem() ?? 0 }}</strong> / Cəmi: <strong>{{ $paginator->total() }}</strong></span>
        </div>
        <div class="pagination-links">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="page-link disabled" aria-disabled="true">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="page-link" rel="prev" aria-label="Əvvəlki">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="page-link dots">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="page-link active" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="page-link" rel="next" aria-label="Növbəti">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </a>
            @else
                <span class="page-link disabled" aria-disabled="true">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </span>
            @endif
        </div>
    </nav>
@endif
