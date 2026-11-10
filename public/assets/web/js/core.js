/* ==========================================================================
   core.js - everything the pages share.
   Shop.t        translations (the same lang/xx/web.php used by Blade)
   Shop.esc      HTML escaping (EVERY dynamic text goes through it)
   Shop.api      the API client (token, language, errors)
   Shop.auth     the logged-in user
   Shop.currency prices in the chosen currency
   Shop.cart     the cart (kept in the browser)
   Shop.ui       small shared pieces (toast, product card, pagination ...)
   Each page has its own file in js/pages/ and registers itself in Shop.pages.<name>.
   ========================================================================== */
(function (window, $) {
    'use strict';

    const APP = window.APP;
    const Shop = (window.Shop = { APP, pages: {}, state: {} });

    /* ---------------------------------------------------------------- helpers */

    // Translation: Shop.t('cart.title') or Shop.t('common.showing', {from: 1, to: 10})
    Shop.t = function (key, params) {
        let text = key.split('.').reduce((node, part) => (node && node[part] !== undefined ? node[part] : null), APP.t);

        if (typeof text !== 'string') {
            return key;
        }

        Object.keys(params || {}).forEach((name) => {
            text = text.split(':' + name).join(params[name]);
        });

        return text;
    };

    // Escape before putting a text in HTML. The API data is NEVER trusted.
    Shop.esc = function (value) {
        return String(value === null || value === undefined ? '' : value).replace(/[&<>"']/g, (char) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
        }[char]));
    };

    Shop.img = (url) => url || APP.placeholder;

    Shop.isAr = () => APP.locale === 'ar';

    Shop.debounce = function (fn, wait) {
        let timer;

        return function () {
            const args = arguments;
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, args), wait || 350);
        };
    };

    Shop.date = function (iso) {
        if (!iso) {
            return '-';
        }

        return new Date(iso).toLocaleDateString(Shop.isAr() ? 'ar-EG-u-nu-latn' : 'en-GB', {
            year: 'numeric', month: 'short', day: 'numeric',
        });
    };

    // localStorage that never throws (private mode, full disk ...)
    Shop.store = {
        get(key, fallback) {
            try {
                const raw = window.localStorage.getItem(key);
                return raw === null ? fallback : JSON.parse(raw);
            } catch (e) {
                return fallback;
            }
        },
        set(key, value) {
            try { window.localStorage.setItem(key, JSON.stringify(value)); } catch (e) { /* ignore */ }
        },
        remove(key) {
            try { window.localStorage.removeItem(key); } catch (e) { /* ignore */ }
        },
    };

    // ?a=1&b=2  <->  {a: 1, b: 2}
    Shop.qs = {
        get() {
            const out = {};
            new URLSearchParams(window.location.search).forEach((value, key) => { out[key] = value; });
            return out;
        },
        set(params) {
            const clean = {};
            Object.keys(params).forEach((key) => {
                if (params[key] !== '' && params[key] !== null && params[key] !== undefined && params[key] !== false) {
                    clean[key] = params[key];
                }
            });
            const query = new URLSearchParams(clean).toString();
            window.history.replaceState(null, '', window.location.pathname + (query ? '?' + query : ''));
        },
    };

    /* ----------------------------------------------------------- HTML cleaner
       Pages written in the admin panel are HTML. Before showing them we remove everything that is not
       plain content (scripts, forms, event attributes, javascript: links ...). */
    Shop.sanitize = function (html) {
        const allowedTags = ['P', 'BR', 'B', 'STRONG', 'I', 'EM', 'U', 'H1', 'H2', 'H3', 'H4', 'H5', 'UL', 'OL', 'LI', 'A', 'BLOCKQUOTE', 'HR', 'TABLE', 'THEAD', 'TBODY', 'TR', 'TH', 'TD', 'SPAN', 'DIV', 'IMG', 'SMALL', 'CODE', 'PRE'];
        const doc = new DOMParser().parseFromString('<div>' + (html || '') + '</div>', 'text/html');

        const clean = (node) => {
            Array.from(node.childNodes).forEach((child) => {
                if (child.nodeType === 8) { // comment
                    child.remove();
                } else if (child.nodeType === 1) {
                    if (!allowedTags.includes(child.tagName)) {
                        // keep the text of an unknown tag, drop the tag (and everything inside <script>/<style>)
                        if (['SCRIPT', 'STYLE', 'IFRAME', 'OBJECT', 'EMBED', 'FORM', 'LINK', 'META'].includes(child.tagName)) {
                            child.remove();
                        } else {
                            clean(child);
                            child.replaceWith(...Array.from(child.childNodes));
                        }
                        return;
                    }

                    Array.from(child.attributes).forEach((attr) => {
                        const name = attr.name.toLowerCase();
                        const value = attr.value.trim().toLowerCase();
                        const okLink = name === 'href' && /^(https?:|mailto:|tel:|\/|#)/.test(value);
                        const okImg = name === 'src' && child.tagName === 'IMG' && /^(https?:|\/)/.test(value);

                        if (!(okLink || okImg || ['alt', 'title'].includes(name))) {
                            child.removeAttribute(attr.name);
                        }
                    });

                    if (child.tagName === 'A') {
                        child.setAttribute('rel', 'noopener noreferrer');
                    }

                    clean(child);
                }
            });
        };

        clean(doc.body.firstChild);

        return doc.body.firstChild.innerHTML;
    };

    /* -------------------------------------------------------------------- API */

    Shop.api = {
        // Returns a Promise. Success => the whole answer { success, message, data, errors }.
        // Failure => { status, message, errors }.
        request(method, path, data, options) {
            options = options || {};

            const headers = { Accept: 'application/json', 'Accept-Language': APP.locale };
            const token = Shop.auth.token();

            if (token) {
                headers.Authorization = 'Bearer ' + token;
            }

            const settings = { url: APP.apiBase + path, method, headers, dataType: 'json' };

            if (method === 'GET') {
                settings.data = data;
            } else if (data instanceof FormData) {
                settings.data = data;
                settings.processData = false;
                settings.contentType = false;
            } else if (data !== undefined) {
                settings.data = JSON.stringify(data);
                settings.contentType = 'application/json';
            }

            return new Promise((resolve, reject) => {
                $.ajax(settings)
                    .done((response) => resolve(response))
                    .fail((xhr) => {
                        const body = xhr.responseJSON || {};
                        const error = {
                            status: xhr.status,
                            message: body.message || Shop.t('common.error'),
                            errors: body.errors || null,
                        };

                        // The token is not valid anymore (expired, removed, account suspended): go to the login page.
                        if (xhr.status === 401 && token && !options.silent) {
                            Shop.auth.clear();
                            window.location.href = '/login?redirect=' + encodeURIComponent(window.location.pathname + window.location.search);
                        }

                        reject(error);
                    });
            });
        },

        get: (path, data, options) => Shop.api.request('GET', path, data, options),
        post: (path, data, options) => Shop.api.request('POST', path, data, options),
        put: (path, data, options) => Shop.api.request('PUT', path, data, options),
        patch: (path, data, options) => Shop.api.request('PATCH', path, data, options),
        delete: (path, data, options) => Shop.api.request('DELETE', path, data, options),
    };

    /* ------------------------------------------------------------------- Auth */

    Shop.auth = {
        token: () => Shop.store.get('shop_token', null),
        user: () => Shop.store.get('shop_user', null),
        isLoggedIn() { return !!this.token(); },
        hasRole(role) { const user = this.user(); return !!(user && (user.roles || []).includes(role)); },

        login(token, user) {
            Shop.store.set('shop_token', token);
            Shop.store.set('shop_user', user);
        },
        setUser(user) { Shop.store.set('shop_user', user); },
        clear() { Shop.store.remove('shop_token'); Shop.store.remove('shop_user'); },

        // The server removes the token, then the browser forgets it.
        logout() {
            const done = () => { Shop.auth.clear(); window.location.href = '/'; };

            Shop.api.post('/logout', undefined, { silent: true }).then(done, done);
        },

        // For the pages that need a login: sends the visitor to the login page and comes back afterwards.
        require(role) {
            if (!this.isLoggedIn()) {
                window.location.href = '/login?redirect=' + encodeURIComponent(window.location.pathname + window.location.search);

                return false;
            }

            if (role && !this.hasRole(role)) {
                window.location.href = role === 'vendor' ? '/become-vendor' : '/';

                return false;
            }

            return true;
        },

        // Reads the profile again (name, roles ...) and keeps it fresh in the browser.
        refresh() {
            return Shop.api.get('/profile').then((res) => {
                Shop.auth.setUser(res.data.user);

                return res.data.user;
            });
        },
    };

    /* ---------------------------------------------------------------- Currency
       The API prices are in EGP. The chosen currency only changes how they are SHOWN:
       shown = EGP price / rate_to_egp. The order is always charged in EGP. */
    Shop.currency = {
        list: [],
        current: { code: 'EGP', symbol: 'EGP', rate_to_egp: 1, decimal_places: 2 },

        load() {
            const cached = Shop.store.get('shop_currencies', null);
            const fresh = cached && Date.now() - cached.time < 30 * 60 * 1000;

            const apply = (list) => {
                this.list = list;
                const code = Shop.store.get('shop_currency', APP.config.defaultCurrency);
                this.current = list.find((currency) => currency.code === code)
                    || list.find((currency) => currency.code === 'EGP')
                    || this.current;
            };

            if (fresh) {
                apply(cached.list);

                return Promise.resolve();
            }

            return Shop.api.get('/currencies').then((res) => {
                Shop.store.set('shop_currencies', { time: Date.now(), list: res.data });
                apply(res.data);
            }).catch(() => apply(cached ? cached.list : []));
        },

        set(code) {
            Shop.store.set('shop_currency', code);
            this.current = this.list.find((currency) => currency.code === code) || this.current;
            this.render();
            $(document).trigger('currency:changed');
        },

        convert(egp) {
            const rate = parseFloat(this.current.rate_to_egp) || 1;

            return (parseFloat(egp) || 0) / rate;
        },

        format(egp) {
            const decimals = parseInt(this.current.decimal_places, 10);
            const number = new Intl.NumberFormat('en-US', {
                minimumFractionDigits: isNaN(decimals) ? 2 : decimals,
                maximumFractionDigits: isNaN(decimals) ? 2 : decimals,
            }).format(this.convert(egp));

            return number + ' ' + this.current.symbol;
        },

        // <span class="js-price" data-egp="120.5">  is filled (and refilled when the currency changes).
        html(egp, extraClass) {
            return '<span class="js-price ' + (extraClass || '') + '" data-egp="' + Shop.esc(egp) + '">' + Shop.esc(this.format(egp)) + '</span>';
        },

        render(scope) {
            $('.js-price', scope || document).each(function () {
                $(this).text(Shop.currency.format($(this).attr('data-egp')));
            });
        },

        isBase() { return this.current.code === 'EGP'; },
    };

    /* ------------------------------------------------------------------- Cart
       The cart lives in the browser (localStorage). The server never stores it:
       POST /checkout/preview checks prices, stock and coupon whenever we need. */
    Shop.cart = {
        KEY: 'shop_cart',
        MAX_QTY: 99,

        key: (item) => item.product_id + '-' + (item.variant_id || 0),
        items() { return Shop.store.get(this.KEY, []); },
        count() { return this.items().reduce((sum, item) => sum + item.quantity, 0); },
        write(items) { Shop.store.set(this.KEY, items); $(document).trigger('cart:changed'); },

        // item: { product_id, variant_id, quantity, snapshot: { name, slug, image, variant_label, price } }
        add(item) {
            const items = this.items();
            const found = items.find((row) => this.key(row) === this.key(item));

            if (found) {
                found.quantity = Math.min(this.MAX_QTY, found.quantity + item.quantity);
                found.snapshot = item.snapshot;
            } else {
                items.push(item);
            }

            this.write(items);
        },

        setQty(key, quantity) {
            const items = this.items();
            const found = items.find((row) => this.key(row) === key);

            if (found) {
                found.quantity = Math.max(1, Math.min(this.MAX_QTY, quantity));
                this.write(items);
            }
        },

        remove(key) { this.write(this.items().filter((row) => this.key(row) !== key)); },
        clear() { Shop.store.remove(this.KEY); Shop.store.remove('shop_coupon'); $(document).trigger('cart:changed'); },

        coupon: () => Shop.store.get('shop_coupon', ''),
        setCoupon(code) { code ? Shop.store.set('shop_coupon', code) : Shop.store.remove('shop_coupon'); },

        // What the server needs: only product, variant and quantity. The prices are never sent.
        payload(extra) {
            const body = {
                items: this.items().map((row) => ({ product_id: row.product_id, variant_id: row.variant_id || null, quantity: row.quantity })),
            };

            if (this.coupon()) {
                body.coupon_code = this.coupon();
            }

            return Object.assign(body, extra || {});
        },
    };

    /* --------------------------------------------------------------------- UI */

    Shop.ui = {
        toast(message, type) {
            const icons = { success: 'bi-check-circle-fill text-success', error: 'bi-x-circle-fill text-danger', info: 'bi-info-circle-fill text-primary' };
            type = type || 'info';

            let wrap = $('.toast-wrap');
            if (!wrap.length) {
                wrap = $('<div class="toast-wrap" role="status" aria-live="polite"></div>').appendTo('body');
            }

            const toast = $('<div class="toast-item ' + type + '"><i class="bi ' + icons[type] + '"></i><div class="flex-fill"></div></div>');
            toast.find('.flex-fill').text(message); // text(): the message is never read as HTML
            wrap.append(toast);

            setTimeout(() => toast.fadeOut(250, () => toast.remove()), 4200);
        },

        // The message of an API error as a toast.
        error(error) { Shop.ui.toast(error && error.message ? error.message : Shop.t('common.error'), 'error'); },

        // Disable a button while a request is running.
        busy($button, on) {
            if (on) {
                $button.data('label', $button.html()).prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm me-1"></span>' + Shop.esc(Shop.t('common.sending')));
            } else {
                $button.prop('disabled', false).html($button.data('label'));
            }
        },

        // Show the validation errors under the inputs. "variants.0.price" matches name="variants[0][price]".
        showErrors($form, errors) {
            $form.find('.is-invalid').removeClass('is-invalid');
            $form.find('.js-error').remove();
            $form.find('.form-errors').empty().addClass('d-none');

            const general = [];

            Object.keys(errors || {}).forEach((key) => {
                const name = key.replace(/\.(\w+)/g, '[$1]');
                const $input = $form.find('[name="' + name + '"], [name="' + name + '[]"]').first();
                const message = [].concat(errors[key])[0];

                if ($input.length) {
                    $input.addClass('is-invalid');
                    $('<div class="invalid-feedback js-error"></div>').text(message).insertAfter($input.closest('.input-group').length ? $input.closest('.input-group') : $input);
                } else {
                    general.push(message);
                }
            });

            if (general.length) {
                $form.find('.form-errors').removeClass('d-none').html(general.map((m) => '<div>' + Shop.esc(m) + '</div>').join(''));
            }
        },

        skeletonCards(count) {
            let html = '';
            for (let i = 0; i < count; i++) {
                html += '<div class="col-6 col-md-4 col-xl-3"><div class="skeleton-card"><div class="skeleton s-img"></div><div class="skeleton s-line"></div><div class="skeleton s-line short"></div></div></div>';
            }

            return html;
        },

        empty(icon, title, text, actionHtml) {
            return '<div class="empty-state"><i class="bi ' + icon + '"></i><h5>' + Shop.esc(title) + '</h5>'
                + (text ? '<p>' + Shop.esc(text) + '</p>' : '') + (actionHtml || '') + '</div>';
        },

        // ---- the status of an order / delivery / payment / return as a colored badge
        statusBadge(group, value) {
            const colors = {
                order: { pending: 'warning', confirmed: 'info', cancelled: 'danger' },
                delivery: { pending: 'secondary', preparing: 'warning', shipped: 'primary', delivered: 'success', returned: 'danger' },
                payment: { pending: 'warning', paid: 'success', failed: 'danger', refunded: 'secondary' },
                return: { requested: 'warning', approved: 'info', rejected: 'danger', received: 'primary', refunded: 'success' },
            };

            return '<span class="status ' + ((colors[group] || {})[value] || 'secondary') + '">' + Shop.esc(Shop.t('status.' + group + '.' + value)) + '</span>';
        },

        // ---- the product card used in every list
        productCard(product) {
            const url = '/products/' + encodeURIComponent(product.slug);
            const hasOld = product.is_sale && product.sale_price !== null;
            const canQuickAdd = !product.has_variants && product.in_stock;

            const badge = !product.in_stock
                ? '<span class="badge-out">' + Shop.esc(Shop.t('product.out_of_stock')) + '</span>'
                : (hasOld ? '<span class="badge-sale">' + Shop.esc(Shop.t('product.sale')) + '</span>' : '');

            const quick = canQuickAdd
                ? '<button type="button" class="btn btn-brand btn-icon quick-add" data-quick-add data-id="' + product.id + '" data-name="' + Shop.esc(product.name)
                    + '" data-slug="' + Shop.esc(product.slug) + '" data-image="' + Shop.esc(product.image_url || '') + '" data-price="' + Shop.esc(product.final_price)
                    + '" aria-label="' + Shop.esc(Shop.t('product.add_to_cart')) + '"><i class="bi bi-bag-plus"></i></button>'
                : '';

            return '<div class="col-6 col-md-4 col-xl-3"><div class="product-card">'
                + '<a class="thumb" href="' + url + '">' + badge + '<img loading="lazy" src="' + Shop.esc(Shop.img(product.image_url)) + '" alt="' + Shop.esc(product.name) + '"></a>'
                + quick
                + '<div class="body">'
                + (product.store ? '<a class="store" href="/stores/' + encodeURIComponent(product.store.slug) + '">' + Shop.esc(product.store.name) + '</a>' : '')
                + '<a class="name" href="' + url + '">' + Shop.esc(product.name) + '</a>'
                + '<div class="price-row">'
                + (product.price_is_from ? '<span class="price-from">' + Shop.esc(Shop.t('product.from')) + '</span>' : '')
                + Shop.currency.html(product.final_price, 'price-now')
                + (hasOld ? Shop.currency.html(product.price, 'price-old') : '')
                + '</div></div></div></div>';
        },

        // ---- the store card (home page and stores page)
        storeCard(store) {
            const banner = store.banner_url ? ' style="background-image:url(\'' + encodeURI(store.banner_url).replace(/'/g, '%27') + '\')"' : '';

            return '<div class="col-6 col-md-4 col-xl-3"><a class="store-card" href="/stores/' + encodeURIComponent(store.slug) + '">'
                + '<div class="banner"' + banner + '></div>'
                + '<img class="logo" loading="lazy" src="' + Shop.esc(Shop.img(store.logo_url)) + '" alt="' + Shop.esc(store.name) + '">'
                + '<div class="info"><h6 class="mb-1">' + Shop.esc(store.name) + '</h6>'
                + '<small class="text-muted d-block">' + Shop.esc(Shop.t('stores.products_count', { count: store.products_count || 0 })) + '</small>'
                + (store.city ? '<small class="text-muted"><i class="bi bi-geo-alt"></i> ' + Shop.esc(store.city) + '</small>' : '')
                + '</div></a></div>';
        },

        // ---- pagination: meta = { current_page, last_page }, onPage(page) is called when a button is clicked
        pagination($box, meta, onPage) {
            $box.empty();

            if (!meta || meta.last_page <= 1) {
                return;
            }

            const current = meta.current_page;
            const last = meta.last_page;
            const pages = [];

            for (let page = 1; page <= last; page++) {
                if (page === 1 || page === last || Math.abs(page - current) <= 1) {
                    pages.push(page);
                } else if (pages[pages.length - 1] !== '...') {
                    pages.push('...');
                }
            }

            const item = (label, page, extra) => '<li class="page-item ' + (extra || '') + '"><a class="page-link" href="#" data-page="' + page + '">' + label + '</a></li>';

            let html = '<ul class="pagination">';
            html += item('<i class="bi bi-chevron-' + (Shop.isAr() ? 'right' : 'left') + '"></i>', current - 1, current === 1 ? 'disabled' : '');
            pages.forEach((page) => { html += page === '...' ? '<li class="page-item disabled"><span class="page-link">…</span></li>' : item(page, page, page === current ? 'active' : ''); });
            html += item('<i class="bi bi-chevron-' + (Shop.isAr() ? 'left' : 'right') + '"></i>', current + 1, current === last ? 'disabled' : '');
            html += '</ul>';

            $box.html(html).off('click').on('click', '[data-page]', function (event) {
                event.preventDefault();
                const page = parseInt($(this).data('page'), 10);

                if (page >= 1 && page <= last && page !== current) {
                    onPage(page);
                    $('html, body').animate({ scrollTop: Math.max(0, $box.closest('[data-scroll-top]').offset() ? $box.closest('[data-scroll-top]').offset().top - 100 : 0) }, 250);
                }
            });
        },

        // The same 4 steps timeline used for the delivery of an order.
        deliveryTimeline(status) {
            const steps = ['pending', 'preparing', 'shipped', 'delivered'];
            const index = steps.indexOf(status);
            const icons = ['bi-receipt', 'bi-box-seam', 'bi-truck', 'bi-house-check'];

            return '<div class="timeline">' + steps.map((step, position) =>
                '<div class="t-step ' + (index >= position ? 'done' : '') + '"><div class="t-dot"><i class="bi ' + icons[position] + '"></i></div>'
                + Shop.esc(Shop.t('status.delivery.' + step)) + '</div>').join('') + '</div>';
        },
    };

    /* ------------------------------------------------------------- Location
       Country -> City -> Area selects, used by the profile, the vendor store and the become-a-vendor form.
       Shop.location.init({ $country, $city, $area, values: { country, city, area } }) */
    Shop.location = {
        options(list, placeholder) {
            return '<option value="">' + Shop.esc(placeholder) + '</option>'
                + list.map((row) => '<option value="' + row.id + '">' + Shop.esc(row.name) + '</option>').join('');
        },

        init(config) {
            const self = this;
            const values = config.values || {};

            const loadAreas = (cityId, selected) => {
                config.$area.html(self.options([], Shop.t('checkout.select_area'))).prop('disabled', true);

                if (!cityId) { return Promise.resolve(); }

                return Shop.api.get('/cities/' + cityId + '/areas').then((res) => {
                    config.$area.html(self.options(res.data, Shop.t('checkout.select_area'))).prop('disabled', false);
                    if (selected) { config.$area.val(selected); }
                });
            };

            const loadCities = (countryId, selectedCity, selectedArea) => {
                config.$city.html(self.options([], Shop.t('checkout.select_city'))).prop('disabled', true);
                config.$area.html(self.options([], Shop.t('checkout.select_area'))).prop('disabled', true);

                if (!countryId) { return Promise.resolve(); }

                return Shop.api.get('/countries/' + countryId + '/cities').then((res) => {
                    config.$city.html(self.options(res.data, Shop.t('checkout.select_city'))).prop('disabled', false);

                    if (selectedCity) {
                        config.$city.val(selectedCity);

                        return loadAreas(selectedCity, selectedArea);
                    }
                });
            };

            config.$country.on('change', () => loadCities(config.$country.val()));
            config.$city.on('change', () => loadAreas(config.$city.val()));

            return Shop.api.get('/countries').then((res) => {
                config.$country.html(self.options(res.data, Shop.t('checkout.select_country')));

                if (values.country) {
                    config.$country.val(values.country);

                    return loadCities(values.country, values.city, values.area);
                }
            }).catch(() => {});
        },
    };

    // A message that is shown on the NEXT page (after a redirect): Shop.flash('Saved', 'success')
    Shop.flash = (message, type) => Shop.store.set('shop_flash', { message, type: type || 'success' });

    /* --------------------------------------------------------------- Header */

    function initHeader() {
        const refreshCart = () => {
            const count = Shop.cart.count();
            $('.js-cart-count').text(count).toggleClass('d-none', count === 0);
        };

        refreshCart();
        $(document).on('cart:changed', refreshCart);

        // Logged in / out
        const user = Shop.auth.user();
        $('.js-guest').toggleClass('d-none', Shop.auth.isLoggedIn());
        $('.js-member').toggleClass('d-none', !Shop.auth.isLoggedIn());
        $('.js-vendor-only').toggleClass('d-none', !Shop.auth.hasRole('vendor'));
        $('.js-not-vendor').toggleClass('d-none', Shop.auth.hasRole('vendor'));
        $('.js-user-name').text(user ? user.name : '');
        $('.js-logout').on('click', function (event) { event.preventDefault(); Shop.auth.logout(); });

        // Keep the profile fresh (roles can change: for example the store was approved)
        if (Shop.auth.isLoggedIn()) {
            Shop.auth.refresh().then((fresh) => {
                $('.js-user-name').text(fresh.name);
                $('.js-vendor-only').toggleClass('d-none', !Shop.auth.hasRole('vendor'));
                $('.js-not-vendor').toggleClass('d-none', Shop.auth.hasRole('vendor'));
            }).catch(() => {});

            // The bell: works only when the notifications module is installed (otherwise it stays hidden)
            Shop.api.get('/notifications/unread-count', undefined, { silent: true }).then((res) => {
                $('.js-bell').removeClass('d-none');
                $('.js-bell-count').text(res.data.count).toggleClass('d-none', res.data.count === 0);
            }).catch(() => {});
        }

        // Currency menu
        const $menu = $('.js-currency-menu');
        $menu.html(Shop.currency.list.map((currency) =>
            '<li><button class="dropdown-item ' + (currency.code === Shop.currency.current.code ? 'active' : '') + '" type="button" data-currency="' + Shop.esc(currency.code) + '">'
            + Shop.esc(currency.code) + ' <small class="text-muted">' + Shop.esc(currency.symbol) + '</small></button></li>').join(''));
        $('.js-currency-label').text(Shop.currency.current.code);
        $menu.on('click', '[data-currency]', function () {
            Shop.currency.set($(this).data('currency'));
            $('.js-currency-label').text(Shop.currency.current.code);
            $menu.find('.dropdown-item').removeClass('active').filter('[data-currency="' + $(this).data('currency') + '"]').addClass('active');
        });

        // Search boxes go to the shop page
        $('.js-search-form').on('submit', function (event) {
            event.preventDefault();
            const query = $.trim($(this).find('input[name="q"]').val());
            window.location.href = '/shop' + (query ? '?q=' + encodeURIComponent(query) : '');
        });

        // Back to top
        const $top = $('.back-top');
        $(window).on('scroll', Shop.debounce(() => $top.toggle(window.scrollY > 500), 100));
        $top.on('click', () => $('html, body').animate({ scrollTop: 0 }, 300));
    }

    /* ------------------------------------------------- global click handlers */

    // The "+" button of a product card: add the product (with no variants) to the cart.
    $(document).on('click', '[data-quick-add]', function () {
        const $button = $(this);

        Shop.cart.add({
            product_id: parseInt($button.data('id'), 10),
            variant_id: null,
            quantity: 1,
            snapshot: { name: $button.data('name'), slug: $button.data('slug'), image: $button.data('image'), variant_label: null, price: parseFloat($button.data('price')) },
        });

        Shop.ui.toast(Shop.t('cart.added'), 'success');
    });

    $(document).on('currency:changed', () => Shop.currency.render());

    /* ---------------------------------------------------------------- Start */

    $(function () {
        Shop.currency.load().then(() => {
            initHeader();

            const page = $('body').data('page');

            if (page && typeof Shop.pages[page] === 'function') {
                Shop.pages[page]();
            }

            Shop.currency.render();

            const flash = Shop.store.get('shop_flash', null);

            if (flash) {
                Shop.store.remove('shop_flash');
                Shop.ui.toast(flash.message, flash.type);
            }
        });
    });
})(window, jQuery);
