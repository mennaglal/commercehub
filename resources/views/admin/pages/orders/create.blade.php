@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">{{ __('orders.add') }}</h4>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
                {{ __('orders.back') }}
            </a>
        </div>

        <form method="POST" action="{{ route('admin.orders.store') }}">
            @csrf

            {{-- ===================== Customer ===================== --}}
            <h5 class="mb-3">{{ __('orders.sections.customer') }}</h5>

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label for="customer_name" class="form-label">
                        {{ __('orders.fields.customer_name') }} <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        name="customer_name"
                        id="customer_name"
                        class="form-control @error('customer_name') is-invalid @enderror"
                        value="{{ old('customer_name') }}"
                        required
                    >
                    @error('customer_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="customer_phone" class="form-label">
                        {{ __('orders.fields.customer_phone') }} <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        name="customer_phone"
                        id="customer_phone"
                        class="form-control @error('customer_phone') is-invalid @enderror"
                        value="{{ old('customer_phone') }}"
                        required
                    >
                    @error('customer_phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Country --}}
                <div class="col-md-4 mb-3">
                    <label for="country_id" class="form-label">
                        {{ app()->getLocale() === 'ar' ? 'الدولة' : 'Country' }}
                        <span class="text-danger">*</span>
                    </label>

                    <select
                        name="country_id"
                        id="country_id"
                        class="form-control @error('country_id') is-invalid @enderror"
                        required
                    >
                        <option value="">
                            {{ app()->getLocale() === 'ar' ? 'اختر الدولة' : 'Select country' }}
                        </option>

                        @foreach($countries as $country)
                            <option
                                value="{{ $country->id }}"
                                @selected((string) old('country_id') === (string) $country->id)
                            >
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('country_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- City / Region --}}
                <div class="col-md-4 mb-3">
                    <label for="region_id" class="form-label">
                        {{ app()->getLocale() === 'ar' ? 'المدينة' : 'City' }}
                        <span class="text-danger">*</span>
                    </label>

                    <select
                        name="region_id"
                        id="region_id"
                        class="form-control @error('region_id') is-invalid @enderror"
                        required
                        disabled
                    >
                        <option value="">
                            {{ app()->getLocale() === 'ar' ? 'اختر المدينة' : 'Select city' }}
                        </option>
                    </select>

                    @error('region_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Area --}}
                <div class="col-md-4 mb-3">
                    <label for="area_id" class="form-label">
                        {{ app()->getLocale() === 'ar' ? 'المنطقة / الحي' : 'Area / District' }}
                        <span class="text-danger">*</span>
                    </label>

                    <select
                        name="area_id"
                        id="area_id"
                        class="form-control @error('area_id') is-invalid @enderror"
                        required
                        disabled
                    >
                        <option value="">
                            {{ app()->getLocale() === 'ar' ? 'اختر المنطقة' : 'Select area' }}
                        </option>
                    </select>

                    @error('area_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="shipping_address" class="form-label">
                        {{ __('orders.fields.shipping_address') }}
                        <span class="text-danger">*</span>
                    </label>

                    <textarea
                        name="shipping_address"
                        id="shipping_address"
                        rows="3"
                        class="form-control @error('shipping_address') is-invalid @enderror"
                        required
                    >{{ old('shipping_address') }}</textarea>

                    @error('shipping_address')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="shipping_notes" class="form-label">
                        {{ app()->getLocale() === 'ar' ? 'تعليمات التوصيل' : 'Delivery instructions' }}
                    </label>

                    <textarea
                        name="shipping_notes"
                        id="shipping_notes"
                        rows="3"
                        maxlength="1000"
                        class="form-control"
                    >{{ old('shipping_notes') }}</textarea>

                    @error('shipping_notes')
                    <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="notes" class="form-label">
                        {{ __('orders.fields.notes') }}
                    </label>

                    <textarea
                        name="notes"
                        id="notes"
                        rows="3"
                        class="form-control @error('notes') is-invalid @enderror"
                    >{{ old('notes') }}</textarea>

                    @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

            </div>

            {{-- ===================== Items ===================== --}}
            <hr>

            <h5 class="mb-3">{{ __('orders.sections.items') }}</h5>

            @error('items')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror

            @foreach($errors->get('items.*') as $messages)
                @foreach($messages as $message)
                    <div class="alert alert-danger">{{ $message }}</div>
                @endforeach
            @endforeach

            <div id="items_container"></div>

            <button type="button" id="add_item" class="btn btn-outline-primary mb-4">
                {{ __('orders.fields.add_item') }}
            </button>

            {{-- ===================== Payment ===================== --}}
            <hr>

            <h5 class="mb-3">{{ __('orders.sections.payment') }}</h5>

            <div class="row">

                <div class="col-md-4 mb-3">
                    <label for="payment_method" class="form-label">
                        {{ __('orders.fields.payment_method') }}
                        <span class="text-danger">*</span>
                    </label>

                    <select
                        name="payment_method"
                        id="payment_method"
                        class="form-control @error('payment_method') is-invalid @enderror"
                        required
                    >
                        @foreach(\App\Models\Order::PAYMENT_METHODS as $method)
                            <option
                                value="{{ $method }}"
                                @selected(old('payment_method', 'cod') === $method)
                            >
                                {{ __('orders.payment_methods.' . $method) }}
                            </option>
                        @endforeach
                    </select>

                    @error('payment_method')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3" id="gateway_box">
                    <label for="payment_gateway" class="form-label">
                        {{ __('orders.fields.payment_gateway') }}
                        <span class="text-danger">*</span>
                    </label>

                    <select
                        name="payment_gateway"
                        id="payment_gateway"
                        class="form-control @error('payment_gateway') is-invalid @enderror"
                    >
                        <option value="">{{ __('orders.fields.select_gateway') }}</option>

                        @foreach($gateways as $key => $label)
                            <option
                                value="{{ $key }}"
                                @selected(old('payment_gateway') === $key)
                            >
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>

                    @error('payment_gateway')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

            </div>

            {{-- ===================== Summary ===================== --}}
            <hr>

            <h5 class="mb-3">{{ __('orders.sections.summary') }}</h5>

            <div class="row">

                <div class="col-md-4 mb-3">
                    <label for="coupon_code" class="form-label">
                        {{ __('orders.fields.coupon_code') }}
                    </label>

                    <input
                        type="text"
                        name="coupon_code"
                        id="coupon_code"
                        maxlength="50"
                        class="form-control text-uppercase @error('coupon_code') is-invalid @enderror"
                        value="{{ old('coupon_code') }}"
                    >

                    @error('coupon_code')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <small class="text-muted">{{ __('orders.fields.coupon_hint') }}</small>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">
                        {{ __('orders.fields.shipping_total') }}
                    </label>

                    <div class="form-control-plaintext fw-bold" id="shipping_total_display">
                        0.00
                    </div>

                    <small class="text-muted">
                        {{ app()->getLocale() === 'ar'
                            ? 'يتم تحديد سعر الشحن تلقائياً حسب المنطقة المختارة'
                            : 'Shipping is calculated automatically based on the selected area.' }}
                    </small>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">{{ __('orders.columns.total') }}</label>
                    <div class="form-control-plaintext fw-bold" id="order_total">0.00</div>
                </div>

            </div>

            <button type="submit" class="btn btn-success">
                {{ __('orders.save') }}
            </button>

        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const rowsBox = document.getElementById('items_container');
            const totalBox = document.getElementById('order_total');
            const shippingDisplay = document.getElementById('shipping_total_display');

            const countrySelect = document.getElementById('country_id');
            const regionSelect = document.getElementById('region_id');
            const areaSelect = document.getElementById('area_id');

            const methodSelect = document.getElementById('payment_method');
            const gatewayBox = document.getElementById('gateway_box');
            const gatewaySelect = document.getElementById('payment_gateway');

            const products = @json($products);
            const cities = @json($cities->map(fn ($city) => [
        'id' => $city->id,
        'country_id' => $city->country_id,
        'name' => $city->name,
    ])->values());

            const areas = @json($areas->map(fn ($area) => [
        'id' => $area->id,
        'city_id' => $area->city_id,
        'name' => $area->name,
        'shipping_price' => (float) $area->shipping_price,
    ])->values());

            const oldItems = @json(old('items', []));
            const oldCountryId = @json(old('country_id'));
            const oldRegionId = @json(old('region_id'));
            const oldAreaId = @json(old('area_id'));

            const t = @json(__('orders.js'));

            let nextIndex = 0;

            const findProduct = (id) =>
                products.find(product => String(product.id) === String(id));

            const money = number => Number(number || 0).toFixed(2);

            function makeOption(value, label) {
                const option = document.createElement('option');
                option.value = value;
                option.textContent = label;
                return option;
            }

            function resetSelect(select, placeholder) {
                select.innerHTML = '';
                select.appendChild(makeOption('', placeholder));
            }

            function selectedShippingPrice() {
                const area = areas.find(item => String(item.id) === String(areaSelect.value));
                return area ? Number(area.shipping_price || 0) : 0;
            }

            function updateShippingAndTotal() {
                const shipping = selectedShippingPrice();

                shippingDisplay.textContent = money(shipping);

                let total = shipping;

                rowsBox.querySelectorAll('.order-item').forEach(row => {
                    total += unitPriceOf(row) *
                        (Number(row.querySelector('.i-quantity').value) || 0);
                });

                totalBox.textContent = money(total);
            }

            /* ===================== Locations ===================== */

            function fillCities(countryId, selectedCityId = '') {
                resetSelect(
                    regionSelect,
                    @json(app()->getLocale() === 'ar' ? 'اختر المدينة' : 'Select city')
                );

                resetSelect(
                    areaSelect,
                    @json(app()->getLocale() === 'ar' ? 'اختر المنطقة' : 'Select area')
                );

                regionSelect.disabled = true;
                areaSelect.disabled = true;

                if (!countryId) {
                    updateShippingAndTotal();
                    return;
                }

                const filteredCities = cities.filter(city =>
                    String(city.country_id) === String(countryId)
                );

                filteredCities.forEach(city => {
                    regionSelect.appendChild(makeOption(city.id, city.name));
                });

                regionSelect.disabled = filteredCities.length === 0;

                if (selectedCityId) {
                    regionSelect.value = String(selectedCityId);
                }

                if (regionSelect.value) {
                    fillAreas(regionSelect.value, oldAreaId || '');
                }

                updateShippingAndTotal();
            }

            function fillAreas(cityId, selectedAreaId = '') {
                resetSelect(
                    areaSelect,
                    @json(app()->getLocale() === 'ar' ? 'اختر المنطقة' : 'Select area')
                );

                areaSelect.disabled = true;

                if (!cityId) {
                    updateShippingAndTotal();
                    return;
                }

                const filteredAreas = areas.filter(area =>
                    String(area.city_id) === String(cityId)
                );

                filteredAreas.forEach(area => {
                    const label = `${area.name} - ${money(area.shipping_price)}`;
                    areaSelect.appendChild(makeOption(area.id, label));
                });

                areaSelect.disabled = filteredAreas.length === 0;

                if (selectedAreaId) {
                    areaSelect.value = String(selectedAreaId);
                }

                updateShippingAndTotal();
            }

            countrySelect.addEventListener('change', function () {
                fillCities(this.value);
            });

            regionSelect.addEventListener('change', function () {
                fillAreas(this.value);
            });

            areaSelect.addEventListener('change', updateShippingAndTotal);

            /* ===================== Payment ===================== */

            function toggleGateway() {
                const isOnline = methodSelect.value === 'online';

                gatewayBox.style.display = isOnline ? '' : 'none';
                gatewaySelect.disabled = !isOnline;
                gatewaySelect.required = isOnline;
            }

            methodSelect.addEventListener('change', toggleGateway);

            /* ===================== Items ===================== */

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
                    <button type="button" class="btn btn-sm btn-outline-danger remove-item">
                        ${t.remove}
                    </button>
                </div>

            </div>
        `;

                const productSelect = row.querySelector('.i-product');

                productSelect.appendChild(makeOption('', t.select_product));

                products.forEach(product => {
                    productSelect.appendChild(makeOption(product.id, product.name));
                });

                productSelect.value = item.product_id ?? '';
                row.querySelector('.i-quantity').value = item.quantity ?? 1;

                rowsBox.appendChild(row);

                refreshRow(row, item.variant_id ?? '');
            }

            function refreshRow(row, selectedVariantId = '') {
                const product = findProduct(row.querySelector('.i-product').value);
                const variantBox = row.querySelector('.i-variant-box');
                const variantSelect = row.querySelector('.i-variant');

                variantSelect.innerHTML = '';

                if (product && product.has_variants) {
                    variantSelect.appendChild(makeOption('', t.select_variant));

                    product.variants.forEach(variant => {
                        variantSelect.appendChild(
                            makeOption(variant.id, variant.label)
                        );
                    });

                    variantSelect.value = String(selectedVariantId);
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
                    const variant = product.variants.find(v =>
                        String(v.id) === String(row.querySelector('.i-variant').value)
                    );

                    return variant ? Number(variant.price) : 0;
                }

                return Number(product.price || 0);
            }

            function updateRow(row) {
                const unit = unitPriceOf(row);
                const quantity = Number(row.querySelector('.i-quantity').value) || 0;

                row.querySelector('.i-price').textContent = money(unit);
                row.querySelector('.i-line').textContent = money(unit * quantity);

                updateShippingAndTotal();
            }

            document.getElementById('add_item').addEventListener('click', () => addRow());

            rowsBox.addEventListener('change', function (event) {
                const row = event.target.closest('.order-item');

                if (!row) {
                    return;
                }

                if (event.target.classList.contains('i-product')) {
                    refreshRow(row);
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
                updateShippingAndTotal();
            });

            /* ===================== Initial State ===================== */

            if (oldCountryId) {
                countrySelect.value = String(oldCountryId);
                fillCities(oldCountryId, oldRegionId || '');
            }

            const startRows = Object.values(oldItems);

            if (startRows.length) {
                startRows.forEach(item => addRow(item));
            } else {
                addRow();
            }

            updateShippingAndTotal();
            toggleGateway();

        });
    </script>
@endsection
