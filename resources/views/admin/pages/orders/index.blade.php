@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">{{ __('orders.title') }}</h4>
            <a href="{{ route('admin.orders.create') }}" class="btn btn-primary">
                {{ __('orders.add') }}
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Filters --}}
        <form method="GET" class="row mb-3">

            <div class="col-md-3 mb-2">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="form-control"
                    placeholder="{{ __('orders.search') }}"
                >
            </div>

            <div class="col-md-2 mb-2">
                <select name="status" class="form-control">
                    <option value="">
                        {{ __('orders.columns.status') }}: {{ __('orders.all') }}
                    </option>
                    @foreach(\App\Models\Order::STATUSES as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>
                            {{ __('orders.statuses.' . $status) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2 mb-2">
                <select name="payment_status" class="form-control">
                    <option value="">
                        {{ __('orders.columns.payment') }}: {{ __('orders.all') }}
                    </option>
                    @foreach(\App\Models\Order::PAYMENT_STATUSES as $status)
                        <option value="{{ $status }}" @selected(request('payment_status') === $status)>
                            {{ __('orders.payment_statuses.' . $status) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2 mb-2">
                <select name="delivery_status" class="form-control">
                    <option value="">
                        {{ __('orders.columns.delivery') }}: {{ __('orders.all') }}
                    </option>
                    @foreach(\App\Models\Order::DELIVERY_STATUSES as $status)
                        <option value="{{ $status }}" @selected(request('delivery_status') === $status)>
                            {{ __('orders.delivery_statuses.' . $status) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Country --}}
            <div class="col-md-3 mb-2">
                <select name="country_id" id="country_id" class="form-control">
                    <option value="">
                        {{ app()->getLocale() === 'ar' ? 'كل الدول' : 'All Countries' }}
                    </option>

                    @foreach($countries as $country)
                        <option
                            value="{{ $country->id }}"
                            @selected((string) request('country_id') === (string) $country->id)
                        >
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- City / Region --}}
            <div class="col-md-3 mb-2">
                <select
                    name="region_id"
                    id="region_id"
                    class="form-control"
                    {{ request('country_id') ? '' : 'disabled' }}
                >
                    <option value="">
                        {{ app()->getLocale() === 'ar' ? 'كل المدن' : 'All Cities' }}
                    </option>
                </select>
            </div>

            {{-- Area --}}
            <div class="col-md-3 mb-2">
                <select
                    name="area_id"
                    id="area_id"
                    class="form-control"
                    {{ request('region_id') ? '' : 'disabled' }}
                >
                    <option value="">
                        {{ app()->getLocale() === 'ar' ? 'كل المناطق' : 'All Areas' }}
                    </option>
                </select>
            </div>

            {{-- Shipping Price --}}
            <div class="col-md-3 mb-2">
                <div class="form-control-plaintext fw-bold">
                    {{ app()->getLocale() === 'ar' ? 'سعر الشحن:' : 'Shipping Price:' }}
                    <span id="shipping_price_display">0.00</span>
                </div>
            </div>

            <div class="col-md-3 mb-2">
                <button class="btn btn-outline-primary w-100">
                    {{ __('orders.filter') }}
                </button>
            </div>

            <div class="col-md-3 mb-2">
                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary w-100">
                    {{ app()->getLocale() === 'ar' ? 'إعادة تعيين الفلاتر' : 'Reset Filters' }}
                </a>
            </div>

        </form>

        {{-- Orders Table --}}
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

                            <td>
                                {{ $order->customer_name }}
                                <br>
                                <small class="text-muted">{{ $order->customer_phone }}</small>
                            </td>

                            <td>
                                {{ $order->items->pluck('store_name')->filter()->unique()->implode(', ') ?: '-' }}
                            </td>

                            <td>{{ $order->items_count }}</td>

                            <td>{{ number_format($order->total, 2) }}</td>

                            <td>
                                <span class="badge bg-{{ $order->badge('status') }}">
                                    {{ __('orders.statuses.' . $order->status) }}
                                </span>
                            </td>

                            <td>
                                <span class="badge bg-{{ $order->badge('payment_status') }}">
                                    {{ __('orders.payment_statuses.' . $order->payment_status) }}
                                </span>
                                <br>
                                <small class="text-muted">
                                    {{ __('orders.payment_methods.' . $order->payment_method) }}
                                </small>
                            </td>

                            <td>
                                <span class="badge bg-{{ $order->badge('delivery_status') }}">
                                    {{ __('orders.delivery_statuses.' . $order->delivery_status) }}
                                </span>
                            </td>

                            <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>

                            <td>
                                <a
                                    href="{{ route('admin.orders.show', $order) }}"
                                    class="btn btn-sm btn-outline-secondary"
                                >
                                    {{ __('orders.fields.view') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                {{ __('orders.empty') }}
                            </td>
                        </tr>
                    @endforelse
                    </tbody>

                </table>
            </div>
        </div>

        <div class="mt-3">
            {{ $orders->appends(request()->query())->links() }}
        </div>

    </div>
    @php
        $citiesData = $cities->map(function ($city) {
            return [
                'id' => $city->id,
                'country_id' => $city->country_id,
                'name' => app()->getLocale() === 'ar'
                    ? $city->name_ar
                    : $city->name_en,
            ];
        })->values();

        $areasData = $areas->map(function ($area) {
            return [
                'id' => $area->id,
                'city_id' => $area->city_id,
                'name' => app()->getLocale() === 'ar'
                    ? $area->name_ar
                    : $area->name_en,
                'shipping_price' => (float) $area->shipping_price,
            ];
        })->values();
    @endphp
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const countrySelect = document.getElementById('country_id');
            const regionSelect = document.getElementById('region_id');
            const areaSelect = document.getElementById('area_id');
            const shippingDisplay = document.getElementById('shipping_price_display');

            const cities = @json($citiesData);
            const areas = @json($areasData);

            const oldCountryId = @json((string) request('country_id'));
            const oldRegionId = @json((string) request('region_id'));
            const oldAreaId = @json((string) request('area_id'));

            const isArabic = @json(app()->getLocale() === 'ar');

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

            function updateShippingPrice() {
                const area = areas.find(item =>
                    String(item.id) === String(areaSelect.value)
                );

                shippingDisplay.textContent = money(
                    area ? area.shipping_price : 0
                );
            }

            function fillCities(countryId, selectedCityId = '') {

                resetSelect(
                    regionSelect,
                    isArabic ? 'كل المدن' : 'All Cities'
                );

                resetSelect(
                    areaSelect,
                    isArabic ? 'كل المناطق' : 'All Areas'
                );

                regionSelect.disabled = true;
                areaSelect.disabled = true;

                if (!countryId) {
                    updateShippingPrice();
                    return;
                }

                const filteredCities = cities.filter(city =>
                    String(city.country_id) === String(countryId)
                );

                filteredCities.forEach(city => {
                    regionSelect.appendChild(
                        makeOption(city.id, city.name)
                    );
                });

                regionSelect.disabled = filteredCities.length === 0;

                if (selectedCityId) {
                    regionSelect.value = String(selectedCityId);
                }

                if (regionSelect.value) {
                    fillAreas(regionSelect.value, oldAreaId);
                }

                updateShippingPrice();
            }

            function fillAreas(cityId, selectedAreaId = '') {

                resetSelect(
                    areaSelect,
                    isArabic ? 'كل المناطق' : 'All Areas'
                );

                areaSelect.disabled = true;

                if (!cityId) {
                    updateShippingPrice();
                    return;
                }

                const filteredAreas = areas.filter(area =>
                    String(area.city_id) === String(cityId)
                );

                filteredAreas.forEach(area => {

                    const label = `${area.name} - ${money(area.shipping_price)}`;

                    areaSelect.appendChild(
                        makeOption(area.id, label)
                    );
                });

                areaSelect.disabled = filteredAreas.length === 0;

                if (selectedAreaId) {
                    areaSelect.value = String(selectedAreaId);
                }

                updateShippingPrice();
            }

            countrySelect.addEventListener('change', function () {
                fillCities(this.value);
            });

            regionSelect.addEventListener('change', function () {
                fillAreas(this.value);
            });

            areaSelect.addEventListener('change', updateShippingPrice);

            // Restore selected filters after page reload.
            if (oldCountryId) {
                countrySelect.value = oldCountryId;
                fillCities(oldCountryId, oldRegionId);
            }

            updateShippingPrice();

        });
    </script>
@endsection
