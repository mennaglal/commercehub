/* Checkout: the address (country -> city -> area), the shipping price of the area, the coupon, the payment, and the order. */
Shop.pages.checkout = function () {
    if (!Shop.auth.require()) { return; }

    if (!Shop.cart.items().length) {
        window.location.href = '/cart';

        return;
    }

    const $form = $('#checkout-form');
    const $country = $('#country_id');
    const $city = $('#city_id');
    const $area = $('#area_id');
    const methods = APP.config.paymentMethods || {};

    // ---------- payment options (switched on in config/website.php)
    const icons = { cod: 'bi-cash-coin', bank_transfer: 'bi-bank', online: 'bi-credit-card-2-front' };
    const enabled = Object.keys(methods).filter((key) => methods[key]);

    $('#pay-options').html(enabled.map((key, index) =>
        '<label class="pay-option mb-2"><input class="form-check-input mt-0" type="radio" name="payment_method" value="' + key + '" ' + (index === 0 ? 'checked' : '') + '>'
        + '<i class="bi ' + icons[key] + ' fs-4 text-brand" style="color:var(--brand)"></i><div><div class="fw-bold">' + Shop.esc(Shop.t('checkout.pay.' + key)) + '</div>'
        + '<small class="text-muted">' + Shop.esc(Shop.t('checkout.pay.' + key + '_text')) + '</small></div></label>').join(''));

    const gateways = APP.config.gateways || {};
    $('#gateway-box').html('<label class="form-label mt-2">' + Shop.esc(Shop.t('checkout.gateway')) + '</label><select class="form-select" name="payment_gateway">'
        + Object.keys(gateways).map((key) => '<option value="' + Shop.esc(key) + '">' + Shop.esc(gateways[key]) + '</option>').join('') + '</select>');

    const togglePayment = () => {
        const method = $form.find('[name="payment_method"]:checked').val();
        $('#gateway-box').toggleClass('d-none', method !== 'online').find('select').prop('disabled', method !== 'online');
        $('#bank-box').toggleClass('d-none', method !== 'bank_transfer');
    };

    $form.on('change', '[name="payment_method"]', togglePayment);
    togglePayment();

    // ---------- country -> city -> area
    const options = (list, placeholder, label) => '<option value="">' + Shop.esc(placeholder) + '</option>' + list.map((row) => '<option value="' + row.id + '">' + Shop.esc(label ? label(row) : row.name) + '</option>').join('');

    const loadCities = (countryId, selectedCity, selectedArea) => {
        $city.html(options([], Shop.t('checkout.select_city'))).prop('disabled', true);
        $area.html(options([], Shop.t('checkout.select_area'))).prop('disabled', true);

        if (!countryId) { return Promise.resolve(); }

        return Shop.api.get('/countries/' + countryId + '/cities').then((res) => {
            $city.html(options(res.data, Shop.t('checkout.select_city'))).prop('disabled', false);

            if (selectedCity) { $city.val(selectedCity); return loadAreas(selectedCity, selectedArea); }
        });
    };

    const loadAreas = (cityId, selectedArea) => {
        $area.html(options([], Shop.t('checkout.select_area'))).prop('disabled', true);

        if (!cityId) { return Promise.resolve(); }

        return Shop.api.get('/cities/' + cityId + '/areas').then((res) => {
            $area.html(options(res.data, Shop.t('checkout.select_area'), (area) => area.name + ' (+' + Shop.currency.format(area.shipping_price) + ')')).prop('disabled', false);

            if (selectedArea) { $area.val(selectedArea); }
        });
    };

    $country.on('change', () => { loadCities($country.val()).then(recalc); });
    $city.on('change', () => { loadAreas($city.val()).then(recalc); });
    $area.on('change', recalc);

    // ---------- the summary: the prices, the coupon and the shipping come from the server
    let lastSummary = null;

    function recalc() {
        const extra = $area.val() ? { country_id: parseInt($country.val(), 10), city_id: parseInt($city.val(), 10), area_id: parseInt($area.val(), 10) } : {};

        $('#checkout-alert').addClass('d-none');

        return Shop.api.post('/checkout/preview', Shop.cart.payload(extra)).then((res) => {
            lastSummary = res.data;
            drawSummary(res.data);
            $('#place-order').prop('disabled', false);
        }).catch((error) => {
            if (error.errors && error.errors.coupon_code) {
                Shop.cart.setCoupon('');
                Shop.ui.toast([].concat(error.errors.coupon_code)[0], 'error');

                return recalc();
            }

            $('#checkout-alert').removeClass('d-none').text(error.errors && error.errors.items ? [].concat(error.errors.items)[0] : error.message);
            $('#place-order').prop('disabled', true);
        });
    }

    function drawSummary(data) {
        const rows = data.items.map((line) => {
            const saved = Shop.cart.items().find((row) => Shop.cart.key(row) === Shop.cart.key(line));

            return '<div class="d-flex gap-2 align-items-center mb-2"><img class="thumb-sm" src="' + Shop.esc(Shop.img(saved ? saved.snapshot.image : null)) + '" alt="">'
                + '<div class="flex-fill small"><div class="fw-bold">' + Shop.esc(line.product_name) + '</div>'
                + (line.variant_label ? '<div class="text-muted">' + Shop.esc(line.variant_label) + '</div>' : '')
                + '<div class="text-muted">x ' + line.quantity + '</div></div><div class="small fw-bold">' + Shop.currency.html(line.line_total) + '</div></div>';
        }).join('');

        $('#checkout-summary').html(rows + '<hr>'
            + '<div class="summary-row"><span>' + Shop.esc(Shop.t('cart.subtotal')) + '</span>' + Shop.currency.html(data.subtotal) + '</div>'
            + (data.discount > 0 ? '<div class="summary-row"><span>' + Shop.esc(Shop.t('cart.discount')) + ' <span class="chip">' + Shop.esc(data.coupon_code) + '</span></span><span class="discount">- ' + Shop.currency.html(data.discount) + '</span></div>' : '')
            + '<div class="summary-row"><span>' + Shop.esc(Shop.t('cart.shipping')) + '</span>' + ($area.val() ? Shop.currency.html(data.shipping) : '<small class="text-muted">' + Shop.esc(Shop.t('checkout.choose_area')) + '</small>') + '</div>'
            + '<div class="summary-row total"><span>' + Shop.esc(Shop.t('cart.total')) + '</span>' + Shop.currency.html(data.total) + '</div>'
            + (!Shop.currency.isBase() ? '<div class="small text-muted mt-2"><i class="bi bi-info-circle"></i> ' + Shop.esc(Shop.t('common.currency_note')) + '</div>' : ''));
        Shop.currency.render($('#checkout-summary'));
    }

    $(document).on('currency:changed', () => lastSummary && drawSummary(lastSummary));

    // The coupon box of the checkout
    $('#co-coupon').val(Shop.cart.coupon());
    $('#co-coupon-apply').on('click', function () {
        Shop.cart.setCoupon($.trim($('#co-coupon').val()).toUpperCase());
        recalc();
    });

    // ---------- place the order
    $form.on('submit', function (event) {
        event.preventDefault();

        const $button = $('#place-order');
        const data = {
            customer_name: $form.find('[name="customer_name"]').val(),
            customer_phone: $form.find('[name="customer_phone"]').val(),
            country_id: $country.val() ? parseInt($country.val(), 10) : null,
            city_id: $city.val() ? parseInt($city.val(), 10) : null,
            area_id: $area.val() ? parseInt($area.val(), 10) : null,
            shipping_address: $form.find('[name="shipping_address"]').val(),
            shipping_notes: $form.find('[name="shipping_notes"]').val() || null,
            notes: $form.find('[name="notes"]').val() || null,
            payment_method: $form.find('[name="payment_method"]:checked').val(),
        };

        if (data.payment_method === 'online') {
            data.payment_gateway = $form.find('[name="payment_gateway"]').val();
        }

        Shop.ui.busy($button, true);

        Shop.api.post('/orders', Shop.cart.payload(data)).then((res) => {
            Shop.cart.clear();
            window.location.href = '/order-success/' + encodeURIComponent(res.data.order_number);
        }).catch((error) => {
            Shop.ui.busy($button, false);

            if (error.status === 422) {
                Shop.ui.showErrors($form, error.errors);

                // The errors of the cart ("items") and of the coupon have no input: show them on top.
                const top = [].concat((error.errors && error.errors.items) || [], (error.errors && error.errors.coupon_code) || []);
                $('#checkout-alert').toggleClass('d-none', !top.length).text(top[0] || '');
                $('html, body').animate({ scrollTop: $form.offset().top - 100 }, 250);
            } else {
                Shop.ui.error(error);
            }
        });
    });

    // ---------- start: prefill from the profile, then the summary
    const user = Shop.auth.user() || {};
    $form.find('[name="customer_name"]').val(user.name || '');
    $form.find('[name="customer_phone"]').val(user.phone || '');

    Shop.api.get('/countries').then((res) => {
        $country.html(options(res.data, Shop.t('checkout.select_country')));

        if (user.country_id) {
            $country.val(user.country_id);

            return loadCities(user.country_id, user.city_id, user.area_id);
        }
    }).catch(() => {}).then(recalc);
};

/* After the order: shows the order number and what happens next. */
Shop.pages['order-success'] = function () {
    const number = $('#success-root').data('order');

    Shop.api.get('/orders/' + encodeURIComponent(number)).then((res) => {
        const order = res.data;

        $('#success-number').text(order.order_number);
        $('#success-total').html(Shop.currency.html(order.totals.total));
        $('#success-payment').text(Shop.t('checkout.pay.' + order.payment.method));
        $('#success-view').attr('href', '/account/orders/' + encodeURIComponent(order.order_number));

        if (order.payment.invoice_url) {
            $('#success-pay').removeClass('d-none').attr('href', order.payment.invoice_url);
        }

        Shop.currency.render($('#success-root'));
    }).catch(() => {});
};
