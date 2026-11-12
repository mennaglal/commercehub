/* Cart page. The cart is in the browser; the PRICES come from the server (POST /checkout/preview)
   every time, so a price changed in the admin panel is never shown wrong, and an unavailable product is reported. */
Shop.pages.cart = function () {
    const $lines = $('#cart-lines');
    const $summary = $('#cart-summary');
    let reloadTimer = null;

    const showEmpty = () => {
        $('#cart-content').addClass('d-none');
        $('#cart-empty').removeClass('d-none').html(Shop.ui.empty('bi-bag', Shop.t('cart.empty'), Shop.t('cart.empty_text'), '<a class="btn btn-brand mt-3" href="/shop">' + Shop.esc(Shop.t('cart.start_shopping')) + '</a>'));
    };

    // One line of the cart. "line" = { product_id, variant_id, product_name, variant_label, unit_price, quantity, line_total } (or the saved copy)
    const lineHtml = (line) => {
        const key = Shop.cart.key(line);
        const saved = Shop.cart.items().find((row) => Shop.cart.key(row) === key);
        const snap = saved ? saved.snapshot : {};

        return '<div class="cart-line" data-key="' + Shop.esc(key) + '">'
            + '<a href="/products/' + encodeURIComponent(snap.slug || '') + '"><img src="' + Shop.esc(Shop.img(snap.image)) + '" alt=""></a>'
            + '<div class="meta"><a class="fw-bold text-dark" href="/products/' + encodeURIComponent(snap.slug || '') + '">' + Shop.esc(line.product_name || snap.name) + '</a>'
            + (line.variant_label || snap.variant_label ? '<div class="text-muted small">' + Shop.esc(line.variant_label || snap.variant_label) + '</div>' : '')
            + '<div class="small mt-1">' + Shop.currency.html(line.unit_price !== undefined ? line.unit_price : snap.price) + '</div>'
            + '<div class="d-flex align-items-center gap-3 mt-2"><div class="qty"><button type="button" data-step="-1" aria-label="-">&minus;</button>'
            + '<input type="number" min="1" max="99" value="' + line.quantity + '" data-qty inputmode="numeric"><button type="button" data-step="1" aria-label="+">+</button></div>'
            + '<button type="button" class="btn btn-link text-danger p-0" data-remove><i class="bi bi-trash"></i> ' + Shop.esc(Shop.t('common.remove')) + '</button></div></div>'
            + '<div class="fw-bold text-end">' + (line.line_total !== undefined ? Shop.currency.html(line.line_total) : '') + '</div></div>';
    };

    const summaryHtml = (data, blocked) => {
        const coupon = Shop.cart.coupon();

        return '<div class="card-soft pad sticky-summary"><h5 class="mb-3">' + Shop.esc(Shop.t('cart.summary')) + '</h5>'
            + '<form id="coupon-form" class="input-group mb-3"><input class="form-control" name="coupon" maxlength="50" placeholder="' + Shop.esc(Shop.t('cart.coupon_placeholder')) + '" value="' + Shop.esc(coupon) + '">'
            + '<button class="btn btn-soft" type="submit">' + Shop.esc(Shop.t('common.apply')) + '</button></form>'
            + (data ? '<div class="summary-row"><span>' + Shop.esc(Shop.t('cart.subtotal')) + '</span>' + Shop.currency.html(data.subtotal) + '</div>'
                + (data.discount > 0 ? '<div class="summary-row"><span>' + Shop.esc(Shop.t('cart.discount')) + (data.coupon_code ? ' <span class="chip">' + Shop.esc(data.coupon_code) + ' <button type="button" id="coupon-remove" aria-label="x"><i class="bi bi-x-lg"></i></button></span>' : '') + '</span><span class="discount">- ' + Shop.currency.html(data.discount) + '</span></div>' : '')
                + '<div class="summary-row"><span>' + Shop.esc(Shop.t('cart.shipping')) + '</span><small class="text-muted">' + Shop.esc(Shop.t('cart.shipping_at_checkout')) + '</small></div>'
                + '<div class="summary-row total"><span>' + Shop.esc(Shop.t('cart.total')) + '</span>' + Shop.currency.html(data.total) + '</div>' : '')
            + (!Shop.currency.isBase() ? '<div class="small text-muted mt-2"><i class="bi bi-info-circle"></i> ' + Shop.esc(Shop.t('common.currency_note')) + '</div>' : '')
            + '<a href="/checkout" class="btn btn-accent w-100 mt-3 ' + (blocked ? 'disabled' : '') + '" ' + (blocked ? 'aria-disabled="true"' : '') + '>' + Shop.esc(Shop.t('cart.checkout')) + '</a>'
            + '<a href="/shop" class="btn btn-link w-100 mt-1">' + Shop.esc(Shop.t('cart.continue')) + '</a></div>';
    };

    const load = () => {
        const items = Shop.cart.items();

        if (!items.length) {
            showEmpty();

            return;
        }

        $('#cart-empty').addClass('d-none');
        $('#cart-content').removeClass('d-none');
        $('#cart-alert').addClass('d-none');

        Shop.api.post('/checkout/preview', Shop.cart.payload()).then((res) => {
            const data = res.data;

            // The server is the truth: the saved copy of the price is refreshed too.
            $lines.html(data.items.map(lineHtml).join(''));
            $summary.html(summaryHtml(data, false));
            Shop.currency.render();
        }).catch((error) => {
            // The server refused (a product is not available, not enough stock, a wrong coupon ...)
            if (error.errors && error.errors.coupon_code) {
                Shop.cart.setCoupon('');
                Shop.ui.toast([].concat(error.errors.coupon_code)[0], 'error');
                load();

                return;
            }

            const message = error.errors && error.errors.items ? [].concat(error.errors.items)[0] : error.message;

            $('#cart-alert').removeClass('d-none').text(message);
            $lines.html(items.map((row) => lineHtml({
                product_id: row.product_id, variant_id: row.variant_id, quantity: row.quantity,
                product_name: row.snapshot.name, variant_label: row.snapshot.variant_label, unit_price: row.snapshot.price, line_total: row.snapshot.price * row.quantity,
            })).join(''));
            $summary.html(summaryHtml(null, true));
            Shop.currency.render();
        });
    };

    const reloadSoon = () => { clearTimeout(reloadTimer); reloadTimer = setTimeout(load, 450); };

    // ---- events (delegated, the lines are redrawn all the time)
    $lines.on('click', '[data-step]', function () {
        const key = $(this).closest('.cart-line').data('key');
        const $input = $(this).siblings('[data-qty]');
        const quantity = Math.max(1, (parseInt($input.val(), 10) || 1) + parseInt($(this).data('step'), 10));

        $input.val(quantity);
        Shop.cart.setQty(String(key), quantity);
        reloadSoon();
    });

    $lines.on('change', '[data-qty]', function () {
        Shop.cart.setQty(String($(this).closest('.cart-line').data('key')), parseInt($(this).val(), 10) || 1);
        reloadSoon();
    });

    $lines.on('click', '[data-remove]', function () {
        Shop.cart.remove(String($(this).closest('.cart-line').data('key')));
        load();
    });

    $summary.on('submit', '#coupon-form', function (event) {
        event.preventDefault();
        Shop.cart.setCoupon($.trim($(this).find('[name="coupon"]').val()).toUpperCase());
        load();
    });

    $summary.on('click', '#coupon-remove', function () { Shop.cart.setCoupon(''); load(); });
    $('#cart-clear').on('click', function () { if (window.confirm(Shop.t('cart.clear_confirm'))) { Shop.cart.clear(); load(); } });
    $(document).on('currency:changed', () => $summary.find('.js-price').length && Shop.currency.render());

    load();
};
