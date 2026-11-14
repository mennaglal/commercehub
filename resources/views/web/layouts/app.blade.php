@php($isAr = app()->getLocale() === 'ar')
@php($enabledGateways = collect(config('orders.gateways', []))->filter(fn ($g) => $g['enabled'] ?? false)->map(fn ($g) => $g['name'])->all())
<!doctype html>
<html lang="{{ $isAr ? 'ar' : 'en' }}" dir="{{ $isAr ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0f172a">
    <title>@yield('title', config('app.name', 'CommerceHub')) | CommerceHub</title>
    <meta name="description" content="@yield('description', 'CommerceHub — discover products from trusted stores.')">
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@500;600;700;800&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&family=Manrope:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/nexus/vendor/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/nexus/vendor/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/nexus/css/nexus.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/nexus/css/commercehub.css') }}">
    @stack('head')
</head>
<body class="nx-body d-flex flex-column min-vh-100" data-page="@yield('page', 'home')" data-locale="{{ app()->getLocale() }}">
<header id="nx-header-root">
    <div class="nx-topbar-dark"><div class="container-xl d-flex justify-content-between align-items-center py-2 gap-3">
        <span class="small text-white-50">{{ $isAr ? 'تسوق بثقة من متاجر موثوقة' : 'Shop confidently from trusted stores' }}</span>
        <div class="d-flex align-items-center gap-3"><a class="nx-util-btn" href="{{ url('/change-language/'.($isAr ? 'en' : 'ar')) }}"><i class="bi bi-globe2"></i> {{ $isAr ? 'English' : 'العربية' }}</a><a class="nx-util-btn" href="{{ route('site.contact') }}">{{ $isAr ? 'تواصل معنا' : 'Contact' }}</a></div>
    </div></div>
    <div class="nx-sticky-nav">
        <div class="nx-main-header"><div class="container-xl d-flex align-items-center justify-content-between gap-3 py-3">
            <a href="{{ route('site.home') }}" class="nx-brand-logo text-decoration-none">Commerce<span>Hub</span></a>
            <nav class="d-none d-lg-flex align-items-center gap-4"><a class="nx-center-link" href="{{ route('site.home') }}">{{ $isAr ? 'الرئيسية' : 'Home' }}</a><a class="nx-center-link" href="{{ route('site.shop') }}">{{ $isAr ? 'المتجر' : 'Shop' }}</a><a class="nx-center-link" href="{{ url('/categories') }}">{{ $isAr ? 'الأقسام' : 'Categories' }}</a><a class="nx-center-link" href="{{ route('site.stores') }}">{{ $isAr ? 'المتاجر' : 'Stores' }}</a><a class="nx-center-link" href="{{ url('/deals') }}">{{ $isAr ? 'العروض' : 'Deals' }}</a></nav>
            <div class="d-flex align-items-center gap-2">
                <form class="nx-inline-search d-none d-md-flex" action="{{ route('site.shop') }}"><i class="bi bi-search"></i><input class="nx-inline-search-input" name="q" placeholder="{{ $isAr ? 'ابحث عن منتج...' : 'Search products...' }}"><button class="btn btn-link p-0" aria-label="Search"><i class="bi bi-arrow-left"></i></button></form>
                <a class="nx-icon-btn" href="{{ url('/saved') }}" aria-label="Wishlist"><i class="bi bi-heart"></i></a>
                <a class="nx-icon-btn position-relative" href="{{ route('site.cart') }}" aria-label="Cart"><i class="bi bi-bag"></i><span class="nx-count-badge bg-dark-badge" id="nx-cart-count">0</span></a>
                <a class="nx-icon-btn" href="{{ route('site.account') }}" aria-label="Account"><i class="bi bi-person"></i></a>
                <button class="nx-icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#nxMobileMenu"><i class="bi bi-list"></i></button>
            </div>
        </div></div>
        <div class="nx-category-bar d-none d-md-block"><div class="container-xl d-flex align-items-center justify-content-between py-2"><nav id="nx-category-links" class="d-flex gap-4 overflow-auto"></nav><a href="{{ url('/deals') }}" class="nx-deals-link text-decoration-none">{{ $isAr ? 'عروض حصرية' : 'Exclusive deals' }} <i class="bi bi-arrow-up-right"></i></a></div></div>
    </div>
