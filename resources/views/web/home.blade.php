@extends('web.layouts.app')
@section('page', 'home')

@section('content')
{{-- ========== Hero ========== --}}
<section class="hero">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <span class="chip mb-3" style="background:rgba(255,255,255,.15);color:#fff"><i class="bi bi-stars"></i> {{ __('web.home.badge') }}</span>
                <h1 class="mb-3">{{ __('web.home.hero_title') }} <span>{{ __('web.home.hero_accent') }}</span></h1>
                <p class="lead mb-4">{{ __('web.home.hero_text') }}</p>

                <form class="hero-search js-search-form" role="search">
                    <input type="search" name="q" maxlength="100" placeholder="{{ __('web.nav.search_placeholder') }}" aria-label="{{ __('web.common.search') }}">
                    <button class="btn btn-accent" type="submit"><i class="bi bi-search me-1"></i>{{ __('web.common.search') }}</button>
                </form>

                <div class="d-flex flex-wrap gap-2 mt-4">
                    <a class="btn btn-light rounded-pill fw-bold" href="{{ route('site.shop') }}">{{ __('web.home.shop_now') }}</a>
                    <a class="btn btn-outline-light rounded-pill fw-bold" href="{{ route('site.become-vendor') }}">{{ __('web.nav.become_vendor') }}</a>
                </div>
            </div>

            <div class="col-lg-5 d-none d-lg-block">
                <div class="hero-art">
                    <div class="bubble b1"><i class="bi bi-truck"></i> {{ __('web.home.b1') }}</div>
                    <div class="bubble b2"><i class="bi bi-shield-check"></i> {{ __('web.home.b2') }}</div>
                    <div class="bubble b3"><i class="bi bi-arrow-repeat"></i> {{ __('web.home.b3') }}</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ========== Categories ========== --}}
<section class="page-section pb-0" id="home-categories-box">
    <div class="container">
        <div class="section-head"><h2 class="section-title">{{ __('web.home.categories') }}</h2><a href="{{ route('site.shop') }}">{{ __('web.common.see_all') }}</a></div>
        <div class="cat-scroll" id="home-categories"></div>
    </div>
</section>

{{-- ========== Deals ========== --}}
<section class="page-section">
    <div class="container">
        <div class="section-head">
            <div><h2 class="section-title">{{ __('web.home.deals') }}</h2><p class="section-sub">{{ __('web.home.deals_text') }}</p></div>
            <a href="{{ route('site.shop', ['on_sale' => 1]) }}">{{ __('web.common.see_all') }}</a>
        </div>
        <div class="row g-3" id="home-deals"></div>
    </div>
</section>

{{-- ========== Why us ========== --}}
<section class="page-section pt-0">
    <div class="container">
        <div class="row g-3">
            @foreach([['bi-truck', 'f1'], ['bi-shield-lock', 'f2'], ['bi-arrow-repeat', 'f3'], ['bi-headset', 'f4']] as [$icon, $key])
                <div class="col-6 col-lg-3"><div class="feature"><div class="ico"><i class="bi {{ $icon }}"></i></div><h6>{{ __('web.home.' . $key . '_title') }}</h6><p class="text-muted small mb-0">{{ __('web.home.' . $key . '_text') }}</p></div></div>
            @endforeach
        </div>
    </div>
</section>

{{-- ========== New arrivals ========== --}}
<section class="page-section pt-0">
    <div class="container">
        <div class="section-head"><h2 class="section-title">{{ __('web.home.new') }}</h2><a href="{{ route('site.shop') }}">{{ __('web.common.see_all') }}</a></div>
        <div class="row g-3" id="home-new"></div>
    </div>
</section>

{{-- ========== Stores ========== --}}
<section class="page-section pt-0">
    <div class="container">
        <div class="section-head"><h2 class="section-title">{{ __('web.home.stores') }}</h2><a href="{{ route('site.stores') }}">{{ __('web.common.see_all') }}</a></div>
        <div class="row g-3" id="home-stores"></div>
    </div>
</section>

{{-- ========== Become a vendor ========== --}}
<section class="page-section pt-0">
    <div class="container">
        <div class="cta-banner">
            <div class="row align-items-center g-3">
                <div class="col-lg-8"><h3 class="mb-2">{{ __('web.home.cta_title') }}</h3><p class="mb-0 opacity-75">{{ __('web.home.cta_text') }}</p></div>
                <div class="col-lg-4 text-lg-end"><a class="btn btn-light btn-lg rounded-pill fw-bold" href="{{ route('site.become-vendor') }}">{{ __('web.home.cta_button') }}</a></div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('assets/web/js/pages/home.js') }}?v={{ config('website.version') }}"></script>
@endpush
