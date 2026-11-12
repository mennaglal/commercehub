/* Shop page: search + filters + sort + pagination. The filters live in the URL (?q=&category_id=...), so a result can be shared. */
Shop.pages.shop = function () {
    const $grid = $('#shop-grid');
    const $form = $('#filters-form');
    const state = Object.assign({ q: '', category_id: '', store_id: '', min_price: '', max_price: '', on_sale: '', in_stock: '', sort: 'newest', page: 1 }, Shop.qs.get());
    let categories = [];

    // Fill the form from the state
    const fillForm = () => {
        $form.find('[name="q"]').val(state.q);
        $form.find('[name="min_price"]').val(state.min_price);
        $form.find('[name="max_price"]').val(state.max_price);
        $form.find('[name="on_sale"]').prop('checked', !!state.on_sale && state.on_sale !== '0');
        $form.find('[name="in_stock"]').prop('checked', !!state.in_stock && state.in_stock !== '0');
        $('#sort-select').val(state.sort);
    };

    // The categories tree on the side
    const renderTree = () => {
        const link = (category, extra) => '<a href="#" class="' + (String(category.id) === String(state.category_id) ? 'active' : '') + '" data-cat="' + category.id + '">' + Shop.esc(category.name) + '</a>' + (extra || '');
        const html = '<li><a href="#" class="' + (state.category_id ? '' : 'active') + '" data-cat="">' + Shop.esc(Shop.t('shop.all_categories')) + '</a></li>'
            + categories.map((category) => '<li>' + link(category,
                (category.children || []).length ? '<ul class="cat-tree sub">' + category.children.map((child) => '<li>' + link(child) + '</li>').join('') + '</ul>' : '') + '</li>').join('');

        $('#cat-tree').html(html);
    };

    // The chips of the active filters (with an x to remove each one)
    const renderChips = () => {
        const chips = [];
        const chip = (key, label) => chips.push('<span class="chip">' + Shop.esc(label) + ' <button type="button" data-clear="' + key + '" aria-label="x"><i class="bi bi-x-lg"></i></button></span>');

        if (state.q) { chip('q', '"' + state.q + '"'); }
        if (state.category_id) {
            const all = categories.reduce((list, parent) => list.concat([parent], parent.children || []), []);
            const found = all.find((category) => String(category.id) === String(state.category_id));
            chip('category_id', found ? found.name : Shop.t('shop.category'));
        }
        if (state.min_price || state.max_price) { chip('price', (state.min_price || 0) + ' - ' + (state.max_price || '∞') + ' EGP'); }
        if (state.on_sale) { chip('on_sale', Shop.t('shop.on_sale')); }
        if (state.in_stock) { chip('in_stock', Shop.t('shop.in_stock')); }
        if (state.store_id) { chip('store_id', Shop.t('shop.one_store')); }

        $('#active-filters').html(chips.join(' '));
    };

    const load = () => {
        $grid.html(Shop.ui.skeletonCards(8));
        Shop.qs.set(Object.assign({}, state, { page: state.page > 1 ? state.page : '', sort: state.sort === 'newest' ? '' : state.sort }));
        renderTree();
        renderChips();

        Shop.api.get('/products', {
            q: state.q, category_id: state.category_id, store_id: state.store_id, min_price: state.min_price, max_price: state.max_price,
            on_sale: state.on_sale ? 1 : '', in_stock: state.in_stock ? 1 : '', sort: state.sort, page: state.page, per_page: 12,
        }).then((res) => {
            const data = res.data;

            $('#shop-count').text(Shop.t('shop.results', { count: data.meta.total }));

            if (!data.items.length) {
                $grid.html('<div class="col-12">' + Shop.ui.empty('bi-search', Shop.t('shop.no_results'), Shop.t('shop.no_results_text')) + '</div>');
                $('#shop-pagination').empty();

                return;
            }

            $grid.html(data.items.map(Shop.ui.productCard).join(''));
            Shop.currency.render($grid);
            Shop.ui.pagination($('#shop-pagination'), data.meta, (page) => { state.page = page; load(); });
        }).catch((error) => {
            $grid.html('<div class="col-12">' + Shop.ui.empty('bi-exclamation-circle', error.message, '') + '</div>');
        });
    };

    // ---- events
    $form.on('submit', function (event) {
        event.preventDefault();
        state.q = $.trim($form.find('[name="q"]').val());
        state.min_price = $form.find('[name="min_price"]').val();
        state.max_price = $form.find('[name="max_price"]').val();
        state.on_sale = $form.find('[name="on_sale"]').is(':checked') ? 1 : '';
        state.in_stock = $form.find('[name="in_stock"]').is(':checked') ? 1 : '';
        state.page = 1;
        load();
    });

    $form.on('change', 'input[type="checkbox"]', () => $form.trigger('submit'));
    $('#sort-select').on('change', function () { state.sort = $(this).val(); state.page = 1; load(); });

    $('#cat-tree').on('click', '[data-cat]', function (event) {
        event.preventDefault();
        state.category_id = $(this).data('cat') || '';
        state.page = 1;
        load();
    });

    $('#active-filters').on('click', '[data-clear]', function () {
        const key = $(this).data('clear');
        (key === 'price' ? ['min_price', 'max_price'] : [key]).forEach((name) => { state[name] = ''; });
        state.page = 1;
        fillForm();
        load();
    });

    $('#filters-reset').on('click', function () {
        Object.assign(state, { q: '', category_id: '', store_id: '', min_price: '', max_price: '', on_sale: '', in_stock: '', sort: 'newest', page: 1 });
        fillForm();
        load();
    });

    // Start: the categories first (for the tree and the chips), then the products
    fillForm();
    Shop.catalog.categories().then((list) => { categories = list; }).catch(() => {}).then(load);
};
