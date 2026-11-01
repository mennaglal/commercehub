@extends('admin.layouts.app')

@php
    // Available status transitions.
    $nextStatuses = \App\Models\Order::STATUS_TRANSITIONS[$order->status] ?? [];
    $nextDeliveries = \App\Models\Order::DELIVERY_TRANSITIONS[$order->delivery_status] ?? [];

    // Show invoice as a link only when it is a valid HTTP(S) URL.
    $invoiceIsLink = \Illuminate\Support\Str::startsWith(
        (string) $order->invoice_url,
        ['https://', 'http://']
    );
@endphp

@section('content')
    <div class="container-fluid">

        {{-- ===================== Header ===================== --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">
                {{ __('orders.details') }}: {{ $order->order_number }}
            </h4>

            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
                {{ __('orders.back') }}
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $message)
                    <div>{{ $message }}</div>
                @endforeach
            </div>
        @endif

        <div class="row">

            {{-- ===================== Customer & Shipping ===================== --}}
            <div class="col-md-6 mb-3">
                <div class="card h-100">

                    <div class="card-header fw-bold">
                        {{ __('orders.sections.customer') }}
                    </div>

                    <div class="card-body">

                        <p class="mb-1">
                            <strong>{{ __('orders.fields.customer_name') }}:</strong>
                            {{ $order->customer_name }}
                        </p>

                        <p class="mb-3">
                            <strong>{{ __('orders.fields.customer_phone') }}:</strong>
                            {{ $order->customer_phone }}
                        </p>

                        <hr>

                        <h6 class="fw-bold mb-3">
                            {{ app()->getLocale() === 'ar' ? 'عنوان التوصيل' : 'Shipping Address' }}
                        </h6>

                        <p class="mb-1">
                            <strong>{{ app()->getLocale() === 'ar' ? 'الدولة' : 'Country' }}:</strong>
                            {{ $order->country?->name ?? '-' }}
                        </p>

                        <p class="mb-1">
                            <strong>{{ app()->getLocale() === 'ar' ? 'المدينة' : 'City' }}:</strong>
                            {{ $order->region?->name ?? '-' }}
                        </p>

                        <p class="mb-1">
                            <strong>{{ app()->getLocale() === 'ar' ? 'المنطقة' : 'Area' }}:</strong>
                            {{ $order->area?->name ?? '-' }}
                        </p>

                        <p class="mb-1">
                            <strong>{{ __('orders.fields.shipping_address') }}:</strong>
                            {{ $order->shipping_address }}
                        </p>

                        @if($order->shipping_notes)
                            <p class="mb-1">
                                <strong>
                                    {{ app()->getLocale() === 'ar' ? 'تعليمات التوصيل' : 'Delivery instructions' }}:
                                </strong>
                                {{ $order->shipping_notes }}
                            </p>
                        @endif

                        <p class="mb-0">
                            <strong>{{ __('orders.fields.shipping_total') }}:</strong>
                            {{ number_format($order->shipping_total, 2) }}
                        </p>

                        @if($order->notes)
                            <hr>
                            <p class="mb-0">
                                <strong>{{ __('orders.fields.notes') }}:</strong>
                                {{ $order->notes }}
                            </p>
                        @endif

                    </div>
                </div>
            </div>

            {{-- ===================== Order Status ===================== --}}
            <div class="col-md-6 mb-3">
                <div class="card h-100">

                    <div class="card-header fw-bold">
                        {{ __('orders.columns.status') }}
                    </div>

                    <div class="card-body">

                        <p>
                        <span class="badge bg-{{ $order->badge('status') }}">
                            {{ __('orders.statuses.' . $order->status) }}
                        </span>

                            <small class="text-muted ms-2">
                                {{ $order->created_at->format('Y-m-d H:i') }}
                            </small>
                        </p>

                        @if(!empty($nextStatuses))
                            <form
                                method="POST"
                                action="{{ route('admin.orders.status', $order) }}"
                                onsubmit="return this.status.value !== 'cancelled' || confirm(@js(__('orders.fields.cancel_warning')));"
                            >
                                @csrf
                                @method('PATCH')

                                <div class="input-group">

                                    <select name="status" class="form-control" required>
                                        @foreach($nextStatuses as $status)
                                            <option value="{{ $status }}">
                                                {{ __('orders.statuses.' . $status) }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <button class="btn btn-primary">
                                        {{ __('orders.fields.update') }}
                                    </button>

                                </div>
                            </form>
                        @else
                            <small class="text-muted">
                                {{ __('orders.fields.final') }}
                            </small>
                        @endif

                    </div>
                </div>
            </div>

            {{-- ===================== Payment ===================== --}}
            <div class="col-md-6 mb-3">
                <div class="card h-100">

                    <div class="card-header fw-bold">
                        {{ __('orders.sections.payment') }}
                    </div>

                    <div class="card-body">

                        <p class="mb-1">
                            <strong>{{ __('orders.fields.payment_method') }}:</strong>

                            {{ __('orders.payment_methods.' . $order->payment_method) }}

                            @if($order->payment_gateway)
                                ({{ config('orders.gateways.' . $order->payment_gateway, $order->payment_gateway) }})
                            @endif
                        </p>

                        <p class="mb-1">
                            <strong>{{ __('orders.fields.payment_status') }}:</strong>

                            <span class="badge bg-{{ $order->badge('payment_status') }}">
                            {{ __('orders.payment_statuses.' . $order->payment_status) }}
                        </span>
                        </p>

                        @if($order->paid_at)
                            <p class="mb-1">
                                <strong>{{ __('orders.fields.paid_at') }}:</strong>
                                {{ $order->paid_at->format('Y-m-d H:i') }}
                            </p>
                        @endif

                        @if($invoiceIsLink)
                            <p class="mb-2">
                                <a
                                    href="{{ $order->invoice_url }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    {{ __('orders.fields.open_invoice') }}
                                </a>
                            </p>
                        @endif

                        <hr>

                        <form method="POST" action="{{ route('admin.orders.payment', $order) }}">
                            @csrf
                            @method('PATCH')

                            <div class="mb-2">
                                <label class="form-label">
                                    {{ __('orders.fields.payment_status') }}
                                </label>

                                <select name="payment_status" class="form-control" required>
                                    @foreach(\App\Models\Order::PAYMENT_STATUSES as $status)
                                        <option
                                            value="{{ $status }}"
                                            @selected(old('payment_status', $order->payment_status) === $status)
                                        >
                                            {{ __('orders.payment_statuses.' . $status) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-2">
                                <label class="form-label">
                                    {{ __('orders.fields.payment_id') }}
                                </label>

                                <input
                                    type="text"
                                    name="payment_id"
                                    class="form-control"
                                    maxlength="255"
                                    value="{{ old('payment_id', $order->payment_id) }}"
                                >
                            </div>

                            <div class="mb-2">
                                <label class="form-label">
                                    {{ __('orders.fields.invoice_id') }}
                                </label>

                                <input
                                    type="text"
                                    name="invoice_id"
                                    class="form-control"
                                    maxlength="255"
                                    value="{{ old('invoice_id', $order->invoice_id) }}"
                                >
                            </div>

                            <div class="mb-2">
                                <label class="form-label">
                                    {{ __('orders.fields.invoice_url') }}
                                </label>

                                <input
                                    type="url"
                                    name="invoice_url"
                                    class="form-control"
                                    maxlength="2000"
                                    value="{{ old('invoice_url', $order->invoice_url) }}"
                                >
                            </div>

                            <button class="btn btn-primary">
                                {{ __('orders.fields.update') }}
                            </button>

                        </form>

                    </div>
                </div>
            </div>

            {{-- ===================== Delivery ===================== --}}
            <div class="col-md-6 mb-3">
                <div class="card h-100">

                    <div class="card-header fw-bold">
                        {{ __('orders.sections.delivery') }}
                    </div>

                    <div class="card-body">

                        <p class="mb-1">
                            <strong>{{ __('orders.fields.delivery_status') }}:</strong>

                            <span class="badge bg-{{ $order->badge('delivery_status') }}">
                            {{ __('orders.delivery_statuses.' . $order->delivery_status) }}
                        </span>
                        </p>

                        @if($order->tracking_number)
                            <p class="mb-1">
                                <strong>{{ __('orders.fields.tracking_number') }}:</strong>
                                {{ $order->tracking_number }}
                            </p>
                        @endif

                        @if($order->shipped_at)
                            <p class="mb-1">
                                <strong>{{ __('orders.fields.shipped_at') }}:</strong>
                                {{ $order->shipped_at->format('Y-m-d H:i') }}
                            </p>
                        @endif

                        @if($order->delivered_at)
                            <p class="mb-1">
                                <strong>{{ __('orders.fields.delivered_at') }}:</strong>
                                {{ $order->delivered_at->format('Y-m-d H:i') }}
                            </p>
                        @endif

                        <hr>

                        @if($order->status !== \App\Models\Order::STATUS_CONFIRMED)

                            <small class="text-muted">
                                {{ __('orders.fields.confirm_first') }}
                            </small>

                        @elseif(empty($nextDeliveries))

                            <small class="text-muted">
                                {{ __('orders.fields.final') }}
                            </small>

                        @else

                            <form
                                method="POST"
                                action="{{ route('admin.orders.delivery', $order) }}"
                                onsubmit="return this.delivery_status.value !== 'returned' || confirm(@js(__('orders.fields.return_warning')));"
                            >
                                @csrf
                                @method('PATCH')

                                <div class="mb-2">
                                    <label class="form-label">
                                        {{ __('orders.fields.delivery_status') }}
                                    </label>

                                    <select name="delivery_status" class="form-control" required>
                                        @foreach($nextDeliveries as $status)
                                            <option value="{{ $status }}">
                                                {{ __('orders.delivery_statuses.' . $status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">
                                        {{ __('orders.fields.tracking_number') }}
                                    </label>

                                    <input
                                        type="text"
                                        name="tracking_number"
                                        class="form-control"
                                        maxlength="100"
                                        value="{{ old('tracking_number', $order->tracking_number) }}"
                                    >
                                </div>

                                <button class="btn btn-primary">
                                    {{ __('orders.fields.update') }}
                                </button>

                            </form>

                        @endif

                    </div>
                </div>
            </div>

            {{-- ===================== Coupon and Blogger ===================== --}}
            @if($order->coupon_code)
                <div class="col-12 mb-3">
                    <div class="card">

                        <div class="card-header fw-bold">
                            {{ __('orders.sections.coupon') }}
                        </div>

                        <div class="card-body">

                            <p class="mb-1">
                                <strong>{{ __('orders.fields.coupon_code') }}:</strong>
                                {{ $order->coupon_code }}
                            </p>

                            <p class="mb-1">
                                <strong>{{ __('orders.fields.discount') }}:</strong>
                                {{ number_format($order->discount_total, 2) }}
                            </p>

                            @if($order->blogger_id)

                                <p class="mb-1">
                                    <strong>{{ __('orders.fields.blogger') }}:</strong>

                                    <a href="{{ route('admin.bloggers.show', $order->blogger_id) }}">
                                        {{ $order->blogger?->name ?? '-' }}
                                    </a>
                                </p>

                                <p class="mb-0">
                                    <strong>{{ __('orders.fields.commission') }}:</strong>

                                    {{ number_format($order->commission_amount, 2) }}

                                    <span class="badge bg-{{ $order->badge('commission_status') }}">
                                    {{ __('orders.commission_statuses.' . $order->commission_status) }}
                                </span>
                                </p>

                            @endif

                        </div>
                    </div>
                </div>
            @endif

        </div>

        {{-- ===================== Order Items ===================== --}}
        <div class="card mb-3">

            <div class="card-header fw-bold">
                {{ __('orders.sections.items') }}
            </div>

            <div class="table-responsive">

                <table class="table mb-0">

                    <thead>
                    <tr>
                        <th>{{ __('orders.columns.product') }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'المتجر' : 'Store' }}</th>
                        <th>{{ __('orders.columns.variant') }}</th>
                        <th>{{ __('orders.columns.sku') }}</th>
                        <th>{{ __('orders.columns.unit_price') }}</th>
                        <th>{{ __('orders.columns.quantity') }}</th>
                        <th>{{ __('orders.columns.line_total') }}</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse($order->items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>

                            <td>{{ $item->store_name ?? '-' }}</td>

                            <td>{{ $item->variant_label ?? '-' }}</td>

                            <td>{{ $item->sku ?? '-' }}</td>

                            <td>{{ number_format($item->unit_price, 2) }}</td>

                            <td>{{ $item->quantity }}</td>

                            <td>{{ number_format($item->line_total, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                {{ app()->getLocale() === 'ar' ? 'لا توجد منتجات في هذا الطلب' : 'No items found.' }}
                            </td>
                        </tr>
                    @endforelse
                    </tbody>

                </table>

            </div>
        </div>

        {{-- ===================== Totals ===================== --}}
        <div class="row justify-content-end">

            <div class="col-md-4">

                <table class="table">

                    <tr>
                        <td>{{ __('orders.fields.subtotal') }}</td>
                        <td class="text-end">
                            {{ number_format($order->subtotal, 2) }}
                        </td>
                    </tr>

                    @if($order->discount_total > 0)
                        <tr>
                            <td>{{ __('orders.fields.discount') }}</td>
                            <td class="text-end">
                                - {{ number_format($order->discount_total, 2) }}
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <td>{{ __('orders.fields.shipping_total') }}</td>
                        <td class="text-end">
                            {{ number_format($order->shipping_total, 2) }}
                        </td>
                    </tr>

                    <tr class="fw-bold">
                        <td>{{ __('orders.columns.total') }}</td>
                        <td class="text-end">
                            {{ number_format($order->total, 2) }}
                        </td>
                    </tr>

                </table>

            </div>
        </div>

    </div>
@endsection
