@if ($data->lastPage() > 1)
    <ul class="pagination">
        {{-- First Page Link --}}
        @if ($data->onFirstPage())
            <li class="page-item disabled"><span class="page-link">« First</span></li>
        @else
            <li class="page-item"><a class="page-link" href="{{ $data->url(1) }}">« First</a></li>
        @endif

        {{-- Previous Page Link --}}
        @if ($data->onFirstPage())
            <li class="page-item disabled"><span class="page-link">‹</span></li>
        @else
            <li class="page-item"><a class="page-link" href="{{ $data->previousPageUrl() }}">‹</a></li>
        @endif

        {{-- Pagination Links --}}
        @php
            $currentPage = $data->currentPage();
            $lastPage = $data->lastPage();
            $startPage = max($currentPage - 5, 1);
            $endPage = min($currentPage + 5, $lastPage);
        @endphp

        {{-- First set of pages --}}
        @if ($startPage > 1)
            <li class="page-item"><a class="page-link" href="{{ $data->url(1) }}">1</a></li>
            @if ($startPage > 2)
                <li class="page-item disabled"><span class="page-link">...</span></li>
            @endif
        @endif

        {{-- Page Links --}}
        @foreach(range($startPage, $endPage) as $page)
            @if ($page == $currentPage)
                <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
            @else
                <li class="page-item"><a class="page-link" href="{{ $data->url($page) }}">{{ $page }}</a></li>
            @endif
        @endforeach

        {{-- Last set of pages --}}
        @if ($endPage < $lastPage)
            @if ($endPage < $lastPage - 1)
                <li class="page-item disabled"><span class="page-link">...</span></li>
            @endif
            <li class="page-item"><a class="page-link" href="{{ $data->url($lastPage) }}">{{ $lastPage }}</a></li>
        @endif

        {{-- Next Page Link --}}
        @if ($data->hasMorePages())
            <li class="page-item"><a class="page-link" href="{{ $data->nextPageUrl() }}">›</a></li>
        @else
            <li class="page-item disabled"><span class="page-link">›</span></li>
        @endif

        {{-- Last Page Link --}}
        @if ($data->hasMorePages())
            <li class="page-item"><a class="page-link" href="{{ $data->url($data->lastPage()) }}">Last »</a></li>
        @else
            <li class="page-item disabled"><span class="page-link">Last »</span></li>
        @endif
    </ul>
@endif
