@extends('web.layouts.app')
@section('title', __('web.vendor.my_store'))
@section('page', 'vendor-store')

@section('content')
<div class="page-hero"><div class="container d-flex flex-wrap justify-content-between align-items-center gap-2"><h1>{{ __('web.vendor.my_store') }}</h1><div id="store-approval"></div></div></div>

<section class="container pb-4">
    <div class="row g-4">
        <div class="col-lg-3">@include('web.partials.vendor-menu')</div>
        <div class="col-lg-9">
            <form class="card-soft pad" id="store-form" enctype="multipart/form-data" novalidate>
                <div class="alert alert-danger form-errors d-none" role="alert"></div>
                <div class="d-flex align-items-center gap-3 mb-3"><img class="avatar" style="border-radius:20px" id="store-logo-img" src="{{ asset('assets/web/img/placeholder.svg') }}" alt=""><div class="small text-muted">{{ __('web.vendor.logo') }}</div></div>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="name">{{ __('web.vendor.store_name') }} *</label><input class="form-control" id="name" name="name" maxlength="255" required></div>
                    <div class="col-md-3"><label class="form-label" for="logo">{{ __('web.vendor.logo') }}</label><input class="form-control" type="file" id="logo" name="logo" accept=".jpg,.jpeg,.png,.webp"></div>
                    <div class="col-md-3"><label class="form-label" for="banner">{{ __('web.vendor.banner') }}</label><input class="form-control" type="file" id="banner" name="banner" accept=".jpg,.jpeg,.png,.webp"></div>
                    <div class="col-12"><label class="form-label" for="description">{{ __('web.vendor.description') }}</label><textarea class="form-control" id="description" name="description" rows="3" maxlength="1000"></textarea></div>
                    <div class="col-md-4"><label class="form-label" for="country_id">{{ __('web.checkout.country') }}</label><select class="form-select" id="country_id" name="country_id"></select></div>
                    <div class="col-md-4"><label class="form-label" for="city_id">{{ __('web.checkout.city') }}</label><select class="form-select" id="city_id" name="city_id" disabled></select></div>
                    <div class="col-md-4"><label class="form-label" for="area_id">{{ __('web.checkout.area') }}</label><select class="form-select" id="area_id" name="area_id" disabled></select></div>
                </div>
                <button class="btn btn-brand mt-3" type="submit">{{ __('web.common.save') }}</button>
            </form>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('assets/web/js/pages/vendor.js') }}?v={{ config('website.version') }}"></script>
@endpush
