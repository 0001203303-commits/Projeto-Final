@if ($paginator->hasPages())
    <nav class="paginacao" aria-label="Paginação">
        @if ($paginator->onFirstPage())
            <span class="pg desativado">‹ Anterior</span>
        @else
            <a class="pg" href="{{ $paginator->previousPageUrl() }}" rel="prev">‹ Anterior</a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="pg desativado">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="pg ativa" aria-current="page">{{ $page }}</span>
                    @else
                        <a class="pg" href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a class="pg" href="{{ $paginator->nextPageUrl() }}" rel="next">Próxima ›</a>
        @else
            <span class="pg desativado">Próxima ›</span>
        @endif
    </nav>
@endif