@if ($paginator->hasPages())
    @php
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();
        $startPage = max(1, $currentPage - 2);
        $endPage = min($lastPage, $startPage + 4);
        $startPage = max(1, $endPage - 4);
    @endphp

    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}">
        <ul class="pagination mb-0">
            <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                @if ($paginator->onFirstPage())
                    <span class="page-link" aria-hidden="true">&laquo;</span>
                @else
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}">&laquo;</a>
                @endif
            </li>

            @for ($page = $startPage; $page <= $endPage; $page++)
                <li class="page-item {{ $page === $currentPage ? 'active' : '' }}">
                    @if ($page === $currentPage)
                        <span class="page-link" aria-current="page">{{ $page }}</span>
                    @else
                        <a class="page-link" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                    @endif
                </li>
            @endfor

            <li class="page-item {{ $currentPage === $lastPage ? 'disabled' : '' }}">
                @if ($currentPage === $lastPage)
                    <span class="page-link" aria-hidden="true">&raquo;</span>
                @else
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}">&raquo;</a>
                @endif
            </li>
        </ul>
    </nav>
@endif
