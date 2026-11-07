{{--
    The notification bell for the top bar. Put it inside the <ul class="nav"> of your navbar:
        @include('admin.partials.notification-bell')
    It loads the unread count and the latest notifications every minute (no page reload).
--}}
@can('notifications.view')
<li class="nav-item dropdown" id="notification-bell">
    <a class="nav-link text-muted my-2" href="#" id="notification-toggle" role="button"
       data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fe fe-bell fe-16"></i>
        <span class="badge badge-pill bg-danger bg-danger text-white" id="notification-count" style="display:none; position:absolute; top:6px; right:2px; font-size:10px;">0</span>
    </a>

    <div class="dropdown-menu dropdown-menu-right dropdown-menu-end p-0" style="width: 340px; max-width: 90vw;" aria-labelledby="notification-toggle">
        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
            <strong>{{ __('notifications.title') }}</strong>
            <a href="{{ route('admin.notifications.index') }}" class="small">{{ __('notifications.view_all') }}</a>
        </div>

        <div id="notification-list" style="max-height: 360px; overflow-y: auto;">
            <div class="text-center text-muted py-3 small">{{ __('notifications.bell_empty') }}</div>
        </div>
    </div>
</li>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const url = @json(route('admin.notifications.unread'));
        const emptyText = @json(__('notifications.bell_empty'));
        const countBadge = document.getElementById('notification-count');
        const list = document.getElementById('notification-list');

        function render(data) {
            countBadge.textContent = data.count > 99 ? '99+' : data.count;
            countBadge.style.display = data.count > 0 ? '' : 'none';

            list.innerHTML = '';

            if (!data.items.length) {
                const empty = document.createElement('div');
                empty.className = 'text-center text-muted py-3 small';
                empty.textContent = emptyText;
                list.appendChild(empty);
                return;
            }

            data.items.forEach(function (item) {
                const link = document.createElement('a');
                link.href = item.url;
                link.className = 'dropdown-item border-bottom py-2' + (item.read ? '' : ' bg-light');

                // textContent (not innerHTML): the text can never inject HTML.
                const title = document.createElement('div');
                title.className = item.read ? '' : 'font-weight-bold fw-bold';
                title.textContent = item.title;

                const body = document.createElement('div');
                body.className = 'small text-muted';
                body.style.whiteSpace = 'normal';
                body.textContent = item.body;

                const time = document.createElement('div');
                time.className = 'small text-muted';
                time.textContent = item.time;

                link.append(title, body, time);
                list.appendChild(link);
            });
        }

        function load() {
            fetch(url, {headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'}, credentials: 'same-origin'})
                .then((response) => response.ok ? response.json() : null)
                .then((data) => { if (data) { render(data); } })
                .catch(() => {});
        }

        load();
        setInterval(load, 60000);
    });
</script>
@endcan
