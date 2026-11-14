@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">{{ __('returns.title') }}</h4>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Filters (GET, so the page can be bookmarked and pagination keeps them) --}}
    <form method="GET" class="row mb-3">
        <div class="col-md-5 mb-2">
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control" placeholder="{{ __('returns.search') }}">
        </div>

        <div class="col-md-3 mb-2">
            <select name="status" class="form-control">
                <option value="">{{ __('returns.columns.status') }}: {{ __('returns.all') }}</option>
                @foreach(\App\Models\OrderReturn::STATUSES as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ __('returns.statuses.' . $status) }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2 mb-2">
            <button class="btn btn-outline-primary w-100">{{ __('returns.filter') }}</button>
        </div>
    </form>

    {{-- Bulk Delete Form --}}
    <form
        id="bulkDeleteReturnsForm"
        method="POST"
        action="{{ route('admin.bulk.delete') }}"
        data-bulk-delete
        data-selection-group="returns"
    >
        @csrf

        <input type="hidden" name="model" value="returns">
        <input type="hidden" name="ids">

        <button type="submit" class="btn btn-danger mb-3">
            @lang('dashboard.delete_all')
        </button>
    </form>
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                <tr>
                    <th>
                        <input
                            type="checkbox"
                            data-select-all="returns"
                            aria-label="Select all returns"
                        >
                    </th>
                    <th>{{ __('returns.columns.return_number') }}</th>
                    <th>{{ __('returns.columns.order') }}</th>
                    <th>{{ __('returns.columns.customer') }}</th>
                    <th>{{ __('returns.columns.reason') }}</th>
                    <th>{{ __('returns.columns.items') }}</th>
                    <th>{{ __('returns.columns.refund') }}</th>
                    <th>{{ __('returns.columns.status') }}</th>
                    <th>{{ __('returns.columns.date') }}</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse($returns as $return)
                    <tr>
                        <td>
                            <input
                                type="checkbox"
                                data-select-item="returns"
                                value="{{ $return->id }}"
                                aria-label="Select return {{ $return->return_number }}"
                            >
                        </td>
                        <td>{{ $return->return_number }}</td>
                        <td>
                            <a href="{{ route('admin.orders.show', $return->order_id) }}">{{ $return->order?->order_number }}</a>
                        </td>
                        <td>{{ $return->order?->customer_name }}</td>
                        <td>{{ __('returns.reasons.' . $return->reason) }}</td>
                        <td>{{ $return->items_count }}</td>
                        <td>{{ number_format($return->refund_amount, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ $return->badge() }}">{{ __('returns.statuses.' . $return->status) }}</span>
                        </td>
                        <td>{{ $return->created_at->format('Y-m-d H:i') }}</td>
                        <td class="text-nowrap">

                            <a
                                href="{{ route('admin.returns.show', $return) }}"
                                class="btn btn-sm btn-outline-secondary"
                            >
                                {{ __('returns.view') }}
                            </a>

                            <form
                                action="{{ route('admin.returns.destroy', $return) }}"
                                method="POST"
                                class="d-inline"
                                data-confirm-delete
                            >
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    {{ __('dashboard.Delete') }}
                                </button>
                            </form>

                        </td>
                    </tr>
                @empty
                    <td colspan="10" class="text-center text-muted py-4">
                        {{ __('returns.empty') }}
                    </td>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="d-flex justify-content-center">
        {{$returns->appends(request()->query())->links('vendor.pagination.medium') }}
    </div>
</div>
@endsection
