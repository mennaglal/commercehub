
@extends('admin.layouts.app')

@section('content')
    @include('includes.error')
    @include('includes.success')

    <div class="card shadow mb-4">
        <div class="card-header">
            <h5 class="mb-0">{{ __('dashboard.users_vendors.add_user') }}</h5>
        </div>

        <div class="card-body">
            <form method="POST"
                  action="{{ route('admin.users.store') }}"
                  enctype="multipart/form-data">
                @csrf

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>{{ __('dashboard.Name') }}</label>
                        <input class="form-control" type="text" name="name"
                               value="{{ old('name') }}" required>
                    </div>

                    <div class="form-group col-md-6">
                        <label>{{ __('dashboard.users_vendors.email') }}</label>
                        <input class="form-control" type="email" name="email"
                               value="{{ old('email') }}" required>
                    </div>

                    <div class="form-group col-md-6">
                        <label>{{ __('dashboard.users_vendors.phone') }}</label>
                        <input class="form-control" name="phone"
                               value="{{ old('phone') }}">
                    </div>

                    <div class="form-group col-md-6">
                        <label>{{ __('dashboard.users_vendors.password') }}</label>
                        <input class="form-control" type="password" name="password"
                               required autocomplete="new-password">
                    </div>

                    <div class="form-group col-md-6">
                        <label>{{ __('dashboard.users_vendors.password_confirmation') }}</label>
                        <input class="form-control" type="password"
                               name="password_confirmation" required
                               autocomplete="new-password">
                    </div>

                    <div class="form-group col-md-6">
                        <label>{{ __('dashboard.Country') }}</label>
                        <select class="form-control" name="country_id" id="country_id">
                            <option value="">{{ __('dashboard.Select country') }}</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}"
                                    @selected(old('country_id') == $country->id)>
                                    {{ app()->getLocale() === 'ar' ? $country->name_ar : $country->name_en }}                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label>{{ __('dashboard.City') }}</label>
                        <select class="form-control" name="city_id" id="city_id">
                            <option value="">{{ __('dashboard.Select city') }}</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}"
                                        data-country="{{ $city->country_id }}"
                                    @selected(old('city_id') == $city->id)>
                                    {{ app()->getLocale() === 'ar' ? $city->name_ar : $city->name_en }}                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label>{{ __('dashboard.Area') }}</label>
                        <select class="form-control" name="area_id" id="area_id">
                            <option value="">{{ __('dashboard.select_area') }}</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}"
                                        data-city="{{ $area->city_id }}"
                                    @selected(old('area_id') == $area->id)>
                                    {{ app()->getLocale() === 'ar' ? $area->name_ar : $area->name_en }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label>{{ __('dashboard.Image') }}</label>
                        <input type="file" name="avatar" id="avatar"
                               class="form-control" accept="image/*">

                        <div id="avatarPreviewContainer" class="mt-3" style="display:none;">
                            <img id="avatarPreview" src="" alt="Image Preview"
                                 width="200" height="200" class="rounded"
                                 style="object-fit:cover; border:1px solid #ddd; padding:3px;">
                        </div>

                        @error('avatar')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <button class="btn btn-primary" type="submit">
                    {{ __('dashboard.users_vendors.create') }}
                </button>

                <a class="btn btn-light" href="{{ route('admin.users.index') }}">
                    {{ __('dashboard.users_vendors.cancel') }}
                </a>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const countrySelect = document.getElementById('country_id');
            const citySelect = document.getElementById('city_id');
            const areaSelect = document.getElementById('area_id');

            const oldCity = @json(old('city_id', isset($user) ? $user->city_id : ''));
            const oldArea = @json(old('area_id', isset($user) ? $user->area_id : ''));

            function filterCities(selectedCity = '') {
                const countryId = countrySelect.value;

                Array.from(citySelect.options).forEach(function (option) {
                    if (!option.value) return;

                    const isVisible = countryId !== ''
                        && option.dataset.country === countryId;

                    option.hidden = !isVisible;
                    option.disabled = !isVisible;
                });

                const selectedOption = Array.from(citySelect.options).find(function (option) {
                    return option.value === String(selectedCity)
                        && !option.disabled;
                });

                citySelect.value = selectedOption ? selectedCity : '';

                filterAreas();
            }

            function filterAreas(selectedArea = '') {
                const cityId = citySelect.value;

                Array.from(areaSelect.options).forEach(function (option) {
                    if (!option.value) return;

                    const isVisible = cityId !== ''
                        && option.dataset.city === cityId;

                    option.hidden = !isVisible;
                    option.disabled = !isVisible;
                });

                const selectedOption = Array.from(areaSelect.options).find(function (option) {
                    return option.value === String(selectedArea)
                        && !option.disabled;
                });

                areaSelect.value = selectedOption ? selectedArea : '';
            }

            countrySelect.addEventListener('change', function () {
                // Reset the city and area when the country changes.
                citySelect.value = '';
                areaSelect.value = '';

                filterCities();
            });

            citySelect.addEventListener('change', function () {
                // Reset the area when the city changes.
                areaSelect.value = '';

                filterAreas();
            });

            // Apply the saved selections when opening the form.
            filterCities(String(oldCity || ''));
            filterAreas(String(oldArea || ''));
        });
    </script>

@endsection
