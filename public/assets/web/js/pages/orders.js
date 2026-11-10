/* My orders (list) and one order (details, cancel, return). */
Shop.pages['account-orders'] = function () {
    if (!Shop.auth.require()) { return; }

    const $list = $('#orders-list');

    const load = (page) => {
        $list.html('<div class="skeleton" style="height:120px"></div>');

        Shop.api.get('/orders', { page: page || 1, per_page: 10 }).then((res) => {
            if (!res.data.items.length) {
                $list.html(Shop.ui.empty('bi-bag', Shop.t('orders.none'), Shop.t('orders.none_text'), '<a class="btn btn-brand mt-3" href="/shop">' + Shop.esc(Shop.t('cart.start_shopping')) + '</a>'));
                $('#orders-pagination').empty();

                return;
            }

            $list.html(res.data.items.map((order) =>
                '<a class="card-soft pad d-block mb-3 text-dark" href="/account/orders/' + encodeURIComponent(order.order_number) + '">'
                + '<div class="d-flex flex-wrap justify-content-between gap-2 align-items-center"><div><div class="fw-bold">' + Shop.esc(order.order_number) + '</div>'
                + '<small class="text-muted">' + Shop.esc(Shop.date(order.created_at)) + ' &middot; ' + Shop.esc(Shop.t('orders.items_count', { count: order.items_count })) + '</small></div>'
                + '<div class="d-flex flex-wrap gap-2">' + Shop.ui.statusBadge('order', order.status) + Shop.ui.statusBadge('delivery', order.delivery.status) + Shop.ui.statusBadge('payment', order.payment.status) + '</div>'
                + '<div class="fw-bold" style="color:var(--brand)">' + Shop.currency.html(order.totals.total) + '</div></div></a>').join(''));

            Shop.currency.render($list);
            Shop.ui.pagination($('#orders-pagination'), res.data.meta, load);
        }).catch(Shop.ui.error);
    };

    load(1);
};

