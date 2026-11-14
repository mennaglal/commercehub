@extends('admin.layouts.app')

@section('content')
    @include('includes.error')
    @include('includes.success')

    <div class="card shadow mb-4">
        <div class="card-header"><h5 class="mb-0">{{ __('dashboard.users_vendors.edit_vendor') }}</h5></div>
        <div class="card-body">
            <form
                method="POST"
                action="{{ route('admin.vendors.update', $vendor) }}"
                enctype="multipart/form-data"
            >
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>{{ __('dashboard.users_vendors.vendor_name') }}</label>
                        <input class="form-control" name="name" value="{{ old('name', $vendor->name) }}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>{{ __('dashboard.users_vendors.email') }}</label>
                        <input class="form-control" type="email" name="email" value="{{ old('email', $vendor->email) }}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>{{ __('dashboard.users_vendors.phone') }}</label>
                        <input class="form-control" name="phone" value="{{ old('phone', $vendor->phone) }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label>{{ __('dashboard.users_vendors.password_optional') }}</label>
                        <input class="form-control" type="password" name="password" autocomplete="new-password">
                    </div>
                    <div class="form-group col-md-6">
                        <label>{{ __('dashboard.users_vendors.password_confirmation') }}</label>
                        <input class="form-control" type="password" name="password_confirmation" autocomplete="new-password">
                    </div>
                    <div class="form-group col-12">
                        <label>{{ __('dashboard.users_vendors.description') }}</label>
                        <textarea class="form-control" name="description" rows="4">{{ old('description', $vendor->store?->description) }}</textarea>
                    </div>

                    <div class="form-group col-md-6">
                        <label>{{ __('dashboard.users_vendors.store_logo') }}</label>

                        <input
                            type="file"
                            name="logo"
                            id="storeLogo"
                            class="form-control"
                            accept="image/jpeg,image/png,image/webp"
                        >

                        <div id="storeLogoPreviewContainer" class="mt-3">
                            @if($vendor->store?->logo)
                                <img
                                    id="storeLogoPreview"
                                    src="{{ asset($vendor->store->logo) }}"
                                    alt="Store Logo"
                                    width="100"
                                    height="100"
                                    class="rounded"
                                    style="object-fit: cover; border: 1px solid #ddd; padding: 3px;"
                                >
                            @else
                                <img
                                    id="storeLogoPreview"
                                    src=""
                                    alt="Store Logo Preview"
                                    width="100"
                                    height="100"
                                    class="rounded"
                                    style="display: none; object-fit: cover; border: 1px solid #ddd; padding: 3px;"
                                >
                            @endif
                        </div>

                        @error('logo')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group col-md-6">
                        <label>{{ __('dashboard.users_vendors.store_banner') }}</label>

                        <input
                            type="file"
                            name="banner"
                            id="storeBanner"
                            class="form-control"
                            accept="image/jpeg,image/png,image/webp"
                        >

                        <div id="storeBannerPreviewContainer" class="mt-3">
                            @if($vendor->store?->banner)
                                <img
                                    id="storeBannerPreview"
                                    src="{{ asset($vendor->store->banner) }}"
                                    alt="Store Banner"
                                    style="width: 100%; max-width: 300px; height: 120px; object-fit: cover; border: 1px solid #ddd; padding: 3px;"
                                >
                            @else
                                <img
                                    id="storeBannerPreview"
                                    src=""
                                    alt="Store Banner Preview"
                                    style="display: none; width: 100%; max-width: 300px; height: 120px; object-fit: cover; border: 1px solid #ddd; padding: 3px;"
                                >
                            @endif
                        </div>

                        @error('banner')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                </div>
                @include('admin.pages.vendors._location-fields', [
                    'countries' => $countries,
                    'cities' => $cities,
                    'areas' => $areas,
                    'selectedCountry' => $vendor->store?->country_id,
                    'selectedCity' => $vendor->store?->city_id,
                    'selectedArea' => $vendor->store?->area_id,
                ])
                <button class="btn btn-primary" type="submit">{{ __('dashboard.users_vendors.update') }}</button>
                <a class="btn btn-light" href="{{ route('admin.vendors.index') }}">{{ __('dashboard.users_vendors.cancel') }}</a>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const imageInputs = [
                {
                    inputId: 'storeLogo',
                    previewId: 'storeLogoPreview'
                },
                {
                    inputId: 'storeBanner',
                    previewId: 'storeBannerPreview'
                }
            ];

            imageInputs.forEach(function (item) {
                const input = document.getElementById(item.inputId);
                const preview = document.getElementById(item.previewId);

                if (!input || !preview) {
                    return;
                }

                let currentObjectUrl = null;

                input.addEventListener('change', function () {
                    const file = input.files[0];

                    if (!file) {
                        return;
                    }

                    if (!file.type.startsWith('image/')) {
                        input.value = '';
                        return;
                    }

                    if (currentObjectUrl) {
                        URL.revokeObjectURL(currentObjectUrl);
                    }

                    currentObjectUrl = URL.createObjectURL(file);

                    preview.src = currentObjectUrl;
                    preview.style.display = 'block';
                });
            });
        });
    </script>

@endsection
