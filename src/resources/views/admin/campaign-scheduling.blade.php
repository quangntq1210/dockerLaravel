@extends('admin.layouts.admin')

@section('content')
<div class="row">
  <div class="col-md-12 mb-4">
    <h2 class="fw-bold">Lịch gửi thông báo</h2>
    <p class="text-muted">Chọn campaign, danh sách người nhận và thời gian gửi</p>
  </div>
</div>

<<<<<<< HEAD
=======
{{-- Display errors --}}
>>>>>>> d6edc5e93a1341eca53919208a0412602627170e
@if ($errors->any())
<div class="alert alert-danger alert-dismissible fade show">
  <ul class="mb-0">
    @foreach ($errors->all() as $error)
    <li>{{ $error }}</li>
    @endforeach
  </ul>
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<<<<<<< HEAD

=======
{{-- Display success message --}}
>>>>>>> d6edc5e93a1341eca53919208a0412602627170e
@if (session('success'))
<div class="alert alert-success alert-dismissible fade show">
  {{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Display form --}}
<div class="row">
  <div class="col-md-8">
    <div class="card shadow-sm">
      <div class="card-header bg-primary text-white">
<<<<<<< HEAD
        <h5 class="mb-0">📅 Tạo lịch gửi mới</h5>
      </div>
      <div class="card-body p-4">
        <form action="{{ route('admin.campaigns.store') }}" method="POST">
=======
        <h5 class="mb-0">Tạo lịch gửi mới</h5>
      </div>
      <div class="card-body p-4">
        <form action="{{ route('admin.campaign-scheduling.store') }}" method="POST">
