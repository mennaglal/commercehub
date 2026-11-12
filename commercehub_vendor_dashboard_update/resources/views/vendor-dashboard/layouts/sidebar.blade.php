<aside class="sidebar-left border-right bg-white shadow" id="leftSidebar">
    <div class="p-3 mb-2"><a href="{{ route('vendor.dashboard') }}" class="h4 text-dark text-decoration-none"><i class="fe fe-shopping-bag text-primary mr-2"></i>CommerceHub</a><div class="small text-muted mt-1">{{ auth()->user()->store->name }}</div></div>
    <nav class="sidebar">
        <ul class="list-unstyled mb-0">
            @php($links = [
                ['vendor.dashboard', 'fe-home', 'Overview'],
                ['vendor.products.index', 'fe-box', 'My Products'],
                ['vendor.orders.index', 'fe-shopping-cart', 'My Orders'],
                ['vendor.returns.index', 'fe-rotate-ccw', 'My Returns'],
                ['vendor.profile.edit', 'fe-user', 'My Profile'],
            ])
            @foreach($links as [$route, $icon, $label])
                <li class="mb-1"><a class="nav-link px-3 py-2 {{ request()->routeIs($route) || ($route !== 'vendor.dashboard' && request()->routeIs(str_replace('.index', '.*', $route))) ? 'active text-primary font-weight-bold' : 'text-dark' }}" href="{{ route($route) }}"><i class="fe {{ $icon }} mr-2"></i>{{ __($label) }}</a></li>
            @endforeach
        </ul>
    </nav>
</aside>
