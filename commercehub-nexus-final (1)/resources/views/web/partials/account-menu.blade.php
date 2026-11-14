@php $active = fn (string ...$names) => request()->routeIs(...$names) ? 'active' : ''; @endphp
<nav class="side-menu mb-3" aria-label="account">
    <a class="{{ $active('site.account') }}" href="{{ route('site.account') }}"><i class="bi bi-person"></i>{{ __('web.nav.profile') }}</a>
    <a class="{{ $active('site.account.orders', 'site.account.order', 'site.account.return.new') }}" href="{{ route('site.account.orders') }}"><i class="bi bi-bag-check"></i>{{ __('web.nav.orders') }}</a>
    <a class="{{ $active('site.account.returns', 'site.account.return') }}" href="{{ route('site.account.returns') }}"><i class="bi bi-arrow-repeat"></i>{{ __('web.nav.returns') }}</a>
    <a class="{{ $active('site.account.notifications') }}" href="{{ route('site.account.notifications') }}"><i class="bi bi-bell"></i>{{ __('web.nav.notifications') }}</a>
    <a class="js-vendor-only d-none" href="{{ route('site.vendor') }}"><i class="bi bi-shop"></i>{{ __('web.nav.vendor') }}</a>
    <a class="js-not-vendor" href="{{ route('site.become-vendor') }}"><i class="bi bi-shop-window"></i>{{ __('web.nav.become_vendor') }}</a>
</nav>