Shop.pages['account-order'] = function () {
    if (!Shop.auth.require()) { return; }

    const number = $('#order-root').data('order');
    const $root = $('#order-root');

    const render = (order) => {
        const cancelled = order.status === 'cancelled';
        const delivery = order.delivery;
        const address = order.shipping_address;
        const where = [address.area, address.city, address.country].filter(Boolean).join(', ');

        $root.html(
            '<div class="d-flex flex-wrap justify-content-between gap-3 align-items-center mb-3"><div><h1 class="h3 mb-1">' + Shop.esc(order.order_number) + '</h1>'
            + '<small class="text-muted">' + Shop.esc(Shop.date(order.created_at)) + '</small></div>'
            + '<div class="d-flex gap-2 flex-wrap">' + Shop.ui.statusBadge('order', order.status) + Shop.ui.statusBadge('payment', order.payment.status) + '</div></div>'

            + '<div class="card-soft pad mb-3">' + (cancelled ? '<div class="text-center text-danger fw-bold py-2"><i class="bi bi-x-circle"></i> ' + Shop.esc(Shop.t('status.order.cancelled')) + '</div>'
                : delivery.status === 'returned' ? '<div class="text-center text-danger fw-bold py-2">' + Shop.esc(Shop.t('status.delivery.returned')) + '</div>' : Shop.ui.deliveryTimeline(delivery.status))
            + (delivery.tracking_number ? '<div class="text-center small"><strong>' + Shop.esc(Shop.t('orders.tracking')) + ':</strong> ' + Shop.esc(delivery.tracking_number) + '</div>' : '') + '</div>'

            + '<div class="row g-3"><div class="col-lg-8"><div class="card-soft pad"><h5 class="mb-3">' + Shop.esc(Shop.t('orders.items')) + '</h5>'
            + order.items.map((item) => '<div class="cart-line"><a href="/products/' + encodeURIComponent(item.product_slug || '') + '"><img src="' + Shop.esc(Shop.img(item.image_url)) + '" alt=""></a>'
                + '<div class="meta"><div class="fw-bold">' + Shop.esc(item.product_name) + '</div>' + (item.variant_label ? '<div class="text-muted small">' + Shop.esc(item.variant_label) + '</div>' : '')
                + '<div class="small text-muted">' + Shop.currency.html(item.unit_price) + ' x ' + item.quantity + '</div></div><div class="fw-bold">' + Shop.currency.html(item.line_total) + '</div></div>').join('')
            + '</div></div>'

            + '<div class="col-lg-4"><div class="card-soft pad mb-3"><h5 class="mb-3">' + Shop.esc(Shop.t('cart.summary')) + '</h5>'
            + '<div class="summary-row"><span>' + Shop.esc(Shop.t('cart.subtotal')) + '</span>' + Shop.currency.html(order.totals.subtotal) + '</div>'
            + (order.totals.discount > 0 ? '<div class="summary-row"><span>' + Shop.esc(Shop.t('cart.discount')) + (order.coupon_code ? ' <span class="chip">' + Shop.esc(order.coupon_code) + '</span>' : '') + '</span><span class="discount">- ' + Shop.currency.html(order.totals.discount) + '</span></div>' : '')
            + '<div class="summary-row"><span>' + Shop.esc(Shop.t('cart.shipping')) + '</span>' + Shop.currency.html(order.totals.shipping) + '</div>'
            + '<div class="summary-row total"><span>' + Shop.esc(Shop.t('cart.total')) + '</span>' + Shop.currency.html(order.totals.total) + '</div></div>'

            + '<div class="card-soft pad mb-3"><h6>' + Shop.esc(Shop.t('orders.shipping_to')) + '</h6><div class="small">' + Shop.esc(address.name) + ' &middot; ' + Shop.esc(address.phone) + '<br>' + Shop.esc(address.address) + '<br><span class="text-muted">' + Shop.esc(where) + '</span></div></div>'

            + '<div class="card-soft pad mb-3"><h6>' + Shop.esc(Shop.t('orders.payment')) + '</h6><div class="small">' + Shop.esc(Shop.t('checkout.pay.' + order.payment.method)) + (order.payment.gateway ? ' (' + Shop.esc(order.payment.gateway) + ')' : '') + '</div>'
            + (order.payment.invoice_url ? '<a class="btn btn-soft btn-sm mt-2" target="_blank" rel="noopener noreferrer" href="' + Shop.esc(order.payment.invoice_url) + '"><i class="bi bi-receipt"></i> ' + Shop.esc(Shop.t('orders.invoice')) + '</a>' : '') + '</div>'

            + '<div class="d-grid gap-2" id="order-actions"></div></div></div>');

        const $actions = $('#order-actions');

        if (order.can_return) {
            $actions.append('<a class="btn btn-outline-brand" href="/account/orders/' + encodeURIComponent(order.order_number) + '/return"><i class="bi bi-arrow-repeat"></i> ' + Shop.esc(Shop.t('orders.request_return')) + '</a>');
        }

        if (order.can_cancel) {
            $('<button type="button" class="btn btn-outline-danger"></button>').text(Shop.t('orders.cancel')).on('click', function () {
                if (!window.confirm(Shop.t('orders.cancel_confirm'))) { return; }

                Shop.api.post('/orders/' + encodeURIComponent(order.order_number) + '/cancel').then((res) => { Shop.ui.toast(Shop.t('orders.cancelled'), 'success'); render(res.data); Shop.currency.render($root); }).catch(Shop.ui.error);
            }).appendTo($actions);
        }

        Shop.currency.render($root);
    };

    Shop.api.get('/orders/' + encodeURIComponent(number)).then((res) => render(res.data)).catch((error) => {
        $root.html(Shop.ui.empty('bi-bag-x', error.status === 404 ? Shop.t('orders.not_found') : error.message, ''));
    });
};
