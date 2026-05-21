<body class="vertical  light @if(app()->getLocale() == "ar") rtl @endif">
<div class="wrapper">
    <nav class="topnav navbar navbar-light">
        <button type="button" class="navbar-toggler text-muted mt-2 p-0 mr-3 collapseSidebar">
            <i class="fe fe-menu navbar-toggler-icon"></i>
        </button>
        <ul class="nav">
            @if(app()->getLocale() == "ar")
                <li class="nav-item">
                    <a class="nav-link text-muted my-2" href="{{ route('change-language','en') }}" >
                        <i class="fe fe-globe fe-16"></i>
                    </a>
                </li>
            @else
                <li class="nav-item">
                    <a class="nav-link text-muted my-2" href="{{route('change-language','ar')}}" >
                        <i class="fe fe-globe fe-16"></i>
                    </a>
                </li>
            @endif
                <li class="nav-item nav-notif">
                    <a class="nav-link text-muted my-2" href="./#" data-toggle="modal" data-target=".modal-notif">
                        <span class="fe fe-bell fe-16"></span>
                        <span class="dot dot-md bg-success"></span>
                    </a>
                </li>
            <li class="nav-item">
                <a class="nav-link text-muted my-2" href="#" id="modeSwitcher" data-mode="light">
                    <i class="fe fe-sun fe-16"></i>
                </a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-muted pr-0" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <span class="avatar avatar-sm mt-2">
                <img src="{{asset('dashboard')}}/assets/avatars/face-1.jpg" alt="..." class="avatar-img rounded-circle">
              </span>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                    <a class="dropdown-item" href="{{route('admin.logout')}}">@lang('dashboard.logout')</a>
                </div>
            </li>
        </ul>
    </nav>

