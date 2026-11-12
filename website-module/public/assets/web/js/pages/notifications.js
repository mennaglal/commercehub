/* My notifications. Needs the notifications module on the server; without it the page says so. */
Shop.pages['account-notifications'] = function () {
    if (!Shop.auth.require()) { return; }

    const $list = $('#notifications-list');
    const icons = { order: 'bi-bag-check', return: 'bi-arrow-repeat', store: 'bi-shop', broadcast: 'bi-megaphone', contact: 'bi-envelope' };

    const load = (page) => {
        Shop.api.get('/notifications', { page: page || 1 }).then((res) => {
            if (!res.data.items.length) {
                $list.html(Shop.ui.empty('bi-bell', Shop.t('notifications.none'), ''));
                $('#notifications-pagination').empty();

                return;
            }

            $list.html('<div class="card-soft overflow-hidden">' + res.data.items.map((item) =>
                '<div class="notice ' + (item.read ? '' : 'unread') + '" data-id="' + Shop.esc(item.id) + '" data-url="' + Shop.esc(item.url || '') + '" style="cursor:pointer">'
                + '<div class="ico"><i class="bi ' + (icons[item.type] || 'bi-bell') + '"></i></div><div class="flex-fill"><div class="fw-bold">' + Shop.esc(item.title) + '</div>'
                + '<div class="text-muted">' + Shop.esc(item.body) + '</div><small class="text-muted">' + Shop.esc(Shop.date(item.created_at)) + '</small></div></div>').join('') + '</div>');

            Shop.ui.pagination($('#notifications-pagination'), res.data.meta, load);
        }).catch((error) => {
            $list.html(Shop.ui.empty('bi-bell-slash', error.status === 404 ? Shop.t('notifications.unavailable') : error.message, ''));
        });
    };

    // A click marks the notification as read and opens its link (only a real web link).
    $list.on('click', '.notice', function () {
        const $item = $(this);
        const url = String($item.data('url'));

        Shop.api.post('/notifications/' + encodeURIComponent($item.data('id')) + '/read').then(() => {
            $item.removeClass('unread');
            $('.js-bell-count').text(Math.max(0, (parseInt($('.js-bell-count').first().text(), 10) || 1) - 1));
        }).catch(() => {});

        if (/^https?:\/\//i.test(url)) {
            window.open(url, '_blank', 'noopener');
        }
    });

    $('#notifications-read-all').on('click', function () {
        Shop.api.post('/notifications/read-all').then(() => { $('.notice').removeClass('unread'); $('.js-bell-count').addClass('d-none'); }).catch(Shop.ui.error);
    });

    load(1);
};
