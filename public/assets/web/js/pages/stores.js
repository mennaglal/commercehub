/* Stores list: search + pagination. */
Shop.pages.stores = function () {
    const state = { q: Shop.qs.get().q || '', page: 1 };
    const $grid = $('#stores-grid');

    const load = () => {
        $grid.html(Shop.ui.skeletonCards(8));

        Shop.api.get('/stores', { q: state.q, page: state.page, per_page: 12 }).then((res) => {
            if (!res.data.items.length) {
                $grid.html('<div class="col-12">' + Shop.ui.empty('bi-shop', Shop.t('stores.none'), '') + '</div>');
                $('#stores-pagination').empty();

                return;
            }

            $grid.html(res.data.items.map(Shop.ui.storeCard).join(''));
            Shop.ui.pagination($('#stores-pagination'), res.data.meta, (page) => { state.page = page; load(); });
        }).catch((error) => $grid.html('<div class="col-12">' + Shop.ui.empty('bi-exclamation-circle', error.message, '') + '</div>'));
    };

    $('#stores-search').val(state.q).closest('form').on('submit', function (event) {
        event.preventDefault();
        state.q = $.trim($('#stores-search').val());
        state.page = 1;
        Shop.qs.set({ q: state.q });
        load();
    });

    load();
};

/* One store: its banner, its info and its products. */
Shop.pages.store = function () {
    const slug = $('#store-root').data('slug');
    const state = { sort: 'newest', page: 1 };
    let storeId = null;

    const loadProducts = () => {
        const $grid = $('#store-products');
        $grid.html(Shop.ui.skeletonCards(8));

        Shop.api.get('/products', { store_id: storeId, sort: state.sort, page: state.page, per_page: 12 }).then((res) => {
            if (!res.data.items.length) {
                $grid.html('<div class="col-12">' + Shop.ui.empty('bi-box-seam', Shop.t('stores.no_products'), '') + '</div>');
                $('#store-pagination').empty();

                return;
            }

            $grid.html(res.data.items.map(Shop.ui.productCard).join(''));
            Shop.currency.render($grid);
            Shop.ui.pagination($('#store-pagination'), res.data.meta, (page) => { state.page = page; loadProducts(); });
        });
    };

    Shop.api.get('/stores/' + encodeURIComponent(slug)).then((res) => {
        const store = res.data;
        storeId = store.id;

        const banner = store.banner_url ? ' style="background-image:url(\'' + encodeURI(store.banner_url).replace(/'/g, '%27') + '\');background-size:cover;background-position:center"' : '';
        const where = [store.area, store.city, store.country].filter(Boolean).join(', ');

        $('#store-head').html('<div class="card-soft overflow-hidden"><div style="height:140px;background:linear-gradient(120deg,var(--secondary-color),#2c4f80)"' + banner + '></div>'
            + '<div class="p-4 d-flex gap-3 align-items-center flex-wrap"><img class="avatar" style="margin-top:-60px;border-radius:22px;width:96px;height:96px" src="' + Shop.esc(Shop.img(store.logo_url)) + '" alt="">'
            + '<div class="flex-fill"><h1 class="h3 mb-1">' + Shop.esc(store.name) + '</h1>'
            + (store.description ? '<p class="text-muted mb-1">' + Shop.esc(store.description) + '</p>' : '')
            + '<small class="text-muted"><i class="bi bi-box-seam"></i> ' + Shop.esc(Shop.t('stores.products_count', { count: store.products_count || 0 })) + '</small>'
            + (where ? ' <small class="text-muted ms-3"><i class="bi bi-geo-alt"></i> ' + Shop.esc(where) + '</small>' : '') + '</div></div></div>');

        loadProducts();
    }).catch((error) => $('#store-root').html(Shop.ui.empty('bi-shop', error.status === 404 ? Shop.t('stores.not_found') : error.message, '')));

    $('#store-sort').on('change', function () { state.sort = $(this).val(); state.page = 1; if (storeId) { loadProducts(); } });
};
