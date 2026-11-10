@extends('web.layouts.app')
@section('title', __('web.vendor.dashboard'))
@section('page', 'vendor-dashboard')

@section('content')
<div class="page-hero"><div class="container"><h1>{{ __('web.vendor.dashboard') }}</h1><p class="text-muted mb-0 js-store-name" id="vendor-store-name">&nbsp;</p></div></div>

<section class="container pb-4">
    <div class="row g-4">
        <div class="col-lg-3">@include('web.partials.vendor-menu')</div>
        <div class="col-lg-9"><div id="vendor-root"><div class="row g-3">@for($i = 0; $i < 6; $i++)<div class="col-6 col-lg-4"><div class="skeleton" style="height:120px"></div></div>@endfor</div></div></div>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('assets/web/js/pages/vendor.js') }}?v={{ config('website.version') }}"></script>
@endpush
