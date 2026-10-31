{{--
    Same idea as the product variants form:
      - The page only collects rows: items[i][product_id], items[i][variant_id], items[i][quantity].
      - Prices shown here are for display only. The server calculates them again,
        together with the coupon discount and the blogger commission.
--}}
{{-- Change 'layouts.admin' / 'content' to your real admin layout names. --}}
@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">{{ __('orders.add') }}</h4>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">{{ __('orders.back') }}</a>
    </div>

    <form method="POST" action="{{ route('admin.orders.store') }}">
        @csrf

        {{-- ===================== Customer ===================== --}}
        <h5 class="mb-3">{{ __('orders.sections.customer') }}</h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="customer_name" class="form-label">{{ __('orders.fields.customer_name') }} <span class="text-danger">*</span></label>
                <input type="text" name="customer_name" id="customer_name"
                       class="form-control @error('customer_name') is-invalid @enderror"
                       value="{{ old('customer_name') }}" required>
                @error('customer_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="customer_phone" class="form-label">{{ __('orders.fields.customer_phone') }} <span class="text-danger">*</span></label>
                <input type="text" name="customer_phone" id="customer_phone"
                       class="form-control @error('customer_phone') is-invalid @enderror"
                       value="{{ old('customer_phone') }}" required>
                @error('customer_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="shipping_address" class="form-label">{{ __('orders.fields.shipping_address') }} <span class="text-danger">*</span></label>
                <textarea name="shipping_address" id="shipping_address" rows="3"
                          class="form-control @error('shipping_address') is-invalid @enderror" required>{{ old('shipping_address') }}</textarea>
                @error('shipping_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="notes" class="form-label">{{ __('orders.fields.notes') }}</label>
                <textarea name="notes" id="notes" rows="3"
                          class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        {{-- ===================== Items ===================== --}}
        <hr>
        <h5 class="mb-3">{{ __('orders.sections.items') }}</h5>

        @error('items') <div class="alert alert-danger">{{ $message }}</div> @enderror
        @foreach($errors->get('items.*') as $messages)
            @foreach($messages as $message)
                <div class="alert alert-danger">{{ $message }}</div>
            @endforeach
        @endforeach

        {{-- Filled by JS --}}
        <div id="items_container"></div>

        <button type="button" id="add_item" class="btn btn-outline-primary mb-4">
            {{ __('orders.fields.add_item') }}
        </button>

        {{-- ===================== Payment ===================== --}}
        <hr>
        <h5 class="mb-3">{{ __('orders.sections.payment') }}</h5>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="payment_method" class="form-label">{{ __('orders.fields.payment_method') }} <span class="text-danger">*</span></label>
                <select name="payment_method" id="payment_method" class="form-control @error('payment_method') is-invalid @enderror" required>
                    @foreach(\App\Models\Order::PAYMENT_METHODS as $method)
                        <option value="{{ $method }}" @selected(old('payment_method', 'cod') === $method)>
                            {{ __('orders.payment_methods.' . $method) }}
                        </option>
                    @endforeach
                </select>
                @error('payment_method') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Only for online payment (JS below) --}}
            <div class="col-md-4 mb-3" id="gateway_box">
                <label for="payment_gateway" class="form-label">{{ __('orders.fields.payment_gateway') }} <span class="text-danger">*</span></label>
                <select name="payment_gateway" id="payment_gateway" class="form-control @error('payment_gateway') is-invalid @enderror">
                    <option value="">{{ __('orders.fields.select_gateway') }}</option>
                    @foreach($gateways as $key => $label)
                        <option value="{{ $key }}" @selected(old('payment_gateway') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('payment_gateway') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        {{-- ===================== Summary ===================== --}}
        <hr>
        <h5 class="mb-3">{{ __('orders.sections.summary') }}</h5>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="coupon_code" class="form-label">{{ __('orders.fields.coupon_code') }}</label>
                <input type="text" name="coupon_code" id="coupon_code" maxlength="50"
                       class="form-control text-uppercase @error('coupon_code') is-invalid @enderror"
                       value="{{ old('coupon_code') }}">
                @error('coupon_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <small class="text-muted">{{ __('orders.fields.coupon_hint') }}</small>
            </div>

            <div class="col-md-4 mb-3">
                <label for="shipping_total" class="form-label">{{ __('orders.fields.shipping_total') }}</label>
                <input type="number" step="0.01" min="0" name="shipping_total" id="shipping_total"
                       class="form-control @error('shipping_total') is-invalid @enderror"
                       value="{{ old('shipping_total', 0) }}">
                @error('shipping_total') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('orders.columns.total') }}</label>
                <div class="form-control-plaintext fw-bold" id="order_total">0.00</div>
            </div>
        </div>

        <button type="submit" class="btn btn-success">{{ __('orders.save') }}</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rowsBox = document.getElementById('items_container');
        const shippingInput = document.getElementById('shipping_total');
        const totalBox = document.getElementById('order_total');
        const methodSelect = document.getElementById('payment_method');
        const gatewayBox = document.getElementById('gateway_box');
        const gatewaySelect = document.getElementById('payment_gateway');

        // From the controller. Used only to fill the selects and show prices.
        const products = @json($products);

        // Rows sent back after a validation error.
        const oldItems = @json(old('items', []));

        // Texts in the current language (lang/xx/orders.php => 'js').
        const t = @json(__('orders.js'));

        // Only goes up, so every row keeps a unique name even after removing rows.
        let nextIndex = 0;

        const findProduct = (id) => products.find((product) => String(product.id) === String(id));
        const money = (number) => Number(number || 0).toFixed(2);

        function makeOption(value, label) {
            const option = document.createElement('option');
            option.value = value;
            option.textContent = label;   // textContent: names can never inject HTML
            return option;
        }

        /* ---------------- Payment ---------------- */

        // The gateway is needed only for online payment.
        // A disabled select is not submitted and is skipped by the browser validation.
        function toggleGateway() {
            const isOnline = methodSelect.value === 'online';

            gatewayBox.style.display = isOnline ? '' : 'none';
            gatewaySelect.disabled = !isOnline;
            gatewaySelect.required = isOnline;
        }

        /* ---------------- One row ---------------- */

        function addRow(item = {}) {
            const index = nextIndex++;

            const row = document.createElement('div');
            row.className = 'card mb-3 order-item';
            row.innerHTML = `
                <div class="card-body row align-items-end">
                    <div class="col-md-3 mb-2">
                        <label class="form-label">${t.product}</label>
                        <select name="items[${index}][product_id]" class="form-control i-product" required></select>
                    </div>
                    <div class="col-md-3 mb-2 i-variant-box">
                        <label class="form-label">${t.variant}</label>
                        <select name="items[${index}][variant_id]" class="form-control i-variant"></select>
                    </div>
                    <div class="col-md-1 mb-2">
                        <label class="form-label">${t.quantity}</label>
                        <input type="number" min="1" name="items[${index}][quantity]" class="form-control i-quantity" required>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="form-label">${t.unit_price}</label>
                        <div class="form-control-plaintext i-price">0.00</div>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="form-label">${t.line_total}</label>
                        <div class="form-control-plaintext i-line">0.00</div>
                    </div>
                    <div class="col-md-1 mb-2">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-item">${t.remove}</button>
                    </div>
                </div>
            `;

            const productSelect = row.querySelector('.i-product');
            productSelect.appendChild(makeOption('', t.select_product));
            products.forEach((product) => productSelect.appendChild(makeOption(product.id, product.name)));

            // Values are set from JS so old input can never break the markup.
            productSelect.value = item.product_id ?? '';
            row.querySelector('.i-quantity').value = item.quantity ?? 1;

            rowsBox.appendChild(row);
            refreshRow(row, item.variant_id ?? '');
        }

        // Rebuild the variant select of a row, show or hide it, and update the prices.
        function refreshRow(row, selectedVariantId) {
            const product = findProduct(row.querySelector('.i-product').value);
            const variantBox = row.querySelector('.i-variant-box');
            const variantSelect = row.querySelector('.i-variant');

            variantSelect.innerHTML = '';

            if (product && product.has_variants) {
                variantSelect.appendChild(makeOption('', t.select_variant));
                product.variants.forEach((variant) => variantSelect.appendChild(makeOption(variant.id, variant.label)));
                variantSelect.value = String(selectedVariantId ?? '');
            }

            const needsVariant = Boolean(product && product.has_variants);
            variantBox.style.display = needsVariant ? '' : 'none';
            variantSelect.disabled = !needsVariant;
            variantSelect.required = needsVariant;

            updateRow(row);
        }

        function unitPriceOf(row) {
            const product = findProduct(row.querySelector('.i-product').value);

            if (!product) {
                return 0;
            }

            if (product.has_variants) {
                const variant = product.variants.find((v) => String(v.id) === row.querySelector('.i-variant').value);
                return variant ? variant.price : 0;
            }

            return product.price;
        }

        function updateRow(row) {
            const unit = unitPriceOf(row);
            const quantity = Number(row.querySelector('.i-quantity').value) || 0;

            row.querySelector('.i-price').textContent = money(unit);
            row.querySelector('.i-line').textContent = money(unit * quantity);

            updateTotal();
        }

        // Total before the coupon discount (the server applies the coupon when saving).
        function updateTotal() {
            let total = Number(shippingInput.value) || 0;

            rowsBox.querySelectorAll('.order-item').forEach(function (row) {
                total += unitPriceOf(row) * (Number(row.querySelector('.i-quantity').value) || 0);
            });

            totalBox.textContent = money(total);
        }

        /* ---------------- Events ---------------- */

        document.getElementById('add_item').addEventListener('click', () => addRow());
        shippingInput.addEventListener('input', updateTotal);
        methodSelect.addEventListener('change', toggleGateway);

        // The rows are created by JS, so the listeners are on the container (event delegation).
        rowsBox.addEventListener('change', function (event) {
            const row = event.target.closest('.order-item');

            if (!row) {
                return;
            }

            if (event.target.classList.contains('i-product')) {
                refreshRow(row, '');
            } else if (event.target.classList.contains('i-variant')) {
                updateRow(row);
            }
        });

        rowsBox.addEventListener('input', function (event) {
            if (event.target.classList.contains('i-quantity')) {
                updateRow(event.target.closest('.order-item'));
            }
        });

        rowsBox.addEventListener('click', function (event) {
            if (!event.target.classList.contains('remove-item')) {
                return;
            }

            event.target.closest('.order-item').remove();
            updateTotal();
        });

        /* ---------------- Start ---------------- */

        // Old input can be an object with gaps in its keys, so Object.values is used.
        const startRows = Object.values(oldItems);

        if (startRows.length > 0) {
            startRows.forEach((item) => addRow(item));
        } else {
            addRow();
        }

        updateTotal();
        toggleGateway();
    });
</script>
@endsection
