@props([
    'paginator',
    'label' => 'bản ghi',
])

@php
    use App\Support\AdminPagination;
    $perPageOptions = AdminPagination::OPTIONS;
@endphp

@if(isset($paginator))
<div {{ $attributes->merge(['class' => 'd-flex flex-wrap align-items-center justify-content-between gap-3 mt-3']) }}>
    <div class="text-muted small">
        @if($paginator->total() > 0)
        Đang hiển thị {{ $label }} từ <strong>{{ $paginator->firstItem() }}</strong> đến <strong>{{ $paginator->lastItem() }}</strong> của <strong>{{ $paginator->total() }}</strong> {{ $label }}.
        @else
        Đang hiển thị <strong>0</strong> {{ $label }}.
        @endif
    </div>

    <div class="d-flex flex-wrap align-items-center gap-3 ms-sm-auto">
        <div class="d-flex align-items-center gap-2">
            <label for="pagination-per-page" class="text-muted small mb-0 text-nowrap">Hiển thị</label>
            <select
                id="pagination-per-page"
                class="form-select form-select-sm pagination-per-page-select"
                style="width: auto; min-width: 4.5rem;"
                aria-label="Số {{ $label }} mỗi trang"
            >
                @foreach($perPageOptions as $n)
                <option value="{{ $n }}" @selected($paginator->perPage() === $n)>{{ $n }}</option>
                @endforeach
            </select>
            {{-- <span class="text-muted small text-nowrap">/ trang</span> --}}
        </div>

        @if($paginator->hasPages())
        <div>
            {{ $paginator->withQueryString()->links('vendor.pagination.bootstrap-5-links-only') }}
        </div>
        @endif
    </div>
</div>

@once
@push('scripts')
<script>
document.addEventListener('change', function (e) {
    if (!e.target.matches('.pagination-per-page-select')) {
        return;
    }
    var url = new URL(window.location.href);
    url.searchParams.set('per_page', e.target.value);
    url.searchParams.delete('page');
    window.location.assign(url.toString());
});
</script>
@endpush
@endonce
@endif