</header>
<div class="offcanvas offcanvas-end" tabindex="-1" id="nxMobileMenu"><div class="offcanvas-header"><strong class="nx-brand-logo">Commerce<span>Hub</span></strong><button class="btn-close" data-bs-dismiss="offcanvas"></button></div><div class="offcanvas-body d-grid gap-3"><a href="{{ route('site.home') }}">{{ $isAr ? 'الرئيسية' : 'Home' }}</a><a href="{{ route('site.shop') }}">{{ $isAr ? 'المتجر' : 'Shop' }}</a><a href="{{ url('/categories') }}">{{ $isAr ? 'الأقسام' : 'Categories' }}</a><a href="{{ route('site.stores') }}">{{ $isAr ? 'المتاجر' : 'Stores' }}</a><a href="{{ route('site.cart') }}">{{ $isAr ? 'السلة' : 'Cart' }}</a><a href="{{ route('site.account') }}">{{ $isAr ? 'حسابي' : 'My account' }}</a></div></div>
<main id="nx-main" class="container-xl py-4 py-md-5 flex-grow-1 nx-page-enter">@yield('content')</main>
<footer class="nx-footer mt-auto"><div class="container-xl py-5"><div class="row g-4"><div class="col-lg-4"><div class="nx-brand-logo text-white mb-3">Commerce<span>Hub</span></div><p class="text-white-50">{{ $isAr ? 'منصة تجارة إلكترونية تجمع العملاء بالمتاجر والمنتجات المميزة.' : 'A connected marketplace bringing customers, stores and great products together.' }}</p></div><div class="col-6 col-lg-2"><h6>{{ $isAr ? 'تسوق' : 'Explore' }}</h6><a href="{{ route('site.shop') }}">{{ $isAr ? 'كل المنتجات' : 'All products' }}</a><a href="{{ route('site.stores') }}">{{ $isAr ? 'المتاجر' : 'Stores' }}</a><a href="{{ url('/deals') }}">{{ $isAr ? 'العروض' : 'Deals' }}</a></div><div class="col-6 col-lg-2"><h6>{{ $isAr ? 'حسابك' : 'Your account' }}</h6><a href="{{ route('site.login') }}">{{ $isAr ? 'تسجيل الدخول' : 'Sign in' }}</a><a href="{{ route('site.account.orders') }}">{{ $isAr ? 'طلباتي' : 'Orders' }}</a><a href="{{ route('site.become-vendor') }}">{{ $isAr ? 'كن بائعاً' : 'Become a seller' }}</a></div><div class="col-lg-4"><h6>{{ $isAr ? 'مساعدة' : 'Need help?' }}</h6><a href="{{ route('site.contact') }}">{{ $isAr ? 'اتصل بنا' : 'Contact us' }}</a></div></div><hr class="border-secondary my-4"><div class="small text-white-50">© {{ date('Y') }} CommerceHub. {{ $isAr ? 'جميع الحقوق محفوظة.' : 'All rights reserved.' }}</div></div></footer>
<nav class="nx-mobile-dock d-md-none"><a href="{{ route('site.home') }}"><i class="bi bi-house"></i><small>{{ $isAr ? 'الرئيسية' : 'Home' }}</small></a><a href="{{ route('site.shop') }}"><i class="bi bi-grid"></i><small>{{ $isAr ? 'المتجر' : 'Shop' }}</small></a><a href="{{ route('site.cart') }}"><i class="bi bi-bag"></i><small>{{ $isAr ? 'السلة' : 'Bag' }}</small></a><a href="{{ route('site.account') }}"><i class="bi bi-person"></i><small>{{ $isAr ? 'حسابي' : 'Account' }}</small></a></nav>
<div class="toast-container position-fixed bottom-0 end-0 p-3"><div class="toast text-bg-dark" id="nxToast"><div class="d-flex"><div class="toast-body" id="nxToastBody"></div><button class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div></div>
<script src="{{ asset('assets/nexus/vendor/bootstrap.bundle.min.js') }}"></script>
<script>window.CHHUB={locale:@json(app()->getLocale()),api:'/api',routes:{home:@json(route('site.home')),shop:@json(route('site.shop')),cart:@json(route('site.cart')),checkout:@json(route('site.checkout')),login:@json(route('site.login')),productBase:@json(url('/products'))},page:@json($__env->yieldContent('page')),paymentMethods:@json(config('website.payment_methods', [])),gateways:@json($enabledGateways),currency:@json(config('website.default_currency','EGP'))};</script>
<script src="{{ asset('assets/nexus/js/storefront.js') }}"></script>
@stack('scripts')
</body></html>
