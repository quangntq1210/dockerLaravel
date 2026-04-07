@extends('admin.layouts.admin')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endpush

@section('content')
<div class="row">
    <div class="col-md-12 mb-4">
       <h2 data-lang="dashboard.title"></h2>
    </div>
    
   <div class="col-md-3">
    <div class="card p-3 bg-white border-start border-primary border-4">
        <p data-lang="dashboard.total_campaign"></p>
        <h3 class="fw-bold" id="total-campaigns">{{ $stats['total_campaigns'] ?? 0 }}</h3>
    </div>
</div>

<div class="col-md-3">
    <div class="card p-3 bg-white border-start border-success border-4">
        <p data-lang="dashboard.subscriber"></p>
        <h3 class="fw-bold" id="total-subscribers">{{ $stats['total_subscribers'] ?? 0 }}</h3>
    </div>
</div>

</div>

<div class="row mt-5">
    <div class="col-md-12 mb-3">
   <h4 data-lang="dashboard.report"></h4>
    </div>

    <!-- FILTER -->
    <div class="col-md-12 mb-3">
        <div class="card p-3">
           <form id="filterForm" method="GET" action="{{ route('admin.dashboard') }}">
                <div class="row">
                    <div class="col-md-3">
                        <input type="text" name="q" class="form-control"
                               placeholder="Search email hoặc name"
                               value="{{ request('q') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="from" class="form-control"
                               value="{{ request('from') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="to" class="form-control"
                               value="{{ request('to') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="campaign_id" class="form-control"
                               placeholder="Campaign ID"
                               value="{{ request('campaign_id') }}">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary w-100" data-lang="dashboard.filter"></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- TABLE -->
    <div class="col-md-12">
        <div class="card p-3">
            <div id="campaignTable">
                @include('admin.partials.dashboard_table', ['data' => $data])
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<!-- <script>
$(document).ready(function () {

    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (response) {
                handleDashboardResponse(response);
            }
        });
    });

    $(document).on('click', '#campaignTable .pagination a', function (e) {
        e.preventDefault();
        let url = $(this).attr('href');
        $.ajax({
            url: url,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (response) {
                handleDashboardResponse(response);
            }
        });
    });
});
</script>
@endpush -->
@push('scripts')
<script src="{{ asset('js/dashboard-manager.js') }}"></script> <script>
$(document).ready(function () {

    DashboardManager.init({
        localeUpdateUrl: "{{ route('locale.update') }}",
        csrfToken: "{{ csrf_token() }}"
    });
});
</script>
@endpush
