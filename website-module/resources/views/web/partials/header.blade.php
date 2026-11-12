@php
    $isAr = app()->getLocale() === 'ar';
    $site = config('website');
    $other = $isAr ? 'en' : 'ar';
    $active = fn (string ...$names) => request()->routeIs(...$names) ? 'active' : '';
@endphp

{{-- ===== the thin bar on top (desktop): promo text, contact, language, currency ===== --}}
<div class="topbar d-none d-lg-block">
    <div class="container d-flex justify-content-between align-items-center py-1">
        <span><i class="bi bi-truck me-1"></i> {{ __('web.brand.topbar') }}</span>
        <span class="d-flex align-items-center gap-2">
            <a href="tel:{{ preg_replace('/\s+/', '', $site['contact']['phone']) }}" dir="ltr"><i class="bi bi-telephone me-1"></i>{{ $site['contact']['phone'] }}</a>
            <span class="opacity-50">|</span>
            <a class="tb-btn" href="{{ url('/change-language/' . $other) }}"><i class="bi bi-translate me-1"></i>{{ $isAr ? 'English' : 'العربية' }}</a>
            <span class="opacity-50">|</span>
            <span class="dropdown">
                <button class="tb-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-currency-exchange me-1"></i><span class="js-currency-label">EGP</span></button>
                <ul class="dropdown-menu dropdown-menu-end js-currency-menu"></ul>
            </span>
        </span>
    </div>
</div>

{{-- ===== the main header ===== --}}
<header class="site-header">
    <div class="container">
        <div class="inner">
            <a class="brand" href="{{ route('site.home') }}">
                <img src="{{ asset($site['logo']) }}" alt="" width="40" height="40">
                <span class="d-none d-sm-inline">{{ $site['name'][$isAr ? 'ar' : 'en'] }}</span>
            </a>

            <form class="header-search desktop js-search-form d-none d-lg-block" role="search">
                <input class="form-control" name="q" type="search" maxlength="100" placeholder="{{ __('web.nav.search_placeholder') }}" aria-label="{{ __('web.common.search') }}">
                <button type="submit" aria-label="{{ __('web.common.search') }}"><i class="bi bi-search"></i></button>
            </form>

            <div class="header-actions">
                {{-- language and currency on phones (on desktop they are in the top bar) --}}
                <a class="icon-btn lang-switch d-lg-none" href="{{ url('/change-language/' . $other) }}" title="{{ __('web.nav.language') }}">{{ $isAr ? 'EN' : 'ع' }}</a>
                <div class="dropdown d-lg-none">
                    <button class="icon-btn lang-switch" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('web.nav.currency') }}"><span class="js-currency-label">EGP</span></button>
                    <ul class="dropdown-menu dropdown-menu-end js-currency-menu"></ul>
                </div>

                {{-- guest --}}
                <a class="hdr-link js-guest d-none d-md-flex" href="{{ route('site.login') }}">
                    <i class="bi bi-person"></i><span><small>{{ __('web.nav.hello') }}</small><strong>{{ __('web.nav.login') }}</strong></span>
                </a>

                {{-- logged in --}}
                <div class="dropdown js-member d-none">
                    <button class="hdr-link" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('web.nav.account') }}">
                        <i class="bi bi-person-circle"></i><span class="d-none d-md-block"><small>{{ __('web.nav.hello') }}</small><strong class="js-user-name"></strong></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" style="min-width:230px">
                        <li><a class="dropdown-item" href="{{ route('site.account') }}"><i class="bi bi-person me-2"></i>{{ __('web.nav.profile') }}</a></li>
                        <li><a class="dropdown-item" href="{{ route('site.account.orders') }}"><i class="bi bi-bag-check me-2"></i>{{ __('web.nav.orders') }}</a></li>
                        <li><a class="dropdown-item" href="{{ route('site.account.returns') }}"><i class="bi bi-arrow-repeat me-2"></i>{{ __('web.nav.returns') }}</a></li>
                        <li><a class="dropdown-item" href="{{ route('site.account.notifications') }}"><i class="bi bi-bell me-2"></i>{{ __('web.nav.notifications') }}</a></li>
                        <li class="js-vendor-only d-none"><a class="dropdown-item" href="{{ route('site.vendor') }}"><i class="bi bi-shop me-2"></i>{{ __('web.nav.vendor') }}</a></li>
                        <li class="js-not-vendor"><a class="dropdown-item" href="{{ route('site.become-vendor') }}"><i class="bi bi-shop-window me-2"></i>{{ __('web.nav.become_vendor') }}</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger js-logout" href="#"><i class="bi bi-box-arrow-right me-2"></i>{{ __('web.nav.logout') }}</a></li>
                    </ul>
                </div>

                {{-- notifications (shown by JS only when the notifications module exists) --}}
                <a class="icon-btn js-bell d-none" href="{{ route('site.account.notifications') }}" title="{{ __('web.nav.notifications') }}">
                    <i class="bi bi-bell"></i><span class="count-badge js-bell-count d-none">0</span>
                </a>

                {{-- cart --}}
                <a class="hdr-link" href="{{ route('site.cart') }}" title="{{ __('web.nav.cart') }}">
                    <i class="bi bi-bag"></i><span class="d-none d-md-block"><strong>{{ __('web.nav.cart') }}</strong></span>
                    <span class="count-badge js-cart-count d-none">0</span>
                </a>
            </div>
        </div>

        {{-- the search box on small screens --}}
        <form class="header-search js-search-form d-lg-none pb-2" style="max-width:none" role="search">
            <input class="form-control" name="q" type="search" maxlength="100" placeholder="{{ __('web.nav.search_placeholder') }}" aria-label="{{ __('web.common.search') }}">
            <button type="submit" style="height:46px" aria-label="{{ __('web.common.search') }}"><i class="bi bi-search"></i></button>
        </form>
    </div>
</header>

{{-- ===== the categories: the big menu + the strip (filled by JS from /api/categories) ===== --}}
<nav class="cat-nav" aria-label="categories">
    <div class="container">
        <div class="inner">
            <div class="dropdown d-none d-lg-block">
                <button class="all-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside"><i class="bi bi-list"></i>{{ __('web.nav.all_categories') }}</button>
                <div class="dropdown-menu mega-menu"><div class="mega-grid" id="mega-grid"></div></div>
            </div>
            <div class="cat-list js-cat-list"></div>
        </div>
    </div>
</nav>
