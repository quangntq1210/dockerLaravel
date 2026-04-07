{{-- File: admin/CreateCampaign.blade.php --}}
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered"> 
    <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
      
      <div class="modal-header bg-primary text-white py-3">
        <h5 class="modal-title fw-bold" id="exampleModalLabel">
            <i class="bi bi-pencil-square me-2"></i>{{ $modalTitle ?? __('message.create_campaign') }}
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <div class="modal-body p-4 bg-light">
        {{-- SỬA ID FORM TẠI ĐÂY --}}
        <form id="modalCreateCampaignForm">
          <div class="mb-4">
            <label class="form-label fw-bold text-secondary small uppercase">{{ __('message.campaign_title') }}</label>
            <div class="input-group custom-input-group">
              <span class="input-group-text bg-white border-end-0 text-primary">
                <i class="bi bi-type"></i>
              </span>
              {{-- THÊM ID: campaign-title --}}
              <input type="text" id="campaign-title" class="form-control border-start-0 ps-0 shadow-none" 
                     placeholder="{{ __('message.enter_campaign_title') }}" 
                     style="border-radius: 0 8px 8px 0;"
                   >
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-bold text-secondary small uppercase">{{ __('message.campaign_content') }}</label>
            {{-- THÊM ID: campaign-content --}}
            <textarea id="campaign-content" class="form-control shadow-none" rows="5" 
                      placeholder="{{ __('message.enter_campaign_content') }}" 
                      style="border-radius: 10px; resize: none; border: 1px solid #dee2e6;"></textarea>
          </div>
        </form>
      </div>

      <div class="modal-footer border-0 bg-white p-3">
        <button type="button" class="btn btn-link text-decoration-none text-muted fw-semibold" data-bs-dismiss="modal">
            Hủy bỏ
        </button>
        {{-- SỬA ID NÚT: btn-save-campaign --}}
        <button type="button" id="btn-save-new-campaign" class="btn btn-primary px-4 shadow-sm" style="border-radius: 8px;" >
            <i class="bi bi-save me-2"></i>{{ __('message.save_changes') }}
        </button>
      </div>

    </div>
  </div>
</div>

<style>
/* CSS để Modal trông chuyên nghiệp hơn */
.custom-input-group {
    transition: all 0.3s ease;
}

.custom-input-group:focus-within {
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
}

.custom-input-group .input-group-text {
    border-radius: 8px 0 0 8px;
    border: 1px solid #dee2e6;
}

.custom-input-group .form-control {
    border: 1px solid #dee2e6;
}

.form-label.small {
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
    display: block;
}

/* Hiệu ứng hover cho nút */
.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3) !important;
    transition: all 0.2s;
}
</style>
@push('scripts')
<script>
    window.LaravelData = {
        storeRoute: "{{ route('admin.campaigns.store') }}",
        csrfToken: "{{ csrf_token() }}",
        msgSaving: "{{ __('message.saving') }}",
        msgSuccess: "{{ __('message.save_success') }}",
        msgComplete: "{{ __('message.save_complete') }}",
        msgError: "{{ __('message.save_error') }}"
    };

    $(document).on('click', '#btn-save-new-campaign', function (e) {
        e.preventDefault();
        const $btn = $(this);
        const config = window.LaravelData; // Sử dụng object đã khai báo trên

        const title = $('#campaign-title').val();
        const content = $('#campaign-content').val();

        if(!title || !content) {
            alert("Vui lòng nhập đầy đủ thông tin");
            return;
        }

        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> ' + config.msgSaving);

        $.ajax({
            url: config.storeRoute,
            type: "POST",
            data: {
                _token: config.csrfToken,
                title: title,
                content: content
            },
            success: function (response) {
                if (response.success) {
                    alert(response.message || config.msgSuccess);
                    const $select = $('#campaign_id');
                    if ($select.length > 0) {
                        const newOption = new Option(response.data.title, response.data.id, true, true);
                        $select.append(newOption).trigger('change');
                    }
                    $('#exampleModal').modal('hide');
                    $('#modalCreateCampaignForm')[0].reset();
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                const errors = xhr.responseJSON ? xhr.responseJSON.errors : null;
                alert(errors ? Object.values(errors).flat().join('\n') : config.msgError);
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="bi bi-save me-2"></i> ' + config.msgComplete);
            }
        });
    });
</script>
{{-- XÓA HOẶC COMMENT DÒNG DƯỚI NẾU BẠN ĐÃ VIẾT CODE VÀO ĐÂY --}}
{{-- <script src="{{ asset('js/createCampaign.js') }}"></script> --}}
@endpush