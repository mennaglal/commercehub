<aside class="sidebar-left border-right bg-white shadow" id="leftSidebar" data-simplebar>
    <a href="#" class="btn collapseSidebar toggle-btn d-lg-none text-muted ml-2 mt-3" data-toggle="toggle">
        <i class="fe fe-x"><span class="sr-only"></span></i>
    </a>

    <nav class="vertnav navbar navbar-light">

        <!-- Logo -->
        <div class="w-100 mb-4 d-flex">
            <a class="navbar-brand mx-auto mt-2 flex-fill text-center" href="{{ route('admin.dashboard') }}">
                <img id="logo" class="navbar-brand-img brand-sm" src="{{asset('assets/images/logo.png')}}" style="width: 3rem">

            </a>
        </div>

        <!-- Dashboard -->
        <ul class="navbar-nav flex-fill w-100 mb-2">
            <li class="nav-item w-100">
                <a class="nav-link" href="{{route('admin.dashboard')}}">
                    <i class="fe fe-home fe-16 text-primary"></i>
                    <span class="ml-3 item-text">{{ __('dashboard.dashboard') }}</span>
                </a>
            </li>
        </ul>

        {{-- Sidebar Sections --}}
        @php
            $sections = [
                [
                    'id' => 'admins',
                    'icon' => 'fe fe-users',
                    'label' => __('dashboard.Admins'),
                    'items' => [
                        ['route' => 'admin.admins.index', 'text' => __('dashboard.show-Admins')],
                        ['route' => 'admin.admins.create', 'text' => __('dashboard.Add-Admin')],
                    ],
                ],


            ];
        @endphp


        {{-- Loop Through Sections --}}
        @foreach($sections as $section)
            <ul class="navbar-nav flex-fill w-100 mb-2">
                <li class="nav-item dropdown" id="{{ $section['id'] }}-item">
                    <a href="#{{ $section['id'] }}-collapse" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle nav-link">
                        <i class="{{ $section['icon'] }} text-primary"></i>
                        <span class="ml-3 item-text">{{ $section['label'] }}</span>
                    </a>
                    <ul class="collapse list-unstyled pl-4 w-100" id="{{ $section['id'] }}-collapse">
                        @foreach($section['items'] as $item)
                            <li>
                                <a class="nav-link pl-3" href="{{ route($item['route']) }}">
                                    <span class="ml-1 item-text">{{ $item['text'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            </ul>
        @endforeach
        {{-- Collapse Memory Script --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const sections = [
                    'dashboard', 'admins',
                ];

                sections.forEach(id => {
                    const collapseId = `#${id}-collapse`;
                    const storageKey = `${id}Open`;

                    if (localStorage.getItem(storageKey) === 'true') {
                        $(collapseId).collapse('show');
                    }

                    $(collapseId).on('show.bs.collapse', function() {
                        localStorage.setItem(storageKey, 'true');
                        sections.filter(s => s !== id).forEach(s => {
                            $(`#${s}-collapse`).collapse('hide');
                            localStorage.setItem(`${s}Open`, 'false');
                        });
                    });

                    $(collapseId).on('hide.bs.collapse', function() {
                        localStorage.setItem(storageKey, 'false');
                    });
                });
            });
        </script>
    </nav>
</aside>
