@extends('web.layouts.app')
@section('title', __('web.vendor.orders'))
@section('page', 'vendor-orders')

@section('content')
<div class="page-hero"><div class="container"><h1>{{ __('web.vendor.orders') }}</h1></div></div>

<section class="container pb-4">
    <div class="row g-4">
        <div class="col-lg-3">@include('web.partials.vendor-menu')</div>
        <div class="col-lg-9"><div id="vendor-orders"></div><nav class="mt-3" id="vendor-orders-pagination" aria-label="pages"></nav></div>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('assets/web/js/pages/vendor.js') }}?v={{ config('website.version') }}"></script>
@endpush
