@if ($paginator->hasPages())
<nav class="pagination" aria-label="Paginasi">
    @if (!$paginator->onFirstPage())
        <a class="btn" href="{{ $paginator->previousPageUrl() }}">Sebelumnya</a>
    @endif
    <span>Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}</span>
    @if ($paginator->hasMorePages())
        <a class="btn" href="{{ $paginator->nextPageUrl() }}">Berikutnya</a>
    @endif
</nav>
@endif