>>>>>>> d6edc5e93a1341eca53919208a0412602627170e
          @csrf

          {{-- Select Campaign --}}
          <div class="mb-4">
            <label for="campaign_id" class="form-label fw-semibold">
              Campaign <span class="text-danger">*</span>
            </label>
            <select name="campaign_id" id="campaign_id"
              class="form-select @error('campaign_id') is-invalid @enderror">
              <option value="">-- Chọn campaign --</option>
              @foreach ($campaigns as $campaign)
              <option value="{{ $campaign->id }}"
                {{ old('campaign_id') == $campaign->id ? 'selected' : '' }}>
                {{ $campaign->title }}
                <span class="badge">{{ $campaign->status }}</span>
              </option>
              @endforeach
            </select>
            @error('campaign_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">Chỉ hiển thị campaign ở trạng thái draft/scheduled</small>
          </div>

          {{-- Select Subscribers --}}
          <div class="mb-4">
            <label class="form-label fw-semibold">
              Người nhận <span class="text-danger">*</span>
            </label>

            {{-- Search Subscribers with AJAX --}}
            <div class="input-group mb-2">
              <span class="input-group-text"><i>Tìm kiếm</i></span>
              <input type="text" id="subscriber-search"
                class="form-control"
                placeholder="Tìm theo tên hoặc email...">
            </div>

            {{-- Search results --}}
            <div id="search-results" class="border rounded p-2 mb-2"
              style="max-height: 180px; overflow-y: auto; display: none; background: #fff;">
              <small class="text-muted">Đang tìm kiếm...</small>
            </div>

            {{-- Selected subscribers (tags) --}}
            <div id="selected-subscribers" class="d-flex flex-wrap gap-2 p-2 border rounded bg-light"
              style="min-height: 50px;">
              <span class="text-muted small" id="empty-hint">Chưa chọn người nhận nào</span>
            </div>

<<<<<<< HEAD
  
=======
            {{-- Hidden inputs will be created dynamically by JS --}}
>>>>>>> d6edc5e93a1341eca53919208a0412602627170e
            <div id="subscriber-inputs"></div>

            @error('subscriber_ids')
            <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
            <small class="text-muted">Có thể chọn nhiều người nhận</small>
          </div>

<<<<<<< HEAD
=======
          {{-- Send time --}}
>>>>>>> d6edc5e93a1341eca53919208a0412602627170e
          <div class="mb-4">
            <label for="send_at" class="form-label fw-semibold">
              Thời gian gửi <span class="text-danger">*</span>
            </label>
            <input type="datetime-local"
              name="send_at"
              id="send_at"
              class="form-control @error('send_at') is-invalid @enderror"
              value="{{ old('send_at') }}"
              min="{{ now()->format('Y-m-d\TH:i') }}">
            @error('send_at')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">Phải chọn thời gian trong tương lai</small>
          </div>

<<<<<<< HEAD

=======
          {{-- Action buttons --}}
>>>>>>> d6edc5e93a1341eca53919208a0412602627170e
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary px-4">
              Lưu lịch gửi
            </button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary px-4">
              Huỷ
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
<<<<<<< HEAD
=======

  {{-- Guide panel --}}
>>>>>>> d6edc5e93a1341eca53919208a0412602627170e
  <div class="col-md-4">
    <div class="card border-info">
      <div class="card-header bg-info text-white">Hướng dẫn</div>
      <div class="card-body small">
        <ol class="ps-3">
          <li class="mb-2">Chọn <strong>Campaign</strong> muốn gửi (đã tạo sẵn).</li>
          <li class="mb-2">Tìm và thêm <strong>người nhận</strong> vào danh sách.</li>
          <li class="mb-2">Chọn <strong>thời gian gửi</strong> trong tương lai.</li>
          <li>Nhấn <strong>Lưu</strong> để đặt lịch.</li>
        </ol>
        <hr>
        <p class="mb-1"><strong>Trạng thái sau khi lưu:</strong></p>
        <span class="badge bg-warning text-dark">scheduled</span>
        <p class="mt-2 text-muted">Scheduler sẽ tự động dispatch job khi đến giờ.</p>
      </div>
    </div>
  </div>
</div>

<<<<<<< HEAD
=======
{{-- Bootstrap JS + jQuery + AJAX Search --}}
>>>>>>> d6edc5e93a1341eca53919208a0412602627170e
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  const selectedIds = new Set();

  // Debounce search
  let searchTimer;
  $('#subscriber-search').on('input', function() {
    clearTimeout(searchTimer);
    const q = $(this).val().trim();
    if (q.length < 2) {
      $('#search-results').hide();
      return;
    }
    searchTimer = setTimeout(() => searchSubscribers(q), 300);
  });

  function searchSubscribers(q) {
    $('#search-results').show().html('<small class="text-muted">Đang tìm...</small>');
    $.get('/admin/subscribers/search', {
      q
    }, function(data) {
      if (!data.length) {
        $('#search-results').html('<small class="text-muted">Không tìm thấy kết quả</small>');
        return;
      }
      let html = '';
      data.forEach(sub => {
        const disabled = selectedIds.has(sub.id) ? 'text-muted' : 'text-primary';
        html += `<div class="subscriber-item d-flex justify-content-between align-items-center py-1 px-2 border-bottom"
                              style="cursor:pointer"
                              data-id="${sub.id}" data-name="${sub.name}" data-email="${sub.email}">
                            <span>${sub.name} <small class="text-muted">(${sub.email})</small></span>
                            <span class="${disabled} small">${selectedIds.has(sub.id) ? '✓ Đã chọn' : '+ Thêm'}</span>
                         </div>`;
      });
      $('#search-results').html(html);
    });
  }

  // Click select subscriber from search results
  $(document).on('click', '.subscriber-item', function() {
    const id = $(this).data('id');
    const name = $(this).data('name');
    const email = $(this).data('email');
    if (selectedIds.has(id)) return;

    selectedIds.add(id);
    $('#empty-hint').hide();

    // Add tag to display
    $('#selected-subscribers').append(
      `<span class="badge bg-primary d-flex align-items-center gap-1" id="tag-${id}">
                ${name}
                <button type="button" class="btn-close btn-close-white btn-sm"
                    onclick="removeSubscriber(${id})" style="font-size:0.6rem"></button>
             </span>`
    );

    // Add hidden input
    $('#subscriber-inputs').append(
      `<input type="hidden" name="subscriber_ids[]" value="${id}" id="input-${id}">`
    );

    // Update search results
    $(`[data-id="${id}"] span:last`).removeClass('text-primary').addClass('text-muted').text('✓ Đã chọn');
  });

  function removeSubscriber(id) {
    selectedIds.delete(id);
    $(`#tag-${id}`).remove();
    $(`#input-${id}`).remove();
    if (selectedIds.size === 0) $('#empty-hint').show();
    // Reset state in search results
    $(`[data-id="${id}"] span:last`).removeClass('text-muted').addClass('text-primary').text('+ Thêm');
  }

  // Hide search results when clicking outside
  $(document).on('click', function(e) {
    if (!$(e.target).closest('#subscriber-search, #search-results').length) {
      $('#search-results').hide();
    }
  });
</script>
@endsection