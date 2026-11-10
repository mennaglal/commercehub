/* The vendor area: become a vendor, dashboard, products list, orders, my store.
   The product form is in vendor-product-form.js. */

Shop.vendor = {
    // The store is not approved yet (or is suspended): the server answers 403. Show a clear message instead of the page.
    gate(error, $root) {
        if (error.status !== 403) {
            return false;
        }

        const status = error.errors && error.errors.approval_status;
        $root.html(Shop.ui.empty(status === 'rejected' ? 'bi-x-octagon' : 'bi-hourglass-split',
            Shop.t(status === 'rejected' ? 'vendor.rejected' : 'vendor.pending'), Shop.t(status === 'rejected' ? 'vendor.rejected_text' : 'vendor.pending_text'),
            '<a class="btn btn-soft mt-3" href="/vendor/store">' + Shop.esc(Shop.t('vendor.my_store')) + '</a>'));

        return true;
    },

    productName: (product) => (Shop.isAr() ? product.name_ar : product.name_en) || product.name_en || product.name_ar,
};

/* ---------------------------------------------------------------- become a vendor */
Shop.pages['become-vendor'] = function () {
    if (!Shop.auth.require()) { return; }

    if (Shop.auth.hasRole('vendor')) { window.location.href = '/vendor'; return; }

    const $form = $('#become-form');

    $form.on('submit', function (event) {
        event.preventDefault();
        const $button = $form.find('[type="submit"]');

        Shop.ui.busy($button, true);

        Shop.api.post('/become-vendor', new FormData($form[0])).then(() => Shop.auth.refresh()).then(() => {
            Shop.flash(Shop.t('vendor.created'), 'success');
            window.location.href = '/vendor';
        }).catch((error) => {
            Shop.ui.busy($button, false);
            error.status === 422 ? Shop.ui.showErrors($form, error.errors) : Shop.ui.error(error);
        });
    });
};

/* -------------------------------------------------------------------- dashboard */
Shop.pages['vendor-dashboard'] = function () {
    if (!Shop.auth.require('vendor')) { return; }

    const $root = $('#vendor-root');

    // The store first: its approval decides what the vendor can see.
    Shop.api.get('/vendor/store').then((res) => {
        const store = res.data;

        $('#vendor-store-name').text(store.name);

        if (store.approval_status !== 'approved') {
            Shop.vendor.gate({ status: 403, errors: { approval_status: store.approval_status } }, $root);

            return;
        }

        return Shop.api.get('/vendor/dashboard').then((stats) => {
            const data = stats.data;
            const card = (icon, label, value, extra) => '<div class="col-6 col-lg-3"><div class="stat-card ' + (extra || '') + '"><div class="ico"><i class="bi ' + icon + '"></i></div><div class="text-muted small">' + Shop.esc(label) + '</div><div class="num">' + value + '</div></div></div>';

            $root.html('<div class="row g-3">'
                + card('bi-cash-stack', Shop.t('vendor.sales_30'), Shop.currency.html(data.sales_last_30_days), 'good')
                + card('bi-bag-check', Shop.t('vendor.orders_30'), data.orders_last_30_days)
                + card('bi-wallet2', Shop.t('vendor.sales_total'), Shop.currency.html(data.sales_total), 'good')
                + card('bi-receipt', Shop.t('vendor.orders_total'), data.orders_total)
                + card('bi-box-seam', Shop.t('vendor.products_total'), data.products.total + ' <small class="text-muted fs-6">(' + data.products.active + ' ' + Shop.esc(Shop.t('vendor.active')) + ')</small>')
                + card('bi-exclamation-triangle', Shop.t('vendor.low_stock'), data.products.low_stock, 'warn')
                + '</div>');

            Shop.currency.render($root);
        });
    }).catch((error) => { if (!Shop.vendor.gate(error, $root)) { Shop.ui.error(error); } });
};

/* ---------------------------------------------------------------------- products */
Shop.pages['vendor-products'] = function () {
    if (!Shop.auth.require('vendor')) { return; }

    const state = { q: '', page: 1 };
    const $list = $('#vendor-products');

    const load = () => {
        $list.html('<tr><td colspan="6"><div class="skeleton" style="height:60px"></div></td></tr>');

        Shop.api.get('/vendor/products', { q: state.q, page: state.page, per_page: 10 }).then((res) => {
            if (!res.data.items.length) {
                $list.html('<tr><td colspan="6">' + Shop.ui.empty('bi-box-seam', Shop.t('vendor.no_products'), Shop.t('vendor.no_products_text')) + '</td></tr>');
                $('#vendor-pagination').empty();

                return;
            }

            $list.html(res.data.items.map((product) =>
                '<tr data-id="' + product.id + '"><td><div class="d-flex align-items-center gap-2"><img class="thumb-sm" src="' + Shop.esc(Shop.img(product.image_url)) + '" alt=""><div><div class="fw-bold">' + Shop.esc(Shop.vendor.productName(product)) + '</div><small class="text-muted">' + Shop.esc(product.sku) + '</small></div></div></td>'
                + '<td>' + Shop.currency.html(product.price) + (product.has_variants ? ' <small class="text-muted">(' + Shop.esc(Shop.t('product.from')) + ')</small>' : '') + '</td>'
                + '<td>' + product.stock + '</td>'
                + '<td><div class="form-check form-switch"><input class="form-check-input" type="checkbox" data-toggle ' + (product.status ? 'checked' : '') + '></div></td>'
                + '<td class="text-nowrap"><a class="btn btn-soft btn-sm" href="/vendor/products/' + product.id + '/edit"><i class="bi bi-pencil"></i></a> '
                + '<button class="btn btn-outline-danger btn-sm" data-delete><i class="bi bi-trash"></i></button></td></tr>').join(''));

            Shop.currency.render($list);
            Shop.ui.pagination($('#vendor-pagination'), res.data.meta, (page) => { state.page = page; load(); });
        }).catch((error) => {
            $list.html('<tr><td colspan="6"></td></tr>');

            if (!Shop.vendor.gate(error, $('#vendor-products-box'))) { Shop.ui.error(error); }
        });
    };

    $('#vendor-search').on('input', Shop.debounce(function () { state.q = $.trim($(this).val()); state.page = 1; load(); }, 400));

    // show / hide a product
    $list.on('change', '[data-toggle]', function () {
        const $input = $(this);

        Shop.api.patch('/vendor/products/' + $input.closest('tr').data('id') + '/status').then(() => Shop.ui.toast(Shop.t('vendor.saved'), 'success')).catch((error) => {
            $input.prop('checked', !$input.prop('checked'));
            Shop.ui.error(error);
        });
    });

    $list.on('click', '[data-delete]', function () {
        if (!window.confirm(Shop.t('vendor.delete_confirm'))) { return; }

        Shop.api.delete('/vendor/products/' + $(this).closest('tr').data('id')).then(() => { Shop.ui.toast(Shop.t('vendor.deleted'), 'success'); load(); }).catch(Shop.ui.error);
    });

    load();
};

