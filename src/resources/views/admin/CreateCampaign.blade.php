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
      
      {{-- BẮT ĐẦU FORM BAO QUANH CẢ BODY VÀ FOOTER --}}
      <form id="modalCreateCampaignForm">
        <div class="modal-body p-4 bg-light">
          <div class="mb-4">
            <label class="form-label fw-bold text-secondary small uppercase">{{ __('message.campaign_title') }}</label>
            <div class="input-group custom-input-group">
              <span class="input-group-text bg-white border-end-0 text-primary">
                <i class="bi bi-type"></i>
              </span>
              <input type="text" id="campaign-title" class="form-control border-start-0 ps-0 shadow-none" 
                     placeholder="{{ __('message.enter_campaign_title') }}" 
                     style="border-radius: 0 8px 8px 0;" required>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-bold text-secondary small uppercase">{{ __('message.campaign_content') }}</label>
            <textarea id="campaign-content" class="form-control shadow-none" rows="5" 
                      placeholder="{{ __('message.enter_campaign_content') }}" 
                      style="border-radius: 10px; resize: none; border: 1px solid #dee2e6;" required></textarea>
          </div>
        </div>

        <div class="modal-footer border-0 bg-white p-3">
          <button type="button" class="btn btn-link text-decoration-none text-muted fw-semibold" data-bs-dismiss="modal">
              Hủy bỏ
          </button>
          {{-- NÚT NÀY PHẢI LÀ TYPE SUBMIT VÀ NẰM TRONG FORM --}}
          <button type="submit" id="btn-save-new-campaign" class="btn btn-primary px-4 shadow-sm" style="border-radius: 8px;">
              <i class="bi bi-save me-2"></i>{{ __('message.save_changes') }}
          </button>
        </div>
      </form>

    </div>
  </div>
</div>

<style>
/* CSS giữ nguyên như của bạn, đã dọn dẹp tí chút */
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

    $(document).ready(function() {
  $(document).on('submit', '#modalCreateCampaignForm', function (e) {
    e.preventDefault();     
    
    const $btn = $('#btn-save-new-campaign'); 
    const config = window.LaravelData;
    const title = $('#campaign-title').val();
    const content = $('#campaign-content').val();

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
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: response.message || config.msgSuccess,
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });

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
            let errorMsg = config.msgError;
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;
                errorMsg = Object.values(errors).flat().join('<br>');
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            Swal.fire({
                icon: 'error',
                title: 'Ối, có lỗi rồi!',
                html: errorMsg, 
                confirmButtonColor: '#0d6efd'
            });
        },
        complete: function () {
            $btn.prop('disabled', false).html('<i class="bi bi-save me-2"></i> ' + config.msgComplete);
        }
    });
});
    }); 
</script>
@endpush