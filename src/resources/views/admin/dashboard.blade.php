@extends('layouts.admin') @section('content')
<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4">Admin Dashboard</h2>
    </div>
    <div class="col-md-4">
        <div class="card bg-primary text-white p-3">
            <h5>Tổng Chiến Dịch</h5>
            <h3>{{ $stats['total_campaigns'] ?? 0 }}</h3>
        </div>
    </div>
    </div>
@endsection