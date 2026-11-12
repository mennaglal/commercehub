@extends('web.layouts.app')
@section('title', __('web.vendor.products'))
@section('page', 'vendor-products')

@section('content')
<div class="page-hero"><div class="container d-flex flex-wrap justify-content-between align-items-center gap-2"><h1>{{ __('web.vendor.products') }}</h1>
    <a class="btn btn-brand" href="{{ route('site.vendor.products.create') }}"><i class="bi bi-plus-lg me-1"></i>{{ __('web.vendor.add_product') }}</a></div></div>

<section class="container pb-4" data-scroll-top>
    <div class="row g-4">
        <div class="col-lg-3">@include('web.partials.vendor-menu')</div>
        <div class="col-lg-9" id="vendor-products-box">
            <input class="form-control mb-3" type="search" id="vendor-search" maxlength="100" placeholder="{{ __('web.vendor.search_products') }}">
            <div class="table-card"><div class="table-responsive">
                <table class="table mb-0">
                    <thead><tr><th>{{ __('web.vendor.product') }}</th><th>{{ __('web.vendor.price') }}</th><th>{{ __('web.vendor.stock') }}</th><th>{{ __('web.vendor.visible') }}</th><th></th></tr></thead>
                    <tbody id="vendor-products"></tbody>
                </table>
            </div></div>
            <nav class="mt-3" id="vendor-pagination" aria-label="pages"></nav>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('assets/web/js/pages/vendor.js') }}?v={{ config('website.version') }}"></script>
@endpush
