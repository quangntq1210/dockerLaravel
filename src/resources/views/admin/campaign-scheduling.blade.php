@extends('admin.layouts.admin')

@section('content')
    <div class="row">
        <div class="col-md-12 mb-4">
            <h2 class="fw-bold">{{ __('message.campaign_scheduling') }}</h2>
            <p class="text-muted">{{ __('message.campaign_scheduling_description') }}</p>
        </div>
    </div>

    {{-- Display success message --}}
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
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('message.create_campaign_scheduling') }}</h5>
                    <button type="button" style="float:right" class="btn btn-light btn-sm fw-bold shadow-sm"
                        data-bs-toggle="modal" data-bs-target="#exampleModal">
                        <i class="bi bi-plus-circle me-1"></i> {{ __('message.create_campaign') }}
                    </button>
                    @include('admin.CreateCampaign')
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.campaign-scheduling.store') }}" method="POST">
                        @csrf

                        {{-- Select Campaign --}}
                        <div class="mb-4">
                            <label for="campaign_id" class="form-label fw-semibold">
                                {{ __('message.campaign') }} <span class="text-danger">*</span>
                            </label>
                            <select name="campaign_id" id="campaign_id"
                                class="form-select @error('campaign_id') is-invalid @enderror" required>
                                <option value="">-- {{ __('message.select_campaign') }} --</option>
                                @if ($campaigns->isEmpty())
                                    <option value="">-- {{ __('message.campaigns_dispatch_empty') }} --</option>
                                @else
                                    @foreach ($campaigns as $campaign)
                                        <option value="{{ $campaign->id }}"
                                            {{ old('campaign_id') == $campaign->id ? 'selected' : '' }}>
                                            {{ $campaign->title }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('campaign_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">{{ __('message.campaign_only_show_draft_scheduled') }}</small>
                        </div>

                        {{-- Select Subscribers --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                {{ __('message.recipients') }} <span class="text-danger">*</span>
                            </label>

                            {{-- Search Subscribers with AJAX --}}
                            <div class="input-group mb-2">
                                <span class="input-group-text"><i>{{ __('message.search') }}</i></span>
                                <input type="text" id="subscriber-search" class="form-control"
                                    placeholder="{{ __('message.search_by_name_or_email') }}" required>
                            </div>

                            {{-- Search results --}}
                            <div id="search-results" class="border rounded p-2 mb-2"
                                style="max-height: 180px; overflow-y: auto; display: none; background: #fff;">
                                <small class="text-muted">{{ __('message.searching') }}</small>
                            </div>

                            {{-- Selected subscribers (tags) --}}
                            <div id="selected-subscribers" class="d-flex flex-wrap gap-2 p-2 border rounded bg-light"
                                style="min-height: 50px;">
                                <span class="text-muted small"
                                    id="empty-hint">{{ __('message.no_recipients_selected') }}</span>
                            </div>

                            {{-- Hidden inputs will be created dynamically by JS --}}
                            <div id="subscriber-inputs"></div>

                            @error('subscriber_ids')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">{{ __('message.can_select_multiple_recipients') }}</small>
                        </div>

                        {{-- Send time --}}
                        <div class="mb-4">
                            <label for="send_at" class="form-label fw-semibold">
                                {{ __('message.send_time') }} <span class="text-danger">*</span>
                            </label>
                            <input type="datetime-local" name="send_at" id="send_at"
                                class="form-control @error('send_at') is-invalid @enderror" value="{{ old('send_at') }}"
                                min="{{ now()->format('Y-m-d\TH:i') }}" required>
                            @error('send_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">{{ __('message.must_select_future_time') }}</small>
                        </div>

                        {{-- Action buttons --}}
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                {{ __('message.save_campaign_scheduling') }}
                            </button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary px-4">
                                {{ __('message.cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Guide panel --}}
        <div class="col-md-4">
            <div class="card border-info">
                <div class="card-header bg-info text-white">{{ __('message.guide') }}</div>
                <div class="card-body small">
                    <ol class="ps-3">
                        <li class="mb-2">{{ __('message.select_campaign_to_send') }}</li>
                        <li class="mb-2">{{ __('message.search_and_add_recipients') }}</li>
                        <li class="mb-2">{{ __('message.select_send_time') }}</li>
                        <li>{{ __('message.click_save_to_schedule') }}</li>
                    </ol>
                    <hr>
                    <p class="mb-1"><strong>{{ __('message.status_after_saving') }}</strong></p>
                    <span class="badge bg-warning text-dark">{{ __('message.scheduled') }}</span>
                    <p class="mt-2 text-muted">
                        {{ __('message.scheduler_will_automatically_dispatch_job_when_time_comes') }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
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
            $('#search-results')
                .show()
                .html('<small class="text-muted">{{ __('message.searching') }}</small>');

            $.get(@js(route('admin.subscribers.search')), {
                q: q
            }, function(data) {
                if (!data.length) {
                    $('#search-results').html(
                        '<small class="text-muted">{{ __('message.no_results_found') }}</small>');
                    return;
                }

                let html = '';

                data.forEach(sub => {
                    const isSelected = selectedIds.has(sub.id);
                    const disabledClass = isSelected ? 'text-muted' : 'text-primary';

                    html += `
        <div class="subscriber-item d-flex justify-content-between align-items-center py-1 px-2 border-bottom"
            style="cursor:pointer" data-id="${sub.id}" data-name="${sub.name}" data-email="${sub.email}">
            <span>
                ${sub.name}
                <small class="text-muted">(${sub.email})</small>
            </span>
            <span class="${disabledClass} small">
                ${isSelected ? '✓ {{ __('message.selected') }}' : '+ {{ __('message.add') }}'}
            </span>
        </div>
        `;
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
            <button type="button" class="btn-close btn-close-white btn-sm" onclick="removeSubscriber(${id})"
                style="font-size:0.6rem"></button>
        </span>`
            );

            // Add hidden input
            $('#subscriber-inputs').append(
                `<input type="hidden" name="subscriber_ids[]" value="${id}" id="input-${id}">`
            );

            // Update search results
            $(`[data-id="${id}"] span:last`).removeClass('text-primary').addClass('text-muted').text(
                '✓ {{ __('message.selected') }}');
        });

        function removeSubscriber(id) {
            selectedIds.delete(id);
            $(`#tag-${id}`).remove();
            $(`#input-${id}`).remove();
            if (selectedIds.size === 0) $('#empty-hint').show();
            // Reset state in search results
            $(`[data-id="${id}"] span:last`).removeClass('text-muted').addClass('text-primary').text(
                '+ {{ __('message.add') }}');
        }

        // Hide search results when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#subscriber-search, #search-results').length) {
                $('#search-results').hide();
            }
        });
    </script>
@endpush
