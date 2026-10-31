{{-- Change 'layouts.admin' / 'content' to your real admin layout names. --}}
@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">{{ $blogger->name }}</h4>
        <div>
            <a href="{{ route('admin.bloggers.edit', $blogger) }}" class="btn btn-outline-primary">{{ __('bloggers.edit') }}</a>
            <a href="{{ route('admin.bloggers.index') }}" class="btn btn-outline-secondary">{{ __('bloggers.back') }}</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Commission totals by status ($stats comes from one grouped query) --}}
    <div class="row mb-3">
        @foreach(['pending' => 'warning', 'earned' => 'success', 'cancelled' => 'danger'] as $key => $color)
            <div class="col-md-4 mb-2">
                <div class="card border-{{ $color }}">
                    <div class="card-body">
                        <div class="text-muted">{{ __('bloggers.stats.' . $key) }}</div>
                        <h4 class="mb-0">{{ number_format($stats[$key]->amount ?? 0, 2) }}</h4>
                        <small class="text-muted">{{ $stats[$key]->orders_count ?? 0 }} {{ __('bloggers.stats.orders') }}</small>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <p>
        <strong>{{ __('bloggers.coupons') }}:</strong>
        @forelse($blogger->coupons as $coupon)
            <a href="{{ route('admin.coupons.edit', $coupon) }}"
               class="badge bg-{{ $coupon->status ? 'primary' : 'secondary' }}">{{ $coupon->code }}</a>
        @empty
            -
        @endforelse
    </p>

    <div class="card">
        <div class="card-header fw-bold">{{ __('bloggers.orders') }}</div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                <tr>
                    <th>{{ __('bloggers.columns.order') }}</th>
                    <th>{{ __('orders.columns.total') }}</th>
                    <th>{{ __('bloggers.columns.commission') }}</th>
                    <th>{{ __('bloggers.columns.commission_status') }}</th>
                    <th>{{ __('bloggers.columns.date') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td><a href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }}</a></td>
                        <td>{{ number_format($order->total, 2) }}</td>
                        <td>{{ number_format($order->commission_amount, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ $order->badge('commission_status') }}">
                                {{ __('orders.commission_statuses.' . $order->commission_status) }}
                            </span>
                        </td>
                        <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">{{ __('bloggers.no_orders') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $orders->links() }}</div>
</div>
@endsection
