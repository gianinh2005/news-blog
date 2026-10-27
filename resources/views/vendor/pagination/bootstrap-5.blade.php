@if ($paginator->hasPages())
    <nav class="d-flex justify-content-between align-items-center w-100 flex-wrap gap-2">
        {{-- Giao diện cho màn hình nhỏ mobile --}}
        <div class="d-flex justify-content-between align-items-center w-100 d-sm-none">
            <span class="small text-muted">
                {{ $paginator->firstItem() }}-{{ $paginator->lastItem() }} / {{ $paginator->total() }}
            </span>
            <ul class="pagination pagination-sm mb-0">
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled"><span class="page-link">&laquo; Trước</span></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ $paginator->previousPageUrl() }}">&laquo; Trước</a></li>
                @endif

                @if ($paginator->hasMorePages())
                    <li class="page-item"><a class="page-link" href="{{ $paginator->nextPageUrl() }}">Sau &raquo;</a></li>
                @else
                    <li class="page-item disabled"><span class="page-link">Sau &raquo;</span></li>
                @endif
            </ul>
        </div>

        {{-- Giao diện chuẩn cho màn hình tablet & desktop --}}
        <div class="d-none d-sm-flex align-items-center justify-content-between w-100">
            <div class="small text-muted">
                Hiển thị từ <span class="fw-bold text-dark">{{ $paginator->firstItem() }}</span> đến <span class="fw-bold text-dark">{{ $paginator->lastItem() }}</span> trên tổng số <span class="fw-bold text-dark">{{ $paginator->total() }}</span> kết quả
            </div>

            <div>
                <ul class="pagination pagination-sm mb-0">
                    {{-- Trang trước --}}
                    @if ($paginator->onFirstPage())
                        <li class="page-item disabled" aria-disabled="true" aria-label="Trang trước">
                            <span class="page-link">&lsaquo;</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Trang trước">&lsaquo;</a>
                        </li>
                    @endif

                    {{-- Danh sách các trang --}}
                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                        @endif

                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Trang sau --}}
                    @if ($paginator->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Trang sau">&rsaquo;</a>
                        </li>
                    @else
                        <li class="page-item disabled" aria-disabled="true" aria-label="Trang sau">
                            <span class="page-link">&rsaquo;</span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
@endif
