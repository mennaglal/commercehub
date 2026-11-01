{{-- Change 'layouts.admin' / 'content' to your real admin layout names. --}}
@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">{{ __('orders.title') }}</h4>
        <a href="{{ route('admin.orders.create') }}" class="btn btn-primary">{{ __('orders.add') }}</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Filters (GET, so the page can be bookmarked and pagination keeps them) --}}
    <form method="GET" class="row mb-3">
        <div class="col-md-3 mb-2">
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control" placeholder="{{ __('orders.search') }}">
        </div>

        <div class="col-md-2 mb-2">
            <select name="status" class="form-control">
                <option value="">{{ __('orders.columns.status') }}: {{ __('orders.all') }}</option>
                @foreach(\App\Models\Order::STATUSES as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ __('orders.statuses.' . $status) }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2 mb-2">
            <select name="payment_status" class="form-control">
                <option value="">{{ __('orders.columns.payment') }}: {{ __('orders.all') }}</option>
                @foreach(\App\Models\Order::PAYMENT_STATUSES as $status)
                    <option value="{{ $status }}" @selected(request('payment_status') === $status)>{{ __('orders.payment_statuses.' . $status) }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3 mb-2">
            <select name="delivery_status" class="form-control">
                <option value="">{{ __('orders.columns.delivery') }}: {{ __('orders.all') }}</option>
                @foreach(\App\Models\Order::DELIVERY_STATUSES as $status)
                    <option value="{{ $status }}" @selected(request('delivery_status') === $status)>{{ __('orders.delivery_statuses.' . $status) }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2 mb-2">
            <button class="btn btn-outline-primary w-100">{{ __('orders.filter') }}</button>
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                <tr>
                    <th>{{ __('orders.columns.number') }}</th>
                    <th>{{ __('orders.columns.customer') }}</th>
                    <th>{{ __('orders.columns.store') }}</th>
                    <th>{{ __('orders.columns.items') }}</th>
                    <th>{{ __('orders.columns.total') }}</th>
                    <th>{{ __('orders.columns.status') }}</th>
                    <th>{{ __('orders.columns.payment') }}</th>
                    <th>{{ __('orders.columns.delivery') }}</th>
                    <th>{{ __('orders.columns.date') }}</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->customer_name }}<br><small class="text-muted">{{ $order->customer_phone }}</small></td>
                        <td>{{ $order->items->pluck('store_name')->filter()->unique()->implode(', ') ?: '-' }}</td>
                        <td>{{ $order->items_count }}</td>
                        <td>{{ number_format($order->total, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ $order->badge('status') }}">{{ __('orders.statuses.' . $order->status) }}</span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $order->badge('payment_status') }}">{{ __('orders.payment_statuses.' . $order->payment_status) }}</span>
                            <br><small class="text-muted">{{ __('orders.payment_methods.' . $order->payment_method) }}</small>
                        </td>
                        <td>
                            <span class="badge bg-{{ $order->badge('delivery_status') }}">{{ __('orders.delivery_statuses.' . $order->delivery_status) }}</span>
                        </td>
                        <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-secondary">
                                {{ __('orders.fields.view') }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="text-center text-muted py-4">{{ __('orders.empty') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $orders->links() }}</div>
</div>
@endsection
