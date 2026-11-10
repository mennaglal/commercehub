@extends('web.layouts.app')
@section('title', __('web.nav.orders'))
@section('page', 'account-orders')

@section('content')
<div class="page-hero"><div class="container"><h1>{{ __('web.nav.orders') }}</h1></div></div>

<section class="container pb-4" data-scroll-top>
    <div class="row g-4">
        <div class="col-lg-3">@include('web.partials.account-menu')</div>
        <div class="col-lg-9"><div id="orders-list"></div><nav class="mt-3" id="orders-pagination" aria-label="pages"></nav></div>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('assets/web/js/pages/orders.js') }}?v={{ config('website.version') }}"></script>
@endpush
