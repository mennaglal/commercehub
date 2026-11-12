@php
    $isAr = app()->getLocale() === 'ar';
    $site = config('website');
    $siteName = $site['name'][$isAr ? 'ar' : 'en'];
    $version = $site['version'];
    $pageTitle = trim($__env->yieldContent('title'));
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isAr ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1D3557">

    <title>{{ $pageTitle ? $pageTitle . ' | ' . $siteName : $siteName . ' - ' . __('web.brand.tagline') }}</title>
    <meta name="description" content="@yield('description', __('web.brand.description'))">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $pageTitle ?: $siteName }}">
    <meta property="og:description" content="@yield('description', __('web.brand.description'))">
    <meta property="og:image" content="@yield('image', asset($site['logo']))">
    <link rel="icon" href="{{ asset($site['logo']) }}" type="image/svg+xml">

    {{-- The fonts, Bootstrap (RTL build for Arabic) and the icons. You can download them and serve them from public/ if you prefer. --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap{{ $isAr ? '.rtl' : '' }}.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('assets/web/css/site.css') }}?v={{ $version }}" rel="stylesheet">
    @stack('head')
</head>
<body class="lang-{{ app()->getLocale() }}" data-page="@yield('page')">

    @include('web.partials.header')

    <main id="main">
        @yield('content')
    </main>

    @include('web.partials.footer')
    @include('web.partials.mobile-nav')

    <button type="button" class="btn btn-brand btn-icon back-top" aria-label="Top"><i class="bi bi-arrow-up"></i></button>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // What the JavaScript needs from the server: the language, the texts (the same lang/xx/web.php) and the settings.
        window.APP = {
            locale: @json(app()->getLocale()),
            apiBase: '/api',
            placeholder: @json(asset('assets/web/img/placeholder.svg')),
            t: @json(__('web')),
            config: {
                siteName: @json($siteName),
                defaultCurrency: @json($site['default_currency']),
                paymentMethods: @json($site['payment_methods']),
                gateways: @json($site['gateways']),
            },
        };
    </script>
    <script src="{{ asset('assets/web/js/core.js') }}?v={{ $version }}"></script>
    @stack('scripts')
</body>
</html>
