@extends('web.layouts.app')
@section('title', __('web.shop.title'))
@section('page', 'shop')

@section('content')
<div class="page-hero"><div class="container"><h1>{{ __('web.shop.title') }}</h1></div></div>

<section class="container pb-4" data-scroll-top>
    <div class="row g-4">
        {{-- ===== filters ===== --}}
        <aside class="col-lg-3">
            <button class="btn btn-soft w-100 d-lg-none mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#filters-box"><i class="bi bi-funnel me-1"></i>{{ __('web.shop.filters') }}</button>

            <div class="collapse d-lg-block" id="filters-box">
                <form class="filter-box mb-3" id="filters-form" novalidate>
                    <h6>{{ __('web.common.search') }}</h6>
                    <input class="form-control mb-3" type="search" name="q" maxlength="100" placeholder="{{ __('web.nav.search_placeholder') }}">

                    <h6>{{ __('web.shop.price') }} <small class="text-muted fw-normal">(EGP)</small></h6>
                    <div class="d-flex gap-2 mb-3">
                        <input class="form-control" type="number" min="0" name="min_price" placeholder="{{ __('web.shop.min') }}">
                        <input class="form-control" type="number" min="0" name="max_price" placeholder="{{ __('web.shop.max') }}">
                    </div>

                    <div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="on_sale" id="f-sale"><label class="form-check-label" for="f-sale">{{ __('web.shop.on_sale') }}</label></div>
                    <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="in_stock" id="f-stock"><label class="form-check-label" for="f-stock">{{ __('web.shop.in_stock') }}</label></div>

                    <button class="btn btn-brand w-100 mb-2" type="submit">{{ __('web.common.apply') }}</button>
                    <button class="btn btn-link w-100" type="button" id="filters-reset">{{ __('web.common.reset') }}</button>
                </form>

                <div class="filter-box"><h6>{{ __('web.shop.categories') }}</h6><ul class="cat-tree" id="cat-tree"></ul></div>
            </div>
        </aside>

        {{-- ===== results ===== --}}
        <div class="col-lg-9">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <div id="shop-count" class="text-muted"></div>
                <select class="form-select w-auto" id="sort-select" aria-label="{{ __('web.common.sort') }}">
                    <option value="newest">{{ __('web.shop.sort.newest') }}</option>
                    <option value="price_asc">{{ __('web.shop.sort.price_asc') }}</option>
                    <option value="price_desc">{{ __('web.shop.sort.price_desc') }}</option>
                    <option value="name">{{ __('web.shop.sort.name') }}</option>
                </select>
            </div>
            <div class="d-flex flex-wrap gap-2 mb-3" id="active-filters"></div>
            <div class="row g-3" id="shop-grid"></div>
            <nav class="mt-4" id="shop-pagination" aria-label="pages"></nav>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('assets/web/js/pages/shop.js') }}?v={{ config('website.version') }}"></script>
@endpush
