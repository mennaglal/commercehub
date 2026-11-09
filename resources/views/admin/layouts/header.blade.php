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
            @can('notifications.view')
                    <li class="nav-item dropdown nav-notif">
                        <a class="nav-link dropdown-toggle text-muted my-2 position-relative"
                           href="#"
                           id="notificationsDropdown"
                           role="button"
                           data-toggle="dropdown"
                           aria-haspopup="true"
                           aria-expanded="false">

                            <i class="fe fe-bell fe-16"></i>

                            <span id="notification_count"
                                  class="badge badge-danger position-absolute"
                                  style="
                                    top: 0px;
                                    border-radius: 50%;
                                    right: 42px; display: none;
                                    ">
                                0
                            </span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right shadow"
                             aria-labelledby="notificationsDropdown"
                             style="width: 340px; max-height: 420px; overflow-y: auto;">

                            <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                                <strong>{{ __('notifications.title') }}</strong>

                                <span class="text-muted small" id="notification_total"></span>
                            </div>

                            <div id="notification_items">
                                <div class="text-center text-muted py-4">
                                    {{ __('notifications.loading') }}
                                </div>
                            </div>

                            <div class="border-top text-center py-2">
                                <a href="{{ route('admin.notifications.index') }}"
                                   class="text-primary small">
                                    {{ __('notifications.view_all') }}
                                </a>
                            </div>
                        </div>
                    </li>
                @endcan
            <li class="nav-item">
                <a class="nav-link text-muted my-2" href="#" id="modeSwitcher" data-mode="light">
                    <i class="fe fe-sun fe-16"></i>
                </a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-muted pr-0" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <span class="avatar avatar-sm mt-2">
                <img src="{{asset('dashboard')}}/assets/avatars/face-7.jpg" alt="..." class="avatar-img rounded-circle">
              </span>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                    <a class="dropdown-item" href="{{route('admin.logout')}}">@lang('dashboard.logout')</a>
                </div>
            </li>
        </ul>
    </nav>

    @can('notifications.view')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const endpoint = @json(route('admin.notifications.unread'));
                const countElement = document.getElementById('notification_count');
                const totalElement = document.getElementById('notification_total');
                const itemsElement = document.getElementById('notification_items');

                function renderNotifications(items) {
                    itemsElement.innerHTML = '';

                    if (!items.length) {
                        const empty = document.createElement('div');
                        empty.className = 'text-center text-muted py-4';
                        empty.textContent = @json(__('notifications.empty'));

                        itemsElement.appendChild(empty);
                        return;
                    }

                    items.forEach(function (item) {
                        const link = document.createElement('a');

                        link.href = item.url;
                        link.className = 'dropdown-item py-3 border-bottom';

                        if (!item.read) {
                            link.classList.add('bg-light');
                        }

                        const title = document.createElement('div');
                        title.className = 'font-weight-bold text-dark';
                        title.textContent = item.title;

                        const body = document.createElement('div');
                        body.className = 'small text-muted text-truncate';
                        body.textContent = item.body;

                        const time = document.createElement('small');
                        time.className = 'text-muted';
                        time.textContent = item.time;

                        link.appendChild(title);
                        link.appendChild(body);
                        link.appendChild(time);

                        itemsElement.appendChild(link);
                    });
                }

                function loadNotifications() {
                    fetch(endpoint, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    })
                        .then(function (response) {
                            if (!response.ok) {
                                throw new Error('Failed to load notifications');
                            }

                            return response.json();
                        })
                        .then(function (data) {
                            const count = data.count || 0;

                            countElement.textContent = count > 99 ? '99+' : count;
                            countElement.style.display = count > 0 ? '' : 'none';

                            totalElement.textContent = count;
                            renderNotifications(data.items || []);
                        })
                        .catch(function (error) {
                            console.error(error);
                        });
                }

                loadNotifications();

                document.getElementById('notificationsDropdown')
                    .addEventListener('click', loadNotifications);
            });
        </script>
@endcan
