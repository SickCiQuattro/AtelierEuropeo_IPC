@if ($paginator->hasPages())
    <nav aria-label="@lang('Pagination Navigation')">
        <ul class="pagination pagination-sm flex-wrap justify-content-center mb-0 gap-2">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <button class="btn btn-ae btn-ae-outline-primary" type="button" disabled aria-hidden="true"><i
                            class="bi bi-chevron-left"></i></button>
                </li>
            @else
                <li>
                    <a class="btn btn-ae btn-ae-outline-primary" href="{{ $paginator->previousPageUrl() }}" rel="prev"
                        aria-label="@lang('pagination.previous')"><i class="bi bi-chevron-left"></i></a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="active" aria-current="page"><span class="btn btn-ae btn-ae-primary">{{ $page }}</span></li>
                        @else
                            <li><a class="btn btn-ae btn-ae-outline-primary" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a class="btn btn-ae btn-ae-outline-primary" href="{{ $paginator->nextPageUrl() }}" rel="next"
                        aria-label="@lang('pagination.next')"><i class="bi bi-chevron-right"></i></a>
                </li>
            @else
                <li aria-disabled="true" aria-label="@lang('pagination.next')">
                    <button class="btn btn-ae btn-ae-outline-primary" type="button" disabled aria-hidden="true"><i
                            class="bi bi-chevron-right"></i></button>
                </li>
            @endif
        </ul>
    </nav>
@endif