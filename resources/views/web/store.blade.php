@extends('web.layouts.app')
@section('title', $store->name)
@section('description', \Illuminate\Support\Str::limit((string) $store->description, 160))
@section('image', $store->logo_url ?: asset('assets/web/img/logo.svg'))
@section('page', 'store')

@section('content')
<div class="container page-section" id="store-root" data-slug="{{ $slug }}" data-scroll-top>
    <div id="store-head" class="mb-4"><div class="skeleton" style="height:210px;border-radius:24px"></div></div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="section-title">{{ __('web.stores.products') }}</h2>
        <select class="form-select w-auto" id="store-sort" aria-label="{{ __('web.common.sort') }}">
            <option value="newest">{{ __('web.shop.sort.newest') }}</option>
            <option value="price_asc">{{ __('web.shop.sort.price_asc') }}</option>
            <option value="price_desc">{{ __('web.shop.sort.price_desc') }}</option>
            <option value="name">{{ __('web.shop.sort.name') }}</option>
        </select>
    </div>
    <div class="row g-3" id="store-products"></div>
    <nav class="mt-4" id="store-pagination" aria-label="pages"></nav>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/web/js/pages/stores.js') }}?v={{ config('website.version') }}"></script>
@endpush
