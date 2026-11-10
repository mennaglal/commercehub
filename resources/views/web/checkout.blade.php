@php $isAr = app()->getLocale() === 'ar'; @endphp
@extends('web.layouts.app')
@section('title', __('web.checkout.title'))
@section('page', 'checkout')

@section('content')
<div class="page-hero"><div class="container"><h1>{{ __('web.checkout.title') }}</h1></div></div>

<section class="container pb-4">
    <form id="checkout-form" novalidate>
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="alert alert-danger d-none" id="checkout-alert" role="alert"></div>
                <div class="alert alert-danger form-errors d-none" role="alert"></div>

                <div class="card-soft pad mb-3">
                    <h5 class="mb-3"><i class="bi bi-person me-1"></i>{{ __('web.checkout.contact') }}</h5>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label" for="customer_name">{{ __('web.checkout.name') }} *</label><input class="form-control" id="customer_name" name="customer_name" maxlength="255" required></div>
                        <div class="col-md-6"><label class="form-label" for="customer_phone">{{ __('web.checkout.phone') }} *</label><input class="form-control" id="customer_phone" name="customer_phone" maxlength="30" inputmode="tel" required></div>
                    </div>
                </div>

                <div class="card-soft pad mb-3">
                    <h5 class="mb-3"><i class="bi bi-geo-alt me-1"></i>{{ __('web.checkout.address') }}</h5>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="country_id">{{ __('web.checkout.country') }} *</label><select class="form-select" id="country_id" name="country_id" required></select></div>
                        <div class="col-md-4"><label class="form-label" for="city_id">{{ __('web.checkout.city') }} *</label><select class="form-select" id="city_id" name="city_id" required disabled></select></div>
                        <div class="col-md-4"><label class="form-label" for="area_id">{{ __('web.checkout.area') }} *</label><select class="form-select" id="area_id" name="area_id" required disabled></select></div>
                        <div class="col-12"><label class="form-label" for="shipping_address">{{ __('web.checkout.street') }} *</label><textarea class="form-control" id="shipping_address" name="shipping_address" rows="2" maxlength="1000" required></textarea></div>
                        <div class="col-12"><label class="form-label" for="shipping_notes">{{ __('web.checkout.shipping_notes') }}</label><input class="form-control" id="shipping_notes" name="shipping_notes" maxlength="1000"></div>
                    </div>
                </div>

                <div class="card-soft pad mb-3">
                    <h5 class="mb-3"><i class="bi bi-wallet2 me-1"></i>{{ __('web.checkout.payment') }}</h5>
                    <div id="pay-options"></div>
                    <div id="gateway-box" class="d-none"></div>
                    <div id="bank-box" class="alert alert-info mt-2 d-none" style="white-space:pre-line">{{ config('website.bank_details.' . ($isAr ? 'ar' : 'en')) }}</div>
                </div>

                <div class="card-soft pad">
                    <label class="form-label" for="notes">{{ __('web.checkout.notes') }}</label>
                    <textarea class="form-control" id="notes" name="notes" rows="2" maxlength="1000"></textarea>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card-soft pad sticky-summary">
                    <h5 class="mb-3">{{ __('web.cart.summary') }}</h5>
                    <div class="input-group mb-3">
                        <input class="form-control" id="co-coupon" maxlength="50" placeholder="{{ __('web.cart.coupon_placeholder') }}">
                        <button class="btn btn-soft" type="button" id="co-coupon-apply">{{ __('web.common.apply') }}</button>
                    </div>
                    <div id="checkout-summary"><div class="skeleton" style="height:160px"></div></div>
                    <button class="btn btn-accent btn-lg w-100 mt-3" id="place-order" type="submit" disabled>{{ __('web.checkout.place_order') }}</button>
                    <p class="small text-muted text-center mt-2 mb-0"><i class="bi bi-shield-lock"></i> {{ __('web.checkout.secure_note') }}</p>
                </div>
            </div>
        </div>
    </form>
</section>
@endsection

@push('scripts')
<script src="{{ asset('assets/web/js/pages/checkout.js') }}?v={{ config('website.version') }}"></script>
@endpush
