@php $active = fn (string ...$names) => request()->routeIs(...$names) ? 'active' : ''; @endphp

{{-- The bottom bar of the phones (like an app) --}}
<nav class="mobile-nav" aria-label="mobile">
    <a class="{{ $active('site.home') }}" href="{{ route('site.home') }}"><i class="bi bi-house-heart"></i>{{ __('web.nav.home') }}</a>
    <a class="{{ $active('site.shop', 'site.product') }}" href="{{ route('site.shop') }}"><i class="bi bi-grid"></i>{{ __('web.nav.shop') }}</a>
    <a class="{{ $active('site.cart') }}" href="{{ route('site.cart') }}"><i class="bi bi-bag"></i>{{ __('web.nav.cart') }}<span class="count-badge js-cart-count d-none">0</span></a>
    <a class="{{ $active('site.account*', 'site.login', 'site.register') }}" href="{{ route('site.account') }}"><i class="bi bi-person"></i>{{ __('web.nav.account') }}</a>
</nav>
