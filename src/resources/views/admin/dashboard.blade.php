@extends('admin.layouts.admin')

@section('content')
<div class="row">
    <div class="col-md-12 mb-4">
        <h2 class="fw-bold">Bảng điều khiển hệ thống</h2>
    </div>

    <div class="col-md-3">
        <div class="card p-3 bg-white border-start border-primary border-4">
            <p class="text-muted mb-1">Tổng Chiến Dịch</p>
            <h3 class="fw-bold">{{ $stats['total_campaigns'] ?? 0 }}</h3>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3 bg-white border-start border-success border-4">
            <p class="text-muted mb-1">Người Đăng Ký</p>
            <h3 class="fw-bold">{{ $stats['total_subscribers'] ?? 0 }}</h3>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3 bg-white border-start border-warning border-4">
            <p class="text-muted mb-1">Chiến Dịch Chờ Gửi</p>
            <h3 class="fw-bold">{{ $stats['pending_jobs'] ?? 0 }}</h3>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3 bg-white border-start border-danger border-4">
            <p class="text-muted mb-1">Thông Báo Đã Gửi</p>
            <h3 class="fw-bold">{{ $stats['sent_notifications'] ?? 0 }}</h3>
        </div>
    </div>
</div>
@endsection