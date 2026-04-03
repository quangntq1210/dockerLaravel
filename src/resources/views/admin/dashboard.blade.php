@extends('admin.layouts.admin')
<<<<<<< HEAD
@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush
=======
>>>>>>> d6edc5e93a1341eca53919208a0412602627170e

@section('content')
<div class="row">
    <div class="col-md-12 mb-4">
<<<<<<< HEAD
       <h2 data-lang="dashboard.title"></h2>
      
    </div>
    
    <div class="col-md-3">
        <div class="card p-3 bg-white border-start border-primary border-4">
           <p data-lang="dashboard.total_campaign"></p>
            <h3 class="fw-bold">{{ $stats['total_campaigns'] ?? 0 }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 bg-white border-start border-success border-4">
      <p data-lang="dashboard.subscriber"></p>
            <h3 class="fw-bold">{{ $stats['total_subscribers'] ?? 0 }}</h3>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function applyLanguage(lang) {
    $('[data-lang]').each(function () {
        let key = $(this).data('lang'); // vd: sidebar.title

        let keys = key.split('.');
        let value = lang;

        keys.forEach(k => {
            value = value[k];
        });

        if (value) {
            $(this).text(value);
        }
    });
}

function loadInitialLang() {
    let locale = $('#languageSwitcher').val();

    $.ajax({
        url: "{{ route('locale.update') }}",
        method: "PUT",
        data: {
            locale: locale,
            _token: "{{ csrf_token() }}"
        },
        success: function (response) {
            applyLanguage(response.lang);
        }
    });
}

$(document).ready(function () {

    loadInitialLang();

    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(response) {
                $('#campaignTable').html(response.table);
            }
        });
    });

    $('#languageSwitcher').on('change', function () {
        let locale = $(this).val();

        $.ajax({
            url: "{{ route('locale.update') }}",
            method: "PUT",
            data: {
                locale: locale,
                _token: "{{ csrf_token() }}"
            },
            success: function (response) {
                $('#campaignTable').html(response.table);
                applyLanguage(response.lang);
            }
        });
    });

});

</script>
@endpush

=======
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
>>>>>>> d6edc5e93a1341eca53919208a0412602627170e
