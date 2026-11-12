@extends('web.layouts.app')
@section('title', __('web.cart.title'))
@section('page', 'cart')

@section('content')
<div class="page-hero"><div class="container"><h1>{{ __('web.cart.title') }}</h1></div></div>

<section class="container pb-4">
    <div id="cart-empty" class="d-none"></div>

    <div class="row g-4" id="cart-content">
        <div class="col-lg-8">
            <div class="card-soft pad">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">{{ __('web.cart.items') }}</h5>
                    <button type="button" class="btn btn-link text-danger p-0" id="cart-clear"><i class="bi bi-trash"></i> {{ __('web.cart.clear') }}</button>
                </div>
                <div class="alert alert-warning d-none" id="cart-alert" role="alert"></div>
                <div id="cart-lines"><div class="skeleton" style="height:110px"></div></div>
            </div>
        </div>
        <div class="col-lg-4" id="cart-summary"></div>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('assets/web/js/pages/cart.js') }}?v={{ config('website.version') }}"></script>
@endpush
