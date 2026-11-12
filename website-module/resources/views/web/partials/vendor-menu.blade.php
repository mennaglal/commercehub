@php $active = fn (string ...$names) => request()->routeIs(...$names) ? 'active' : ''; @endphp
<nav class="side-menu mb-3" aria-label="vendor">
    <a class="{{ $active('site.vendor') }}" href="{{ route('site.vendor') }}"><i class="bi bi-speedometer2"></i>{{ __('web.vendor.dashboard') }}</a>
    <a class="{{ $active('site.vendor.products*') }}" href="{{ route('site.vendor.products') }}"><i class="bi bi-box-seam"></i>{{ __('web.vendor.products') }}</a>
    <a class="{{ $active('site.vendor.orders') }}" href="{{ route('site.vendor.orders') }}"><i class="bi bi-receipt"></i>{{ __('web.vendor.orders') }}</a>
    <a class="{{ $active('site.vendor.store') }}" href="{{ route('site.vendor.store') }}"><i class="bi bi-shop"></i>{{ __('web.vendor.my_store') }}</a>
    <a href="{{ route('site.account') }}"><i class="bi bi-person"></i>{{ __('web.nav.account') }}</a>
</nav>
