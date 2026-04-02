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
    </div>

    <div class="row mt-5">
        <div class="col-md-12 mb-3">
            <h4 class="fw-bold">Báo cáo Campaign</h4>
        </div>

        <!-- FILTER -->
        <div class="col-md-12 mb-3">
            <div class="card p-3">
                <form id="filterForm" method="GET" action="{{ route('admin.dashboard') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <input type="text" name="q" class="form-control" placeholder="Search email hoặc name"
                                value="{{ request('q') }}">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="from" class="form-control" value="{{ request('from') }}">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="to" class="form-control" value="{{ request('to') }}">
                        </div>
                        <div class="col-md-2">
                            <input type="text" name="campaign_id" class="form-control" placeholder="Campaign ID"
                                value="{{ request('campaign_id') }}">
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary w-100">Lọc dữ liệu</button>
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
        $(function() {
            // Filter form AJAX
            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: $(this).attr('action'),
                    data: $(this).serialize(),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        $('#campaignTable').html(response.table);
                    },
                    error: function() {
                        $('#campaignTable').html('<p class="text-danger">Lỗi tải dữ liệu</p>');
                    }
                });
            });

            // Pagination AJAX
            $(document).on('click', '#campaignTable .pagination a', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                $.ajax({
                    url: url,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        $('#campaignTable').html(response.table);
                    },
                    error: function() {
                        $('#campaignTable').html('<p class="text-danger">Lỗi tải dữ liệu</p>');
                    }
                });
            });
        });
        $('#languageSwitcher').on('change', function() {
            let locale = $(this).val();

            $.ajax({
                url: "{{ route('locale.update') }}",
                method: "PUT",
                data: {
                    locale: locale,
                    _token: "{{ csrf_token() }}"
                },
                success: function() {
                    // reload lại dashboard (AJAX)
                    $('#filterForm').submit();
                },
                error: function() {
                    alert('Lỗi đổi ngôn ngữ');
                }
            });
        });
    </script>
@endpush
