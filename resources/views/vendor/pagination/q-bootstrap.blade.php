@if ($paginator->hasPages())
    @php
        $isRtl = app()->getLocale() === 'ar';
        $useBootstrapIcons = ! request()->routeIs('admin.*');
        $iconPrefix = $useBootstrapIcons ? 'bi bi-' : 'fas fa-';
        $prevIcon = ($isRtl ? 'chevron-double-right' : 'chevron-double-left');
        $nextIcon = ($isRtl ? 'chevron-double-left'  : 'chevron-double-right');
        $prevLabel = __('pagination.previous');
        $nextLabel = __('pagination.next');
    @endphp
    <nav class="q-pagination-nav" role="navigation" aria-label="{{ __('pagination.label') }}">
        <ul class="pagination q-pagination-list mb-0">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item q-page-item q-page-prev disabled" aria-disabled="true" aria-label="{{ $prevLabel }}">
                    <span class="page-link q-page-link" aria-hidden="true">
                        <i class="{{ $iconPrefix }}{{ $prevIcon }} q-page-icon"></i>
                        <span class="q-page-text d-none d-sm-inline ms-1">{{ $prevLabel }}</span>
                    </span>
                </li>
            @else
                <li class="page-item q-page-item q-page-prev">
                    <a class="page-link q-page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ $prevLabel }}">
                        <i class="{{ $iconPrefix }}{{ $prevIcon }} q-page-icon"></i>
                        <span class="q-page-text d-none d-sm-inline ms-1">{{ $prevLabel }}</span>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item q-page-item q-page-dots disabled d-none d-sm-inline-block" aria-disabled="true">
                        <span class="page-link q-page-link">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item q-page-item q-page-num active d-none d-sm-inline-block" aria-current="page">
                                <span class="page-link q-page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item q-page-item q-page-num d-none d-sm-inline-block">
                                <a class="page-link q-page-link" href="{{ $url }}" aria-label="{{ __('pagination.goto_page', ['page' => $page]) }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item q-page-item q-page-next">
                    <a class="page-link q-page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ $nextLabel }}">
                        <span class="q-page-text d-none d-sm-inline me-1">{{ $nextLabel }}</span>
                        <i class="{{ $iconPrefix }}{{ $nextIcon }} q-page-icon"></i>
                    </a>
                </li>
            @else
                <li class="page-item q-page-item q-page-next disabled" aria-disabled="true" aria-label="{{ $nextLabel }}">
                    <span class="page-link q-page-link" aria-hidden="true">
                        <span class="q-page-text d-none d-sm-inline me-1">{{ $nextLabel }}</span>
                        <i class="{{ $iconPrefix }}{{ $nextIcon }} q-page-icon"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif