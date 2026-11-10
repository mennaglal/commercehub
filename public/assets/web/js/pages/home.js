/* Home page: categories, deals, new arrivals, top stores. Every block loads on its own, so one slow block never blocks the others. */
Shop.pages.home = function () {
    // Categories (main categories only)
    Shop.api.get('/categories').then((res) => {
        const html = res.data.map((category) =>
            '<a class="cat-chip" href="/shop?category_id=' + category.id + '"><div class="bubble">'
            + (category.image_url ? '<img loading="lazy" src="' + Shop.esc(category.image_url) + '" alt="">' : '<i class="bi bi-grid"></i>')
            + '</div><span>' + Shop.esc(category.name) + '</span></a>').join('');

        $('#home-categories').html(html || '');
        $('#home-categories-box').toggleClass('d-none', !res.data.length);
    }).catch(() => $('#home-categories-box').addClass('d-none'));

    const loadProducts = (selector, params) => {
        const $box = $(selector);
        $box.html(Shop.ui.skeletonCards(4));

        Shop.api.get('/products', params).then((res) => {
            const items = res.data.items;

            if (!items.length) {
                $box.closest('section').addClass('d-none');

                return;
            }

            $box.html(items.map(Shop.ui.productCard).join(''));
            Shop.currency.render($box);
        }).catch(() => $box.closest('section').addClass('d-none'));
    };

    loadProducts('#home-deals', { on_sale: 1, per_page: 8, sort: 'newest' });
    loadProducts('#home-new', { per_page: 8, sort: 'newest' });

    // Top stores
    Shop.api.get('/stores', { per_page: 4 }).then((res) => {
        if (!res.data.items.length) {
            $('#home-stores').closest('section').addClass('d-none');

            return;
        }

        $('#home-stores').html(res.data.items.map(Shop.ui.storeCard).join(''));
    }).catch(() => $('#home-stores').closest('section').addClass('d-none'));
};
