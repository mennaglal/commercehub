<nav class="topnav navbar navbar-light">
    <button type="button" class="navbar-toggler text-muted mt-2 p-0 mr-3 collapseSidebar"><i class="fe fe-menu navbar-toggler-icon"></i></button>
    <ul class="nav align-items-center ml-auto">
        <li class="nav-item"><a class="nav-link text-muted" href="{{ route('change-language', app()->getLocale() === 'ar' ? 'en' : 'ar') }}"><i class="fe fe-globe"></i> {{ strtoupper(app()->getLocale() === 'ar' ? 'EN' : 'AR') }}</a></li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-muted" href="#" data-toggle="dropdown"><i class="fe fe-user"></i> {{ auth()->user()->name }}</a>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="{{ route('vendor.profile.edit') }}">{{ __('My Profile') }}</a>
                <form method="POST" action="{{ route('vendor.logout') }}">@csrf<button class="dropdown-item" type="submit">{{ __('dashboard.logout') }}</button></form>
            </div>
        </li>
    </ul>
</nav>
