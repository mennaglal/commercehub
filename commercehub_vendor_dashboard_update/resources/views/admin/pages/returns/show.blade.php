@extends('admin.layouts.app')

@php
    // The statuses this return can move to now (see TRANSITIONS in the OrderReturn model).
    $next = \App\Models\OrderReturn::TRANSITIONS[$orderReturn->status] ?? [];
@endphp

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">{{ __('returns.details') }}: {{ $orderReturn->return_number }}</h4>
        <a href="{{ route('admin.returns.index') }}" class="btn btn-outline-secondary">{{ __('returns.back') }}</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Errors of all the small forms on this page --}}
    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $message)
                <div>{{ $message }}</div>
            @endforeach
        </div>
    @endif

    <div class="row">

        {{-- ===================== Information ===================== --}}
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header fw-bold">{{ __('returns.sections.info') }}</div>
                <div class="card-body">
                    <p class="mb-1">
                        <strong>{{ __('returns.columns.status') }}:</strong>
                        <span class="badge bg-{{ $orderReturn->badge() }}">{{ __('returns.statuses.' . $orderReturn->status) }}</span>
                    </p>
                    <p class="mb-1">
                        <strong>{{ __('returns.columns.order') }}:</strong>
                        <a href="{{ route('admin.orders.show', $orderReturn->order_id) }}">{{ $orderReturn->order?->order_number }}</a>
                    </p>
                    <p class="mb-1">
                        <strong>{{ __('returns.columns.customer') }}:</strong>
                        {{ $orderReturn->order?->customer_name }} ({{ $orderReturn->order?->customer_phone }})
                    </p>
                    <p class="mb-1"><strong>{{ __('returns.fields.reason') }}:</strong> {{ __('returns.reasons.' . $orderReturn->reason) }}</p>

                    @if($orderReturn->customer_note)
                        <p class="mb-1"><strong>{{ __('returns.fields.customer_note') }}:</strong> {{ $orderReturn->customer_note }}</p>
                    @endif
                    @if($orderReturn->admin_note)
                        <p class="mb-1"><strong>{{ __('returns.fields.admin_note') }}:</strong> {{ $orderReturn->admin_note }}</p>
                    @endif

                    <p class="mb-1"><strong>{{ __('returns.columns.date') }}:</strong> {{ $orderReturn->created_at->format('Y-m-d H:i') }}</p>
                    @if($orderReturn->approved_at)
                        <p class="mb-1"><strong>{{ __('returns.fields.approved_at') }}:</strong> {{ $orderReturn->approved_at->format('Y-m-d H:i') }}</p>
                    @endif
                    @if($orderReturn->received_at)
                        <p class="mb-1"><strong>{{ __('returns.fields.received_at') }}:</strong> {{ $orderReturn->received_at->format('Y-m-d H:i') }}</p>
                    @endif
                    @if($orderReturn->refunded_at)
                        <p class="mb-1"><strong>{{ __('returns.fields.refunded_at') }}:</strong> {{ $orderReturn->refunded_at->format('Y-m-d H:i') }}</p>
                    @endif
                    @if($orderReturn->refund_reference)
                        <p class="mb-1"><strong>{{ __('returns.fields.refund_reference') }}:</strong> {{ $orderReturn->refund_reference }}</p>
                    @endif

                    <p class="mb-0 fw-bold">
                        {{ __('returns.fields.total_refund') }}: {{ number_format($orderReturn->refund_amount, 2) }}
                    </p>
                </div>
            </div>
        </div>

        {{-- ===================== Actions ===================== --}}
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header fw-bold">{{ __('returns.sections.actions') }}</div>
                <div class="card-body">

                    @if(empty($next))
                        <small class="text-muted">{{ __('returns.actions.final') }}</small>
                    @endif

                    {{-- Approve --}}
                    @if(in_array('approved', $next, true))
                        <form method="POST" action="{{ route('admin.returns.status', $orderReturn) }}" class="mb-3">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="approved">
                            <textarea name="admin_note" rows="2" maxlength="1000" class="form-control mb-2"
                                      placeholder="{{ __('returns.fields.admin_note') }}"></textarea>
                            <button class="btn btn-primary">{{ __('returns.actions.approve') }}</button>
                        </form>
                    @endif

                    {{-- Receive: the admin chooses which pieces go back to the stock --}}
                    @if(in_array('received', $next, true))
                        <form method="POST" action="{{ route('admin.returns.status', $orderReturn) }}" class="mb-3">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="received">

                            <p class="text-muted mb-2">{{ __('returns.fields.restock_hint') }}</p>
                            @foreach($orderReturn->items as $returnItem)
                                <div class="form-check mb-1">
                                    <input type="checkbox" class="form-check-input" id="restock_{{ $returnItem->id }}"
                                           name="restock[{{ $returnItem->id }}]" value="1" checked>
                                    <label class="form-check-label" for="restock_{{ $returnItem->id }}">
                                        {{ $returnItem->orderItem?->product_name }}
                                        @if($returnItem->orderItem?->variant_label) ({{ $returnItem->orderItem->variant_label }}) @endif
                                        x {{ $returnItem->quantity }}
                                    </label>
                                </div>
                            @endforeach

                            <button class="btn btn-primary mt-2">{{ __('returns.actions.receive') }}</button>
                        </form>
                    @endif

                    {{-- Refund (needs the returns.refund permission) --}}
                    @if(in_array('refunded', $next, true))
                        @can('returns.refund')
                            <form method="POST" action="{{ route('admin.returns.status', $orderReturn) }}" class="mb-3"
                                  onsubmit="return confirm(@js(__('returns.actions.refund_confirm')));">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="refunded">

                                <div class="mb-2">
                                    <label class="form-label">
                                        {{ __('returns.fields.refund_amount') }}
                                        <small class="text-muted">({{ __('returns.fields.max_refund') }}: {{ number_format($orderReturn->refund_amount, 2) }})</small>
                                    </label>
                                    <input type="number" step="0.01" min="0" max="{{ $orderReturn->refund_amount }}"
                                           name="refund_amount" class="form-control"
                                           value="{{ old('refund_amount', $orderReturn->refund_amount) }}" required>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">{{ __('returns.fields.refund_reference') }}</label>
                                    <input type="text" name="refund_reference" maxlength="255" class="form-control"
                                           value="{{ old('refund_reference') }}">
                                    <small class="text-muted">{{ __('returns.fields.refund_reference_hint') }}</small>
                                </div>

                                <button class="btn btn-success">{{ __('returns.actions.refund') }}</button>
                            </form>
                        @endcan
                    @endif

                    {{-- Reject (the reason is required) --}}
                    @if(in_array('rejected', $next, true))
                        <form method="POST" action="{{ route('admin.returns.status', $orderReturn) }}"
                              onsubmit="return confirm(@js(__('returns.actions.reject_confirm')));">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="rejected">
                            <textarea name="admin_note" rows="2" maxlength="1000" class="form-control mb-2" required
                                      placeholder="{{ __('returns.fields.admin_note') }}"></textarea>
                            <button class="btn btn-outline-danger">{{ __('returns.actions.reject') }}</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== Items ===================== --}}
    <div class="card mb-3">
        <div class="card-header fw-bold">{{ __('returns.sections.items') }}</div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                <tr>
                    <th>{{ __('returns.columns.product') }}</th>
                    <th>{{ __('returns.columns.variant') }}</th>
                    <th>{{ __('returns.columns.sku') }}</th>
                    <th>{{ __('returns.columns.quantity') }}</th>
                    <th>{{ __('returns.columns.refund') }}</th>
                    @if($orderReturn->received_at)
                        <th>{{ __('returns.columns.restock') }}</th>
                    @endif
                </tr>
                </thead>
                <tbody>
                @foreach($orderReturn->items as $returnItem)
                    <tr>
                        <td>{{ $returnItem->orderItem?->product_name }}</td>
                        <td>{{ $returnItem->orderItem?->variant_label ?? '-' }}</td>
                        <td>{{ $returnItem->orderItem?->sku ?? '-' }}</td>
                        <td>{{ $returnItem->quantity }}</td>
                        <td>{{ number_format($returnItem->refund_amount, 2) }}</td>
                        @if($orderReturn->received_at)
                            <td>{{ $returnItem->restock ? __('returns.yes') : __('returns.no') }}</td>
                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
