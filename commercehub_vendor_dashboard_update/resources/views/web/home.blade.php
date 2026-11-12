@extends('web.layouts.app')
@section('page', 'home')

@section('content')
{{-- ========== Hero: slider + 2 promo tiles ========== --}}
<section class="hero-wrap">
    <div class="container">
        <div class="row g-3">
            <div class="col-lg-9">
                <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5500">
                    <div class="carousel-indicators">
                        @for($i = 0; $i < 3; $i++)
                            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $i }}" class="{{ $i === 0 ? 'active' : '' }}" aria-label="slide {{ $i + 1 }}"></button>
                        @endfor
                    </div>
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <div class="hero-slide slide-navy">
                                <div class="content">
                                    <h2>{{ __('web.home.s1_title') }} <span>{{ __('web.home.s1_accent') }}</span></h2>
                                    <p>{{ __('web.home.s1_text') }}</p>
                                    <a class="btn btn-accent btn-lg" href="{{ route('site.shop') }}">{{ __('web.home.s1_cta') }}</a>
                                </div>
                                <div class="art"><i class="bi bi-bag-heart-fill"></i></div>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="hero-slide slide-gold">
                                <div class="content">
                                    <h2>{{ __('web.home.s2_title') }} <span>{{ __('web.home.s2_accent') }}</span></h2>
                                    <p>{{ __('web.home.s2_text') }}</p>
                                    <a class="btn btn-brand btn-lg" href="{{ route('site.shop', ['on_sale' => 1]) }}">{{ __('web.home.s2_cta') }}</a>
                                </div>
                                <div class="art"><i class="bi bi-tags-fill"></i></div>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="hero-slide slide-dark">
                                <div class="content">
                                    <h2>{{ __('web.home.s3_title') }} <span>{{ __('web.home.s3_accent') }}</span></h2>
                                    <p>{{ __('web.home.s3_text') }}</p>
                                    <a class="btn btn-accent btn-lg" href="{{ route('site.become-vendor') }}">{{ __('web.home.s3_cta') }}</a>
                                </div>
                                <div class="art"><i class="bi bi-shop-window"></i></div>
                            </div>
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev" aria-label="prev"><span class="carousel-control-prev-icon"></span></button>
                    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next" aria-label="next"><span class="carousel-control-next-icon"></span></button>
                </div>
            </div>

            <div class="col-lg-3 d-none d-lg-flex flex-column" style="height:380px">
                <a class="promo-tile promo-1" href="{{ route('site.shop', ['on_sale' => 1]) }}"><h5>{{ __('web.home.promo1_title') }}</h5><span>{{ __('web.home.promo1_text') }}</span><i class="bi bi-percent big"></i></a>
                <a class="promo-tile promo-2" href="{{ route('site.stores') }}"><h5>{{ __('web.home.promo2_title') }}</h5><span>{{ __('web.home.promo2_text') }}</span><i class="bi bi-shop big"></i></a>
            </div>
        </div>

        {{-- the 4 promises --}}
        <div class="trust mt-3">
            @foreach([['bi-truck', 'f1'], ['bi-shield-lock', 'f2'], ['bi-arrow-repeat', 'f3'], ['bi-headset', 'f4']] as [$icon, $key])
                <div class="item"><div class="ico"><i class="bi {{ $icon }}"></i></div><div><strong>{{ __('web.home.' . $key . '_title') }}</strong><small>{{ __('web.home.' . $key . '_text') }}</small></div></div>
            @endforeach
        </div>
    </div>
</section>

{{-- ========== Categories ========== --}}
<section class="page-section pb-0" id="home-categories-box">
    <div class="container">
        <div class="section-head"><h2 class="section-title">{{ __('web.home.categories') }}</h2><a class="see-all" href="{{ route('site.shop') }}">{{ __('web.common.see_all') }} <i class="bi bi-arrow-right"></i></a></div>
        <div class="cat-scroll" id="home-categories"></div>
    </div>
</section>

{{-- ========== Deals ========== --}}
<section class="page-section">
    <div class="container">
        <div class="section-head">
            <div><h2 class="section-title"><i class="bi bi-lightning-charge-fill" style="color:var(--primary-color)"></i>{{ __('web.home.deals') }}</h2><p class="section-sub">{{ __('web.home.deals_text') }}</p></div>
            <a class="see-all" href="{{ route('site.shop', ['on_sale' => 1]) }}">{{ __('web.common.see_all') }} <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="row g-3" id="home-deals"></div>
    </div>
</section>

{{-- ========== New arrivals ========== --}}
<section class="page-section pt-0">
    <div class="container">
        <div class="section-head"><h2 class="section-title">{{ __('web.home.new') }}</h2><a class="see-all" href="{{ route('site.shop') }}">{{ __('web.common.see_all') }} <i class="bi bi-arrow-right"></i></a></div>
        <div class="row g-3" id="home-new"></div>
    </div>
</section>

{{-- ========== Stores ========== --}}
<section class="page-section pt-0">
    <div class="container">
        <div class="section-head"><h2 class="section-title">{{ __('web.home.stores') }}</h2><a class="see-all" href="{{ route('site.stores') }}">{{ __('web.common.see_all') }} <i class="bi bi-arrow-right"></i></a></div>
        <div class="row g-3" id="home-stores"></div>
    </div>
</section>

{{-- ========== Become a vendor ========== --}}
<section class="page-section pt-0">
    <div class="container">
        <div class="cta-banner">
            <div class="row align-items-center g-3">
                <div class="col-lg-8"><h3 class="mb-2">{{ __('web.home.cta_title') }}</h3><p class="mb-0 opacity-75">{{ __('web.home.cta_text') }}</p></div>
                <div class="col-lg-4 text-lg-end"><a class="btn btn-accent btn-lg fw-bold" href="{{ route('site.become-vendor') }}">{{ __('web.home.cta_button') }}</a></div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('assets/web/js/pages/home.js') }}?v={{ config('website.version') }}"></script>
@endpush
