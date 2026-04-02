@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endpush

<table class="table table-bordered table-hover">
    <thead class="table-light">
        <tr>
          <th>{{ __('dashboard.id') }}</th>
            <th>{{ __('dashboard.title') }}</th>
            <th>{{ __('dashboard.status') }}</th>
            <th>{{ __('dashboard.send_time') }}</th>
            <th>{{ __('dashboard.total') }}</th>
            <th>{{ __('dashboard.sent') }}</th>
            <th>{{ __('dashboard.failed') }}</th>
        </tr>
    </thead>

    <tbody>
        @forelse($data as $row)
            <tr>
                <td>{{ $row->id }}</td>
                <td>{{ $row->title }}</td>
                <td>
                    @if($row->status == 'sent')
                 <span class="badge bg-success">{{ __('dashboard.status_sent') }}</span>
                    @elseif($row->status == 'processing')
                     <span class="badge bg-warning">{{ __('dashboard.status_processing') }}</span>
                    @else
                        <span class="badge bg-secondary">{{ $row->status }}</span>
                    @endif
                </td>
                <td>{{ \Carbon\Carbon::parse($row->send_at)->format('d/m/Y H:i') }}</td>
                <td>{{ $row->total }}</td>
                <td class="text-success fw-bold">{{ $row->sent }}</td>
                <td class="text-danger fw-bold">{{ $row->failed }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center">{{ __('dashboard.empty') }}</td>
            </tr>
        @endforelse
    </tbody>
</table>

<!-- PAGINATION -->
<div class="mt-3">
    {{ $data->appends(request()->query())->links() }}
</div>

