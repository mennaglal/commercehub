<div class="form-row">
    <div class="form-group col-md-4">
        <label>{{ __('dashboard.Country') }}</label>

        <select name="country_id" id="vendorCountry" class="form-control">
            <option value="">-</option>

            @foreach($countries as $country)
                <option
                    value="{{ $country->id }}"
                    @selected((string) old('country_id', $selectedCountry ?? '') === (string) $country->id)
                >
                    {{ app()->getLocale() === 'ar' ? $country->name_ar : $country->name_en }}
                </option>
            @endforeach
        </select>

        @error('country_id')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group col-md-4">
        <label>{{ __('dashboard.City') }}</label>

        <select name="city_id" id="vendorCity" class="form-control">
            <option value="">-</option>

            @foreach($cities as $city)
                <option
                    value="{{ $city->id }}"
                    data-country="{{ $city->country_id }}"
                    @selected((string) old('city_id', $selectedCity ?? '') === (string) $city->id)
                >
                    {{ app()->getLocale() === 'ar' ? $city->name_ar : $city->name_en }}
                </option>
            @endforeach
        </select>

        @error('city_id')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group col-md-4">
        <label>{{ __('dashboard.Area') }}</label>

        <select name="area_id" id="vendorArea" class="form-control">
            <option value="">-</option>

            @foreach($areas as $area)
                <option
                    value="{{ $area->id }}"
                    data-city="{{ $area->city_id }}"
                    @selected((string) old('area_id', $selectedArea ?? '') === (string) $area->id)
                >
                    {{ app()->getLocale() === 'ar' ? $area->name_ar : $area->name_en }}
                </option>
            @endforeach
        </select>

        @error('area_id')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const countrySelect = document.getElementById('vendorCountry');
        const citySelect = document.getElementById('vendorCity');
        const areaSelect = document.getElementById('vendorArea');

        function filterCities(resetCity = true) {
            const countryId = countrySelect.value;

            Array.from(citySelect.options).forEach(function (option) {
                if (!option.value) {
                    option.hidden = false;
                    return;
                }

                option.hidden = option.dataset.country !== countryId;
            });

            if (resetCity) {
                citySelect.value = '';
                filterAreas(true);
            }
        }

        function filterAreas(resetArea = true) {
            const cityId = citySelect.value;

            Array.from(areaSelect.options).forEach(function (option) {
                if (!option.value) {
                    option.hidden = false;
                    return;
                }

                option.hidden = option.dataset.city !== cityId;
            });

            if (resetArea) {
                areaSelect.value = '';
            }
        }

        countrySelect.addEventListener('change', function () {
            filterCities(true);
        });

        citySelect.addEventListener('change', function () {
            filterAreas(true);
        });

        filterCities(false);
        filterAreas(false);
    });
</script>
