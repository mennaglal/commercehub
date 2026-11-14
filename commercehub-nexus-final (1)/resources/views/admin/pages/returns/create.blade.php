@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">{{ __('returns.create') }}: {{ $order->order_number }}</h4>
        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-outline-secondary">{{ __('returns.back_to_order') }}</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $message)
                <div>{{ $message }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('admin.orders.returns.store', $order) }}">
        @csrf

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
                        <th>{{ __('returns.columns.ordered') }}</th>
                        <th>{{ __('returns.columns.remaining') }}</th>
                        <th>{{ __('returns.columns.unit_refund') }}</th>
                        <th style="width: 140px">{{ __('returns.columns.return_quantity') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($lines as $line)
                        <tr>
                            <td>{{ $line->item->product_name }}</td>
                            <td>{{ $line->item->variant_label ?? '-' }}</td>
                            <td>{{ $line->item->sku ?? '-' }}</td>
                            <td>{{ $line->item->quantity }}</td>
                            <td>{{ $line->remaining }}</td>
                            <td>{{ number_format($line->unit_refund, 2) }}</td>
                            <td>
                                {{-- items[order_item_id] = quantity. Nothing left to return => the field is disabled. --}}
                                <input type="number" min="0" max="{{ $line->remaining }}"
                                       name="items[{{ $line->item->id }}]"
                                       value="{{ old('items.' . $line->item->id, 0) }}"
                                       class="form-control" @disabled($line->remaining < 1)>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer text-muted">
                {{ __('returns.fields.items_hint') }} {{ __('returns.fields.shipping_note') }}
            </div>
        </div>

        {{-- ===================== Reason ===================== --}}
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="reason" class="form-label">{{ __('returns.fields.reason') }} <span class="text-danger">*</span></label>
                <select name="reason" id="reason" class="form-control @error('reason') is-invalid @enderror" required>
                    @foreach(\App\Models\OrderReturn::REASONS as $reason)
                        <option value="{{ $reason }}" @selected(old('reason') === $reason)>{{ __('returns.reasons.' . $reason) }}</option>
                    @endforeach
                </select>
                @error('reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-8 mb-3">
                <label for="customer_note" class="form-label">{{ __('returns.fields.customer_note') }}</label>
                <textarea name="customer_note" id="customer_note" rows="3" maxlength="1000"
                          class="form-control @error('customer_note') is-invalid @enderror">{{ old('customer_note') }}</textarea>
                @error('customer_note') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <button type="submit" class="btn btn-success">{{ __('returns.save') }}</button>
    </form>
</div>
@endsection
