@extends('admin.layouts.app')

@section('content')
<div class="card border-warning">
    <div class="card-body text-center py-5">
        <div class="text-warning mb-3">
            <i class="fa-solid fa-triangle-exclamation fa-3x" aria-hidden="true"></i>
        </div>
        <h5 class="card-title">Không tìm thấy hợp đồng</h5>
        <p class="text-muted mb-4">
            Mã hợp đồng trên đường dẫn không khớp với hợp đồng nào trong hệ thống.
            @if($maHopDong !== '')
                <br>
                <span class="fw-semibold text-dark">Mã đã nhận:</span>
                <code>{{ $maHopDong }}</code>
            @endif
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-2">
            <a href="{{ route('admin.khach-hang.tao-hop-dong') }}" class="btn btn-primary">
                <i class="fa-solid fa-file-circle-plus me-1"></i> Tạo hợp đồng mới
            </a>
            <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-house me-1"></i> Trang chủ admin
            </a>
        </div>
    </div>
</div>
@endsection
