@if ($paginator->hasPages())
    <div class="text-center mt-3 mt-sm-3">
        <ul class="pagination justify-content-center mb-0">
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-label="@lang('pagination.previous')">
                    <span class="page-link">Prev</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" rel="prev" href="{{ $paginator->previousPageUrl() }}"
                       aria-label="@lang('pagination.previous')">Prev</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" rel="next" href="{{ $paginator->nextPageUrl() }}"
                       aria-label="@lang('pagination.next')">Next</a>
                </li>
            @else
                <li class="page-item disabled">
                    <a class="page-link" rel="next" href="{{ $paginator->nextPageUrl() }}"
                       aria-label="@lang('pagination.next')">Next</a>
                </li>
            @endif
        </ul>
    </div>
@endif
