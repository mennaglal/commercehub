@php
    $isAr = app()->getLocale() === 'ar';
    $site = config('website');
    $other = $isAr ? 'en' : 'ar';
    $active = fn (string ...$names) => request()->routeIs(...$names) ? 'active' : '';
@endphp

<div class="topbar d-none d-md-block">
    <div class="container d-flex justify-content-between py-2">
        <span><i class="bi bi-truck me-1"></i> {{ __('web.brand.topbar') }}</span>
        <span>
            <a href="mailto:{{ $site['contact']['email'] }}"><i class="bi bi-envelope me-1"></i>{{ $site['contact']['email'] }}</a>
            <span class="mx-2">|</span>
            <a href="tel:{{ preg_replace('/\s+/', '', $site['contact']['phone']) }}" dir="ltr"><i class="bi bi-telephone me-1"></i>{{ $site['contact']['phone'] }}</a>
        </span>
    </div>
</div>

<header class="site-header">
    <div class="container">
        <div class="inner">
            <a class="brand" href="{{ route('site.home') }}">
                <img src="{{ asset($site['logo']) }}" alt="" width="40" height="40">
                <span class="d-none d-sm-inline">{{ $site['name'][$isAr ? 'ar' : 'en'] }}</span>
            </a>

            <nav class="main-nav d-none d-lg-flex ms-3 me-3" aria-label="main">
                <a class="{{ $active('site.home') }}" href="{{ route('site.home') }}">{{ __('web.nav.home') }}</a>
                <a class="{{ $active('site.shop', 'site.product') }}" href="{{ route('site.shop') }}">{{ __('web.nav.shop') }}</a>
                <a class="{{ $active('site.stores', 'site.store') }}" href="{{ route('site.stores') }}">{{ __('web.nav.stores') }}</a>
                <a class="{{ $active('site.contact') }}" href="{{ route('site.contact') }}">{{ __('web.nav.contact') }}</a>
            </nav>

            <form class="header-search desktop js-search-form d-none d-lg-block" role="search">
                <input class="form-control" name="q" type="search" maxlength="100" placeholder="{{ __('web.nav.search_placeholder') }}" aria-label="{{ __('web.common.search') }}">
                <button type="submit" aria-label="{{ __('web.common.search') }}"><i class="bi bi-search"></i></button>
            </form>

            <div class="header-actions">
                {{-- language --}}
                <a class="icon-btn lang-switch" href="{{ url('/change-language/' . $other) }}" title="{{ __('web.nav.language') }}">{{ $isAr ? 'EN' : 'ع' }}</a>

                {{-- currency (filled by JS from /api/currencies) --}}
                <div class="dropdown">
                    <button class="icon-btn lang-switch" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('web.nav.currency') }}">
                        <span class="js-currency-label">EGP</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end js-currency-menu"></ul>
                </div>

                {{-- notifications (shown by JS only when the notifications module exists) --}}
                <a class="icon-btn js-bell d-none" href="{{ route('site.account.notifications') }}" title="{{ __('web.nav.notifications') }}">
                    <i class="bi bi-bell"></i><span class="count-badge js-bell-count d-none">0</span>
                </a>

                {{-- cart --}}
                <a class="icon-btn" href="{{ route('site.cart') }}" title="{{ __('web.nav.cart') }}">
                    <i class="bi bi-bag"></i><span class="count-badge js-cart-count d-none">0</span>
                </a>

                {{-- guest --}}
                <span class="js-guest d-none d-sm-inline-flex gap-1">
                    <a class="btn btn-soft btn-sm" href="{{ route('site.login') }}">{{ __('web.nav.login') }}</a>
                    <a class="btn btn-brand btn-sm" href="{{ route('site.register') }}">{{ __('web.nav.register') }}</a>
                </span>

                {{-- logged in --}}
                <div class="dropdown js-member d-none">
                    <button class="icon-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('web.nav.account') }}"><i class="bi bi-person-circle"></i></button>
                    <ul class="dropdown-menu dropdown-menu-end" style="min-width:230px">
                        <li class="px-3 py-2 fw-bold js-user-name"></li>
                        <li><hr class="dropdown-divider"></li>
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
            </div>
        </div>

        {{-- the search box on small screens --}}
        <form class="header-search js-search-form d-lg-none pb-3" style="max-width:none" role="search">
            <input class="form-control" name="q" type="search" maxlength="100" placeholder="{{ __('web.nav.search_placeholder') }}" aria-label="{{ __('web.common.search') }}">
            <button type="submit" style="top:calc(50% - 8px)" aria-label="{{ __('web.common.search') }}"><i class="bi bi-search"></i></button>
        </form>
    </div>
</header>
