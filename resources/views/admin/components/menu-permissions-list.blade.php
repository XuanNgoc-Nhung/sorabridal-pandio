@props([
    'adminGetRoutes' => [],
    'checkAllId' => 'permCheckAll',
    'checkboxClass' => 'perm-checkbox',
])
<div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
    <span class="small text-muted mb-0">Menu</span>
    <div class="form-check mb-0">
        <input class="form-check-input" type="checkbox" id="{{ $checkAllId }}" title="Chọn/bỏ chọn tất cả">
        <label class="form-check-label small" for="{{ $checkAllId }}">Chọn tất cả</label>
    </div>
</div>
<div class="list-group list-group-flush border rounded menu-permissions-scroll" style="max-height: 60vh; overflow-y: auto;">
    @forelse($adminGetRoutes as $route)
    <div class="list-group-item d-flex align-items-start gap-2 py-2 px-3">
        <input class="form-check-input mt-1 flex-shrink-0 {{ $checkboxClass }}" type="checkbox"
               name="permissions[]" value="{{ $route['name'] }}" id="perm-{{ $checkAllId }}-{{ Str::slug($route['name']) }}">
        <label class="form-check-label flex-grow-1 mb-0 small menu-perm-label" for="perm-{{ $checkAllId }}-{{ Str::slug($route['name']) }}">
            <span class="d-block fw-medium text-body">{{ $route['description'] }}</span>
            <span class="d-block text-muted font-monospace" style="font-size: 0.8rem;">{{ $route['uri'] }}</span>
        </label>
    </div>
    @empty
    <div class="list-group-item text-muted text-center py-3">Chưa có menu nào trong cấu hình sidebar admin.</div>
    @endforelse
</div>
