/* Product page: gallery, price, options (Color, Size ...), stock, add to cart, related products. */
Shop.pages.product = function () {
    const slug = $('#product-root').data('slug');
    const $root = $('#product-root');

    Shop.api.get('/products/' + encodeURIComponent(slug)).then((res) => render(res.data)).catch((error) => {
        $root.html(Shop.ui.empty('bi-emoji-frown', error.status === 404 ? Shop.t('product.not_found') : error.message, '', '<a class="btn btn-brand mt-3" href="/shop">' + Shop.esc(Shop.t('nav.shop')) + '</a>'));
    });

    function render(product) {
        const types = product.options || [];            // [{ type_id, type, input_type, values: [...] }]
        const variants = product.variants || [];
        const selected = {};                            // type_id => value_id
        let quantity = 1;

        // ---------- which variant matches the choice of the customer?
        const hasValue = (variant, valueId) => variant.values.some((value) => value.id === valueId);
        const current = () => (product.has_variants && Object.keys(selected).length === types.length)
            ? variants.find((variant) => types.every((type) => hasValue(variant, selected[type.type_id]))) || null
            : null;

        // A value is available when some variant that is IN STOCK has it (and fits the other choices).
        const available = (typeId, valueId) => variants.some((variant) => variant.in_stock && hasValue(variant, valueId)
            && types.every((type) => type.type_id === typeId || !selected[type.type_id] || hasValue(variant, selected[type.type_id])));

        const stockOf = () => product.has_variants ? (current() ? current().stock : null) : product.stock;

        // ---------- the page
        const category = product.category;
        const crumbs = '<nav class="crumbs"><a href="/">' + Shop.esc(Shop.t('nav.home')) + '</a><i class="bi bi-chevron-right"></i><a href="/shop">' + Shop.esc(Shop.t('nav.shop')) + '</a>'
            + (product.parent_category ? '<i class="bi bi-chevron-right"></i><a href="/shop?category_id=' + product.parent_category.id + '">' + Shop.esc(product.parent_category.name) + '</a>' : '')
            + (category ? '<i class="bi bi-chevron-right"></i><a href="/shop?category_id=' + category.id + '">' + Shop.esc(category.name) + '</a>' : '')
            + '<i class="bi bi-chevron-right"></i><span>' + Shop.esc(product.name) + '</span></nav>';

        $root.html(crumbs
            + '<div class="row g-4 g-lg-5"><div class="col-lg-6"><div class="gallery-main"><img src="' + Shop.esc(Shop.img(product.image_url)) + '" alt="' + Shop.esc(product.name) + '"></div></div>'
            + '<div class="col-lg-6">'
            + (product.store_details ? '<a href="/stores/' + encodeURIComponent(product.store_details.slug) + '" class="chip mb-2"><i class="bi bi-shop"></i> ' + Shop.esc(product.store_details.name) + '</a>' : '')
            + '<h1 class="h2 mb-2">' + Shop.esc(product.name) + '</h1>'
            + '<div class="text-muted small mb-3">SKU: <span id="p-sku">' + Shop.esc(product.sku) + '</span></div>'
            + '<div class="mb-2" id="p-price"></div><div class="mb-3" id="p-stock"></div>'
            + '<div id="p-options"></div>'
            + '<div class="d-flex flex-wrap gap-3 align-items-center my-4">'
            + '<div class="qty"><button type="button" id="q-minus" aria-label="-">&minus;</button><input type="number" id="q-input" value="1" min="1" max="99" inputmode="numeric"><button type="button" id="q-plus" aria-label="+">+</button></div>'
            + '<button type="button" class="btn btn-brand btn-lg flex-fill" id="p-add"><i class="bi bi-bag-plus me-1"></i>' + Shop.esc(Shop.t('product.add_to_cart')) + '</button>'
            + '<button type="button" class="btn btn-accent btn-lg" id="p-buy">' + Shop.esc(Shop.t('product.buy_now')) + '</button></div>'
            + '<div class="card-soft pad"><div class="d-flex gap-3"><i class="bi bi-arrow-repeat fs-3 text-accent" style="color:var(--accent)"></i><div><strong>' + Shop.esc(Shop.t('product.return_title')) + '</strong><div class="text-muted small">' + Shop.esc(Shop.t('product.return_text')) + '</div></div></div></div>'
            + '</div></div>'
            + (product.description ? '<div class="card-soft pad mt-4"><h5>' + Shop.esc(Shop.t('product.description')) + '</h5><div class="rich-content" id="p-desc"></div></div>' : '')
            + '<section class="mt-5 d-none" id="related-box"><div class="section-head"><h3 class="section-title">' + Shop.esc(Shop.t('product.related')) + '</h3></div><div class="row g-3" id="related"></div></section>');

        // The description is plain text written by the vendor: line breaks are kept, nothing is read as HTML.
        $('#p-desc').text(product.description).css('white-space', 'pre-line');

        // ---------- price + stock (re-drawn after every choice)
        const drawPrice = () => {
            const variant = current();
            const final = variant ? variant.final_price : product.final_price;
            const old = variant ? (variant.discount_price !== null ? variant.price : null) : (product.is_sale && product.sale_price !== null ? product.price : null);
            const fromText = product.has_variants && !variant ? '<small class="text-muted me-1">' + Shop.esc(Shop.t('product.from')) + '</small>' : '';

            $('#p-price').html(fromText + Shop.currency.html(final, 'price-big') + (old ? ' ' + Shop.currency.html(old, 'price-old fs-5') : ''));
            Shop.currency.render($('#p-price'));

            const stock = stockOf();
            let pill = '';

            if (stock === null) {
                pill = '';
            } else if (stock > 0) {
                pill = '<span class="stock-pill in"><i class="bi bi-check-circle"></i>' + Shop.esc(stock <= 5 ? Shop.t('product.only_left', { count: stock }) : Shop.t('product.in_stock')) + '</span>';
            } else {
                pill = '<span class="stock-pill out"><i class="bi bi-x-circle"></i>' + Shop.esc(Shop.t('product.out_of_stock')) + '</span>';
            }

            $('#p-stock').html(pill);
            if (variant && variant.sku) { $('#p-sku').text(variant.sku); }
        };

        // ---------- the options (Color / Size ...)
        const drawOptions = () => {
            $('#p-options').html(types.map((type) => {
                const buttons = type.values.map((value) => {
                    const isActive = selected[type.type_id] === value.id;
                    const disabled = !available(type.type_id, value.id);

                    if (type.input_type === 'color' && value.color_code) {
                        return '<button type="button" class="opt-btn opt-swatch ' + (isActive ? 'active' : '') + '" data-type="' + type.type_id + '" data-value="' + value.id + '" title="' + Shop.esc(value.value) + '" ' + (disabled ? 'disabled' : '') + '><span class="dot" style="background:' + Shop.esc(value.color_code) + '"></span></button>';
                    }

                    return '<button type="button" class="opt-btn ' + (isActive ? 'active' : '') + '" data-type="' + type.type_id + '" data-value="' + value.id + '" ' + (disabled ? 'disabled' : '') + '>' + Shop.esc(value.value) + '</button>';
                }).join('');

                const chosen = type.values.find((value) => value.id === selected[type.type_id]);

                return '<div class="opt-group"><div class="opt-title">' + Shop.esc(type.type) + (chosen ? ': <span class="text-muted fw-normal">' + Shop.esc(chosen.value) + '</span>' : '') + '</div>' + buttons + '</div>';
            }).join(''));
        };

        const redraw = () => { drawOptions(); drawPrice(); };

        $('#p-options').on('click', '.opt-btn:not(:disabled)', function () {
            const typeId = parseInt($(this).data('type'), 10);
            const valueId = parseInt($(this).data('value'), 10);

            // A second click on the same value removes the choice.
            selected[typeId] === valueId ? delete selected[typeId] : (selected[typeId] = valueId);
            redraw();
        });

        // ---------- quantity
        const setQuantity = (value) => {
            const stock = stockOf();
            const max = Math.min(99, stock && stock > 0 ? stock : 99);
            quantity = Math.max(1, Math.min(max, parseInt(value, 10) || 1));
            $('#q-input').val(quantity);
        };

        $('#q-minus').on('click', () => setQuantity(quantity - 1));
        $('#q-plus').on('click', () => setQuantity(quantity + 1));
        $('#q-input').on('change', function () { setQuantity($(this).val()); });

        // ---------- add to cart
        const addToCart = () => {
            const variant = current();

            if (product.has_variants && !variant) {
                Shop.ui.toast(Shop.t('product.choose_options'), 'error');

                return false;
            }

            const stock = stockOf();

            if (stock !== null && stock < 1) {
                Shop.ui.toast(Shop.t('product.out_of_stock'), 'error');

                return false;
            }

            Shop.cart.add({
                product_id: product.id,
                variant_id: variant ? variant.id : null,
                quantity,
                snapshot: {
                    name: product.name, slug: product.slug, image: product.image_url,
                    variant_label: variant ? variant.values.map((value) => value.value).join(' / ') : null,
                    price: variant ? variant.final_price : product.final_price,
                },
            });

            return true;
        };

        $('#p-add').on('click', () => { if (addToCart()) { Shop.ui.toast(Shop.t('cart.added'), 'success'); } });
        $('#p-buy').on('click', () => { if (addToCart()) { window.location.href = '/cart'; } });

        redraw();

        // ---------- related products (same category)
        if (category) {
            Shop.api.get('/products', { category_id: category.id, per_page: 5 }).then((res) => {
                const items = res.data.items.filter((item) => item.slug !== product.slug).slice(0, 4);

                if (items.length) {
                    $('#related').html(items.map(Shop.ui.productCard).join(''));
                    $('#related-box').removeClass('d-none');
                    Shop.currency.render($('#related'));
                }
            }).catch(() => {});
        }
    }
};
