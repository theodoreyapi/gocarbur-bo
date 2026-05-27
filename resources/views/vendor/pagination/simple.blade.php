@if ($paginator->hasPages())
    <div class="pagination">
        {{-- Précédent --}}
        @if ($paginator->onFirstPage())
            <div class="page-item disabled">
                <i class="fa-solid fa-chevron-left" style="font-size:11px"></i>
            </div>
        @else
            <a class="page-item" href="{{ $paginator->previousPageUrl() }}">
                <i class="fa-solid fa-chevron-left" style="font-size:11px"></i>
            </a>
        @endif

        {{-- Pages --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <div class="page-item disabled">{{ $element }}</div>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <div class="page-item active">{{ $page }}</div>
                    @else
                        <a class="page-item" href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Suivant --}}
        @if ($paginator->hasMorePages())
            <a class="page-item" href="{{ $paginator->nextPageUrl() }}">
                <i class="fa-solid fa-chevron-right" style="font-size:11px"></i>
            </a>
        @else
            <div class="page-item disabled">
                <i class="fa-solid fa-chevron-right" style="font-size:11px"></i>
            </div>
        @endif
    </div>
@endif
