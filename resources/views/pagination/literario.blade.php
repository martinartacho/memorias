@if ($paginator->hasPages())
<nav class="pagination-literario" aria-label="Navegación de páginas">

    {{-- Anterior --}}
    @if ($paginator->onFirstPage())
        <span class="page-link page-link--disabled">← Anterior</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" class="page-link">← Anterior</a>
    @endif

    {{-- Números --}}
    <div class="page-numbers">
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="page-link page-link--dots">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="page-link page-link--active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach
    </div>

    {{-- Siguiente --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="page-link">Siguiente →</a>
    @else
        <span class="page-link page-link--disabled">Siguiente →</span>
    @endif

</nav>
@endif
