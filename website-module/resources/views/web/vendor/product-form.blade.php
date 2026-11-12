@extends('web.layouts.app')
@section('title', $productId ? __('web.vendor.edit_product') : __('web.vendor.add_product'))
@section('page', 'vendor-product-form')

@section('content')
<div class="page-hero"><div class="container"><h1>{{ $productId ? __('web.vendor.edit_product') : __('web.vendor.add_product') }}</h1></div></div>

<section class="container pb-4">
    <div class="row g-4">
        <div class="col-lg-3">@include('web.partials.vendor-menu')</div>

        <div class="col-lg-9" id="pf-root" data-id="{{ $productId }}">
            <div id="pf-loading"><div class="skeleton" style="height:300px"></div></div>

            {{-- The inputs have the real names, the whole form is sent as it is (multipart/form-data) --}}
            <form id="product-form" class="d-none" enctype="multipart/form-data" novalidate>
                <div class="alert alert-danger form-errors d-none" role="alert"></div>

                {{-- ===== basic ===== --}}
                <div class="card-soft pad mb-3">
                    <h5 class="mb-3">{{ __('web.vendor.basic') }}</h5>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label" for="name_ar">{{ __('web.vendor.name_ar') }} *</label><input class="form-control" id="name_ar" name="name_ar" maxlength="255" dir="rtl" required></div>
                        <div class="col-md-6"><label class="form-label" for="name_en">{{ __('web.vendor.name_en') }} *</label><input class="form-control" id="name_en" name="name_en" maxlength="255" required></div>
                        <div class="col-md-6"><label class="form-label" for="sku">SKU *</label><input class="form-control" id="sku" name="sku" maxlength="255" required></div>
                        <div class="col-md-6"><label class="form-label" for="category_id">{{ __('web.vendor.category') }} *</label><select class="form-select" id="category_id" name="category_id" required></select></div>
                        <div class="col-md-6"><label class="form-label" for="description_ar">{{ __('web.vendor.description_ar') }}</label><textarea class="form-control" id="description_ar" name="description_ar" rows="4" dir="rtl"></textarea></div>
                        <div class="col-md-6"><label class="form-label" for="description_en">{{ __('web.vendor.description_en') }}</label><textarea class="form-control" id="description_en" name="description_en" rows="4"></textarea></div>
                        <div class="col-md-6">
                            <label class="form-label" for="image">{{ __('web.vendor.image') }} {{ $productId ? '' : '*' }}</label>
                            <input class="form-control" type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp" {{ $productId ? '' : 'required' }}>
                            <img id="image-preview" class="d-none mt-2 rounded" style="max-height:120px" alt="">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <input type="hidden" name="status" value="0">
                            <div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="status" name="status" value="1" checked><label class="form-check-label" for="status">{{ __('web.vendor.visible') }}</label></div>
                        </div>
                    </div>
                </div>

                {{-- ===== price ===== --}}
                <div class="card-soft pad mb-3">
                    <h5 class="mb-3">{{ __('web.vendor.pricing') }}</h5>
                    <input type="hidden" name="has_variants" value="0">
                    <div class="form-check form-switch mb-1"><input class="form-check-input" type="checkbox" id="has_variants" name="has_variants" value="1"><label class="form-check-label fw-bold" for="has_variants">{{ __('web.vendor.has_variants') }}</label></div>
                    <p class="text-muted small">{{ __('web.vendor.has_variants_hint') }}</p>

                    {{-- simple product --}}
                    <div id="simple-box"><div class="row g-3">
                        <div class="col-md-6"><label class="form-label" for="price">{{ __('web.vendor.price') }} *</label><input class="form-control" type="number" step="0.01" min="0" id="price" name="price" required></div>
                        <div class="col-md-6"><label class="form-label" for="stock">{{ __('web.vendor.stock') }} *</label><input class="form-control" type="number" min="0" id="stock" name="stock" required></div>
                        <div class="col-12">
                            <input type="hidden" name="is_sale" value="0">
                            <div class="form-check"><input class="form-check-input" type="checkbox" id="is_sale" name="is_sale" value="1"><label class="form-check-label" for="is_sale">{{ __('web.vendor.is_sale') }}</label></div>
                        </div>
                        <div class="col-md-6 d-none" id="sale-box"><label class="form-label" for="sale_price">{{ __('web.vendor.sale_price') }} *</label><input class="form-control" type="number" step="0.01" min="0" id="sale_price" name="sale_price"></div>
                    </div></div>

                    {{-- product with variants --}}
                    <div id="variants-box" class="d-none">
                        <h6 class="mt-2">1. {{ __('web.vendor.step1') }}</h6>
                        <p class="text-muted small">{{ __('web.vendor.step1_hint') }}</p>
                        <div id="variant-types"></div>
                        <h6 class="mt-3">2. {{ __('web.vendor.step2') }}</h6>
                        <button type="button" class="btn btn-soft mb-3" id="generate-variants"><i class="bi bi-diagram-3 me-1"></i>{{ __('web.vendor.generate') }}</button>
                        <div id="variant-rows"></div>
                    </div>
                </div>

                <button class="btn btn-brand btn-lg" type="submit">{{ __('web.common.save') }}</button>
                <a class="btn btn-link" href="{{ route('site.vendor.products') }}">{{ __('web.common.cancel') }}</a>
            </form>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('assets/web/js/pages/vendor.js') }}?v={{ config('website.version') }}"></script>
<script src="{{ asset('assets/web/js/pages/vendor-product-form.js') }}?v={{ config('website.version') }}"></script>
@endpush
