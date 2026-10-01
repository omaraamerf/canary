{{-- Default paginator view (AppServiceProvider). Arrows point along the reading direction. --}}
@if($paginator->hasPages())
    <nav class="pagination" aria-label="{{ __('ui.pagination.label') }}">
        @if($paginator->onFirstPage())
            <span class="pagination-step is-disabled" aria-hidden="true"><x-lucide-chevron-right class="dir-icon" />{{ __('ui.pagination.previous') }}</span>
        @else
            <a class="pagination-step" href="{{ $paginator->previousPageUrl() }}" rel="prev"><x-lucide-chevron-right class="dir-icon" />{{ __('ui.pagination.previous') }}</a>
        @endif

        <ol class="pagination-pages">
            @foreach($elements as $element)
                @if(is_string($element))
                    <li><span class="pagination-gap">{{ $element }}</span></li>
                @else
                    @foreach($element as $page => $url)
                        <li>
                            @if($page == $paginator->currentPage())
                                <span class="pagination-page is-current" aria-current="page">{{ $page }}</span>
                            @else
                                <a class="pagination-page" href="{{ $url }}" aria-label="{{ __('ui.pagination.page', ['page' => $page]) }}">{{ $page }}</a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach
        </ol>

        @if($paginator->hasMorePages())
            <a class="pagination-step" href="{{ $paginator->nextPageUrl() }}" rel="next">{{ __('ui.pagination.next') }}<x-lucide-chevron-left class="dir-icon" /></a>
        @else
            <span class="pagination-step is-disabled" aria-hidden="true">{{ __('ui.pagination.next') }}<x-lucide-chevron-left class="dir-icon" /></span>
        @endif
    </nav>
@endif
