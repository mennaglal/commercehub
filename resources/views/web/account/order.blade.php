@extends('web.layouts.app')
@section('title', __('web.orders.details'))
@section('page', 'account-order')

@section('content')
<div class="page-hero"><div class="container"><h1>{{ __('web.orders.details') }}</h1></div></div>

<section class="container pb-4">
    <div class="row g-4">
        <div class="col-lg-3">@include('web.partials.account-menu')</div>
        <div class="col-lg-9"><div id="order-root" data-order="{{ $orderNumber }}"><div class="skeleton" style="height:240px"></div></div></div>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('assets/web/js/pages/orders.js') }}?v={{ config('website.version') }}"></script>
@endpush
