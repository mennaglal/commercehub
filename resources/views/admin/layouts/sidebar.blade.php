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
                    'permission' => 'admins.view',
                    'items' => [
                        [
                            'route' => 'admin.admins.index',
                            'text' => __('dashboard.Show-Admins'),
                            'permission' => 'admins.view',
                        ],
                        [
                            'route' => 'admin.admins.create',
                            'text' => __('dashboard.Add-Admin'),
                            'permission' => 'admins.create',
                        ],
                    ],
                ],
                [
                    'id' => 'users',
                    'icon' => 'fe fe-user',
                    'label' => __('dashboard.Users'),
                    'permission' => 'users.view',
                    'items' => [
                        [
                            'route' => 'admin.users.index',
                            'text' => __('dashboard.Show-Users'),
                            'permission' => 'users.view',
                        ],
                        [
                            'route' => 'admin.users.create',
                            'text' => __('dashboard.Add-User'),
                            'permission' => 'users.create',
                        ],
                    ],
                ],
                [
                    'id' => 'vendors',
                    'icon' => 'fe fe-shopping-bag',
                    'label' => __('dashboard.Vendors'),
                    'permission' => 'vendors.view',
                    'items' => [
                        [
                            'route' => 'admin.vendors.index',
                            'text' => __('dashboard.Show-Vendors'),
                            'permission' => 'vendors.view',
                        ],
                        [
                            'route' => 'admin.vendors.create',
                            'text' => __('dashboard.Add-Vendor'),
                            'permission' => 'vendors.create',
                        ],
                    ],
                ],

                [
                    'id' => 'categories',
                    'icon' => 'fe fe-grid',
                    'label' => __('dashboard.Categories'),
                    'permission' => 'categories.view',
                    'items' => [
                        [
                            'route' => 'admin.categories.index',
                            'text' => __('dashboard.Show-Categories'),
                            'permission' => 'categories.view',
                        ],
                        [
                            'route' => 'admin.categories.create',
                            'text' => __('dashboard.Add-Category'),
                            'permission' => 'categories.create',
                        ],
                    ],
                ],
                [
                    'id' => 'sub-categories',
                    'icon' => 'fe fe-list',
                    'label' => __('dashboard.Sub-Categories'),
                    'permission' => 'categories.view',
                    'items' => [
                        [
                            'route' => 'admin.sub-categories.index',
                            'text' => __('dashboard.Show-Sub-Categories'),
                            'permission' => 'categories.view',
                        ],
                        [
                            'route' => 'admin.sub-categories.create',
                            'text' => __('dashboard.Add-Sub-Category'),
                            'permission' => 'categories.create',
                        ],
                    ],
                ],

                [
                    'id' => 'variant-types',
                    'icon' => 'fe fe-sliders',
                    'label' => __('dashboard.variant_types.title'),
                    'permission' => 'variant-types.view',
                    'items' => [
                        [
                            'route' => 'admin.variant-types.index',
                            'text' => __('dashboard.variant_types.title'),
                            'permission' => 'variant-types.view',
                        ],
                        [
                            'route' => 'admin.variant-types.create',
                            'text' => __('dashboard.variant_types.add'),
                            'permission' => 'variant-types.create',
                        ],
                    ],
                ],
                [
                    'id' => 'products',
                    'icon' => 'fe fe-box',
                    'label' => __('dashboard.Products'),
                    'permission' => 'products.view',
                    'items' => [
                        [
                            'route' => 'admin.products.index',
                            'text' => __('dashboard.Show-Products'),
                            'permission' => 'products.view',
                        ],
                        [
                            'route' => 'admin.products.create',
                            'text' => __('dashboard.Add-Product'),
                            'permission' => 'products.create',
                        ],
                    ],
                ],

                [
                    'id' => 'currencies',
                    'icon' => 'fe fe-dollar-sign',
                    'label' => __('dashboard.Currencies'),
                    'permission' => 'currencies.view',
                    'items' => [
                        [
                            'route' => 'admin.currencies.index',
                            'text' => __('dashboard.Show-Currencies'),
                            'permission' => 'currencies.view',
                        ],
                        [
                            'route' => 'admin.currencies.create',
                            'text' => __('dashboard.Add-Currency'),
                            'permission' => 'currencies.create',
                        ],
                    ],
                ],
                [
                    'id' => 'countries',
                    'icon' => 'fe fe-globe',
                    'label' => __('dashboard.Countries'),
                    'permission' => 'countries.view',
                    'items' => [
                        [
                            'route' => 'admin.countries.index',
                            'text' => __('dashboard.Show-Countries'),
                            'permission' => 'countries.view',
                        ],
                        [
                            'route' => 'admin.countries.create',
                            'text' => __('dashboard.Add-Country'),
                            'permission' => 'countries.create',
                        ],
                    ],
                ],
                [
                    'id' => 'cities',
                    'icon' => 'fe fe-map-pin',
                    'label' => __('dashboard.Cities'),
                    'permission' => 'cities.view',
                    'items' => [
                        [
                            'route' => 'admin.cities.index',
                            'text' => __('dashboard.Show-Cities'),
                            'permission' => 'cities.view',
                        ],
                        [
                            'route' => 'admin.cities.create',
                            'text' => __('dashboard.Add-City'),
                            'permission' => 'cities.create',
                        ],
                    ],
                ],
                [
                    'id' => 'areas',
                    'icon' => 'fe fe-map',
                    'label' => __('dashboard.Areas'),
                    'permission' => 'areas.view',
                    'items' => [
                        [
                            'route' => 'admin.areas.index',
                            'text' => __('dashboard.Show-Areas'),
                            'permission' => 'areas.view',
                        ],
                        [
                            'route' => 'admin.areas.create',
                            'text' => __('dashboard.Add-Area'),
                            'permission' => 'areas.create',
                        ],
                    ],
                ],
            ];
        @endphp



        {{-- Loop Through Sections --}}
        @foreach($sections as $section)
            @php
                $sectionActive = collect($section['items'])->contains(function ($item) {
                    return request()->routeIs($item['route']);
                });

                $visibleItems = collect($section['items'])->filter(function ($item) {
                    return auth()->user()?->can($item['permission']);
                });

                $sectionVisible = auth()->user()?->can($section['permission']);
            @endphp

            @if($sectionVisible)
                <ul class="navbar-nav flex-fill w-100 mb-2">
                    <li class="nav-item dropdown {{ $sectionActive ? 'active' : '' }}"
                        id="{{ $section['id'] }}-item">

                        <a
                            href="#{{ $section['id'] }}-collapse"
                            data-toggle="collapse"
                            aria-expanded="{{ $sectionActive ? 'true' : 'false' }}"
                            class="dropdown-toggle nav-link {{ $sectionActive ? 'active' : '' }}"
                        >
                            <i class="{{ $section['icon'] }} text-primary"></i>
                            <span class="ml-3 item-text">{{ $section['label'] }}</span>
                        </a>

                        <ul
                            class="collapse list-unstyled pl-4 w-100 {{ $sectionActive ? 'show' : '' }}"
                            id="{{ $section['id'] }}-collapse"
                        >
                            @foreach($visibleItems as $item)
                                <li>
                                    <a
                                        class="nav-link pl-3 {{ request()->routeIs($item['route']) ? 'active' : '' }}"
                                        href="{{ route($item['route']) }}"
                                    >
                                <span class="ml-1 item-text">
                                    {{ $item['text'] }}
                                </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                </ul>
            @endif
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