/* ------------------------------------------------------------------------ orders */
Shop.pages['vendor-orders'] = function () {
    if (!Shop.auth.require('vendor')) { return; }

    const $list = $('#vendor-orders');

    const load = (page) => {
        $list.html('<div class="skeleton" style="height:110px"></div>');

        Shop.api.get('/vendor/orders', { page: page || 1, per_page: 10 }).then((res) => {
            if (!res.data.items.length) {
                $list.html(Shop.ui.empty('bi-receipt', Shop.t('vendor.no_orders'), ''));
                $('#vendor-orders-pagination').empty();

                return;
            }

            $list.html(res.data.items.map((order) =>
                '<div class="card-soft pad mb-3"><div class="d-flex flex-wrap justify-content-between gap-2 align-items-center mb-2"><div><div class="fw-bold">' + Shop.esc(order.order_number) + '</div>'
                + '<small class="text-muted">' + Shop.esc(Shop.date(order.created_at)) + ' &middot; ' + Shop.esc(order.customer_name) + '</small></div>'
                + '<div class="d-flex gap-2 flex-wrap">' + Shop.ui.statusBadge('order', order.status) + Shop.ui.statusBadge('delivery', order.delivery_status) + '</div>'
                + '<div class="fw-bold" style="color:var(--brand)">' + Shop.currency.html(order.items_total) + '</div></div>'
                + order.items.map((item) => '<div class="d-flex justify-content-between small border-top pt-2 mt-2"><span>' + Shop.esc(item.product_name) + (item.variant_label ? ' <span class="text-muted">(' + Shop.esc(item.variant_label) + ')</span>' : '') + ' x ' + item.quantity + '</span><span>' + Shop.currency.html(item.line_total) + '</span></div>').join('')
                + '</div>').join(''));

            Shop.currency.render($list);
            Shop.ui.pagination($('#vendor-orders-pagination'), res.data.meta, load);
        }).catch((error) => { if (!Shop.vendor.gate(error, $list)) { Shop.ui.error(error); } });
    };

    load(1);
};

/* ---------------------------------------------------------------------- my store */
Shop.pages['vendor-store'] = function () {
    if (!Shop.auth.require('vendor')) { return; }

    const $form = $('#store-form');

    Shop.api.get('/vendor/store').then((res) => {
        const store = res.data;

        $form.find('[name="name"]').val(store.name);
        $form.find('[name="description"]').val(store.description || '');
        $('#store-logo-img').attr('src', Shop.img(store.logo_url));
        $('#store-approval').html('<span class="status ' + ({ approved: 'success', pending: 'warning', rejected: 'danger' }[store.approval_status] || 'secondary') + '">' + Shop.esc(Shop.t('vendor.approval.' + store.approval_status)) + '</span>'
            + (store.status ? '' : ' <span class="status danger">' + Shop.esc(Shop.t('vendor.suspended')) + '</span>'));

        return Shop.location.init({ $country: $('#country_id'), $city: $('#city_id'), $area: $('#area_id'), values: { country: store.country_id, city: store.city_id, area: store.area_id } });
    }).catch(Shop.ui.error);

    $form.on('submit', function (event) {
        event.preventDefault();
        const $button = $form.find('[type="submit"]');

        Shop.ui.busy($button, true);

        // POST (not PUT): PHP does not read the files of a PUT request. The server accepts both.
        Shop.api.post('/vendor/store', new FormData($form[0])).then((res) => {
            Shop.ui.busy($button, false);
            $('#store-logo-img').attr('src', Shop.img(res.data.logo_url));
            Shop.ui.showErrors($form, {});
            Shop.ui.toast(Shop.t('vendor.saved'), 'success');
        }).catch((error) => {
            Shop.ui.busy($button, false);
            error.status === 422 ? Shop.ui.showErrors($form, error.errors) : Shop.ui.error(error);
        });
    });
};
