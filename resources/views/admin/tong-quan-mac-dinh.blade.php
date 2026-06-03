@extends('admin.layouts.app')

@section('title', 'Tổng quan | Wedding Studio')

@section('content')
<div class="row g-2 mb-3">
    <div class="col-12">
        <div class="card mb-0 border-0 shadow-sm overflow-hidden" style="border-left: 4px solid #7367f0; background: linear-gradient(125deg, rgba(115, 103, 240, 0.1) 0%, rgba(255, 159, 67, 0.06) 100%);">
            <div class="card-body py-4 px-4 text-center text-md-start">
                <div class="d-flex flex-column flex-md-row align-items-center gap-3">
                    <span class="avatar avatar-lg rounded bg-primary text-white shadow-sm">
                        <i class="ti tabler-smart-home fs-3"></i>
                    </span>
                    <div>
                        <h4 class="mb-1 text-heading">Chào mừng, {{ $user->name }}</h4>
                        <p class="text-muted mb-0">
                            Vai trò: <strong>{{ $user->role_label }}</strong>.
                            Chọn chức năng bên dưới hoặc dùng menu bên trái để bắt đầu.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if (count($linksNhanh) > 0)
<div class="row g-2">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header py-2 px-3">
                <h6 class="mb-0"><i class="ti tabler-apps me-1"></i>Chức năng được phép</h6>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    @foreach ($linksNhanh as $link)
                    <div class="col-6 col-md-4 col-lg-3">
                        <a href="{{ route($link['route']) }}" class="card h-100 mb-0 text-body text-decoration-none border shadow-none dashboard-link-card">
                            <div class="card-body py-3 text-center">
                                <span class="avatar avatar-sm rounded bg-label-primary mb-2">
                                    <i class="{{ $link['icon'] }}"></i>
                                </span>
                                <span class="d-block small fw-medium">{{ $link['label'] }}</span>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="alert alert-warning mb-0" role="alert">
    <i class="ti tabler-info-circle me-1"></i>
    Tài khoản chưa được gán quyền truy cập menu. Vui lòng liên hệ quản trị viên.
</div>
@endif

<style>
.dashboard-link-card {
    transition: transform 0.15s ease, border-color 0.15s ease;
}
.dashboard-link-card:hover {
    transform: translateY(-2px);
    border-color: rgba(115, 103, 240, 0.35) !important;
}
</style>
@endsection
