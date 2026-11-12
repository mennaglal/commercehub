/* Returns: my list, one return, and the form to request a return of an order. */
Shop.pages['account-returns'] = function () {
    if (!Shop.auth.require()) { return; }

    const $list = $('#returns-list');

    const load = (page) => {
        Shop.api.get('/returns', { page: page || 1, per_page: 10 }).then((res) => {
            if (!res.data.items.length) {
                $list.html(Shop.ui.empty('bi-arrow-repeat', Shop.t('returns.none'), Shop.t('returns.none_text')));
                $('#returns-pagination').empty();

                return;
            }

            $list.html(res.data.items.map((row) =>
                '<a class="card-soft pad d-block mb-3 text-dark" href="/account/returns/' + encodeURIComponent(row.return_number) + '"><div class="d-flex flex-wrap justify-content-between gap-2 align-items-center">'
                + '<div><div class="fw-bold">' + Shop.esc(row.return_number) + '</div><small class="text-muted">' + Shop.esc(Shop.t('returns.for_order')) + ' ' + Shop.esc(row.order_number) + ' &middot; ' + Shop.esc(Shop.date(row.created_at)) + '</small></div>'
                + Shop.ui.statusBadge('return', row.status) + '<div class="fw-bold" style="color:var(--brand)">' + Shop.currency.html(row.refund_amount) + '</div></div></a>').join(''));

            Shop.currency.render($list);
            Shop.ui.pagination($('#returns-pagination'), res.data.meta, load);
        }).catch(Shop.ui.error);
    };

    load(1);
};

Shop.pages['account-return'] = function () {
    if (!Shop.auth.require()) { return; }

    const $root = $('#return-root');

    Shop.api.get('/returns/' + encodeURIComponent($root.data('return'))).then((res) => {
        const row = res.data;

        $root.html('<div class="d-flex flex-wrap justify-content-between gap-3 align-items-center mb-3"><div><h1 class="h3 mb-1">' + Shop.esc(row.return_number) + '</h1>'
            + '<small class="text-muted">' + Shop.esc(Shop.t('returns.for_order')) + ' <a href="/account/orders/' + encodeURIComponent(row.order_number) + '">' + Shop.esc(row.order_number) + '</a> &middot; ' + Shop.esc(Shop.date(row.created_at)) + '</small></div>'
            + Shop.ui.statusBadge('return', row.status) + '</div>'
            + (row.rejection_reason ? '<div class="alert alert-danger"><strong>' + Shop.esc(Shop.t('returns.rejected_reason')) + ':</strong> ' + Shop.esc(row.rejection_reason) + '</div>' : '')
            + '<div class="card-soft pad mb-3"><div class="row g-3"><div class="col-md-6"><strong>' + Shop.esc(Shop.t('returns.reason')) + ':</strong> ' + Shop.esc(Shop.t('returns.reasons.' + row.reason)) + '</div>'
            + '<div class="col-md-6"><strong>' + Shop.esc(Shop.t('returns.refund')) + ':</strong> ' + Shop.currency.html(row.refund_amount) + (row.refunded_at ? ' <small class="text-muted">(' + Shop.esc(Shop.date(row.refunded_at)) + ')</small>' : '') + '</div>'
            + (row.customer_note ? '<div class="col-12"><strong>' + Shop.esc(Shop.t('returns.note')) + ':</strong> ' + Shop.esc(row.customer_note) + '</div>' : '') + '</div></div>'
            + '<div class="card-soft table-card"><table class="table mb-0"><thead><tr><th>' + Shop.esc(Shop.t('orders.items')) + '</th><th>' + Shop.esc(Shop.t('returns.qty')) + '</th><th>' + Shop.esc(Shop.t('returns.refund')) + '</th></tr></thead><tbody>'
            + row.items.map((item) => '<tr><td>' + Shop.esc(item.product_name) + (item.variant_label ? ' <small class="text-muted">(' + Shop.esc(item.variant_label) + ')</small>' : '') + '</td><td>' + item.quantity + '</td><td>' + Shop.currency.html(item.refund_amount) + '</td></tr>').join('')
            + '</tbody></table></div>');

        Shop.currency.render($root);
    }).catch((error) => $root.html(Shop.ui.empty('bi-arrow-repeat', error.status === 404 ? Shop.t('returns.not_found') : error.message, '')));
};

/* The form: how many pieces of each item come back. The refund is calculated by the server. */
Shop.pages['account-return-new'] = function () {
    if (!Shop.auth.require()) { return; }

    const number = $('#return-new-root').data('order');
    const $form = $('#return-form');

    $('#return-reason').html(['defective', 'wrong_item', 'not_as_described', 'changed_mind', 'other'].map((reason) =>
        '<option value="' + reason + '">' + Shop.esc(Shop.t('returns.reasons.' + reason)) + '</option>').join(''));

    Shop.api.get('/orders/' + encodeURIComponent(number) + '/returnable-items').then((res) => {
        const lines = res.data;

        $('#return-items').html(lines.map((line) =>
            '<tr><td>' + Shop.esc(line.product_name) + (line.variant_label ? ' <small class="text-muted">(' + Shop.esc(line.variant_label) + ')</small>' : '') + '</td>'
            + '<td>' + line.ordered_quantity + '</td><td>' + line.remaining_quantity + '</td><td>' + Shop.currency.html(line.refund_per_piece) + '</td>'
            + '<td style="width:120px"><input type="number" class="form-control" min="0" max="' + line.remaining_quantity + '" value="0" data-item="' + line.order_item_id + '" ' + (line.remaining_quantity < 1 ? 'disabled' : '') + '></td></tr>').join(''));

        Shop.currency.render($('#return-items'));
    }).catch((error) => {
        // Not delivered, or the return period is over: the server says why.
        $form.addClass('d-none');
        $('#return-error').removeClass('d-none').text(error.errors && error.errors.items ? [].concat(error.errors.items)[0] : error.message);
    });

    $form.on('submit', function (event) {
        event.preventDefault();

        const items = $('#return-items [data-item]').map(function () {
            return { order_item_id: parseInt($(this).data('item'), 10), quantity: parseInt($(this).val(), 10) || 0 };
        }).get().filter((row) => row.quantity > 0);

        if (!items.length) {
            Shop.ui.toast(Shop.t('returns.choose_items'), 'error');

            return;
        }

        const $button = $form.find('[type="submit"]');
        Shop.ui.busy($button, true);

        Shop.api.post('/orders/' + encodeURIComponent(number) + '/returns', {
            reason: $('#return-reason').val(), customer_note: $form.find('[name="customer_note"]').val() || null, items,
        }).then((res) => {
            Shop.flash(Shop.t('returns.sent'), 'success');
            window.location.href = '/account/returns/' + encodeURIComponent(res.data.return_number);
        }).catch((error) => {
            Shop.ui.busy($button, false);

            if (error.status === 422) {
                const first = Object.values(error.errors || {})[0];
                Shop.ui.toast(first ? [].concat(first)[0] : error.message, 'error');
            } else {
                Shop.ui.error(error);
            }
        });
    });
};
