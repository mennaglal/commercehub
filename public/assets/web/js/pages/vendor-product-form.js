/* Create / edit a product. Two kinds:
     - simple product: price, stock, sale price
     - product with variants: the vendor ticks the values (Red, Blue / S, M ...), "Generate" makes one row per combination,
       and every row has its own SKU, price, discount price and quantity.
   The idea is the same as the admin panel form. The inputs have the real names (variants[0][values][0]) so the
   FormData of the form is sent as it is. */
Shop.pages['vendor-product-form'] = function () {
    if (!Shop.auth.require('vendor')) { return; }

    const id = $('#pf-root').data('id') || null;
    const $form = $('#product-form');
    const $rows = $('#variant-rows');
    const typed = {};          // what the vendor typed, kept per combination so "Generate" never erases it
    let types = [];            // the option types: [{ id, name, input_type, values: [...] }]

    const keyOf = (ids) => ids.map(Number).sort((a, b) => a - b).join('-');

    /* ---------------- category (subcategories only, grouped by their parent) */
    const fillCategories = (categories) => {
        $('#category_id').html('<option value="">' + Shop.esc(Shop.t('vendor.select_category')) + '</option>'
            + categories.filter((parent) => (parent.children || []).length).map((parent) =>
                '<optgroup label="' + Shop.esc(parent.name) + '">' + parent.children.map((child) => '<option value="' + child.id + '">' + Shop.esc(child.name) + '</option>').join('') + '</optgroup>').join(''));
    };

    /* ---------------- the values to tick, one card per type */
    const fillTypes = () => {
        $('#variant-types').html(types.length ? types.map((type) =>
            '<div class="card-soft pad mb-3"><div class="fw-bold mb-2">' + Shop.esc(type.name) + '</div><div class="d-flex flex-wrap gap-3">'
            + type.values.map((value) => '<label class="form-check"><input type="checkbox" class="form-check-input vv-check" value="' + value.id + '" data-label="' + Shop.esc(value.value) + '" data-type="' + type.id + '">'
                + '<span class="form-check-label">' + (type.input_type === 'color' && value.color_code ? '<span style="display:inline-block;width:12px;height:12px;border-radius:50%;border:1px solid #ccc;background:' + Shop.esc(value.color_code) + '"></span> ' : '') + Shop.esc(value.value) + '</span></label>').join('')
            + '</div></div>').join('') : '<div class="alert alert-info">' + Shop.esc(Shop.t('vendor.no_types')) + '</div>');
    };

    // The ticked values grouped by type (a type with nothing ticked is skipped).
    const selectedGroups = () => $('#variant-types .card-soft').map(function () {
        return [$(this).find('.vv-check:checked').map(function () { return { id: parseInt($(this).val(), 10), label: $(this).data('label') }; }).get()];
    }).get().filter((group) => group.length);

    // [[red, blue], [S, M]]  =>  [[red, S], [red, M], [blue, S], [blue, M]]
    const combinations = (groups) => groups.reduce((all, group) => {
        const next = [];
        all.forEach((combo) => group.forEach((item) => next.push(combo.concat([item]))));

        return next;
    }, [[]]);

    const rememberTyped = () => {
        $rows.find('.vrow').each(function () {
            typed[$(this).data('key')] = {
                sku: $(this).find('.v-sku').val(), price: $(this).find('.v-price').val(),
                discount_price: $(this).find('.v-discount').val(), quantity: $(this).find('.v-quantity').val(),
            };
        });
    };

    const renderRows = () => {
        rememberTyped();
        const groups = selectedGroups();

        if (!groups.length) {
            $rows.html('<div class="alert alert-info">' + Shop.esc(Shop.t('vendor.pick_values')) + '</div>');

            return;
        }

        $rows.empty();

        combinations(groups).forEach((combo, index) => {
            const key = keyOf(combo.map((item) => item.id));
            const saved = typed[key] || {};

            const $row = $('<div class="card-soft pad mb-3 vrow"></div>').attr('data-key', key).html(
                '<div class="d-flex justify-content-between align-items-center mb-3"><strong>' + Shop.esc(combo.map((item) => item.label).join(' + ')) + '</strong>'
                + '<button type="button" class="btn btn-outline-danger btn-sm" data-remove-row><i class="bi bi-x-lg"></i></button></div>'
                + combo.map((item, position) => '<input type="hidden" name="variants[' + index + '][values][' + position + ']" value="' + item.id + '">').join('')
                + '<div class="row g-2"><div class="col-6 col-md-3"><label class="form-label small">SKU</label><input class="form-control v-sku" name="variants[' + index + '][sku]"></div>'
                + '<div class="col-6 col-md-3"><label class="form-label small">' + Shop.esc(Shop.t('vendor.price')) + ' *</label><input type="number" step="0.01" min="0" class="form-control v-price" name="variants[' + index + '][price]" required></div>'
                + '<div class="col-6 col-md-3"><label class="form-label small">' + Shop.esc(Shop.t('vendor.discount_price')) + '</label><input type="number" step="0.01" min="0" class="form-control v-discount" name="variants[' + index + '][discount_price]"></div>'
                + '<div class="col-6 col-md-3"><label class="form-label small">' + Shop.esc(Shop.t('vendor.quantity')) + ' *</label><input type="number" min="0" class="form-control v-quantity" name="variants[' + index + '][quantity]" required></div></div>');

            // The values are set from JS (never inside the HTML text), so a quote in a SKU cannot break the markup.
            $row.find('.v-sku').val(saved.sku || '');
            $row.find('.v-price').val(saved.price !== undefined ? saved.price : '');
            $row.find('.v-discount').val(saved.discount_price !== undefined && saved.discount_price !== null ? saved.discount_price : '');
            $row.find('.v-quantity').val(saved.quantity !== undefined ? saved.quantity : 0);
            $rows.append($row);
        });
    };

    $('#generate-variants').on('click', renderRows);

    $rows.on('click', '[data-remove-row]', function () {
        const $row = $(this).closest('.vrow');
        delete typed[$row.data('key')];
        $row.remove();
    });

    /* ---------------- simple product / product with variants */
    const toggleMode = () => {
        const withVariants = $('#has_variants').is(':checked');

        $('#simple-box').toggleClass('d-none', withVariants).find('input').prop('disabled', withVariants);
        $('#variants-box').toggleClass('d-none', !withVariants).find('input').prop('disabled', !withVariants);
        toggleSale();
    };

    // A disabled input is not sent and is skipped by the browser validation (this is why the hidden part is safe).
    const toggleSale = () => {
        const on = $('#is_sale').is(':checked') && !$('#has_variants').is(':checked');

        $('#sale-box').toggleClass('d-none', !on);
        $('#sale_price').prop('disabled', !on).prop('required', on);
    };

    $('#has_variants').on('change', toggleMode);
    $('#is_sale').on('change', toggleSale);

    // The picture: only a preview in the browser
    $('#image').on('change', function () {
        const file = this.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => $('#image-preview').attr('src', e.target.result).removeClass('d-none');
            reader.readAsDataURL(file);
        }
    });

    /* ---------------- start: the lists, then (when editing) the product */
    Promise.all([
        Shop.api.get('/categories'),
        Shop.api.get('/vendor/variant-types'),
        id ? Shop.api.get('/vendor/products/' + id) : Promise.resolve(null),
    ]).then(([categories, variantTypes, product]) => {
        types = variantTypes.data;
        fillCategories(categories.data);
        fillTypes();

        if (product) {
            const data = product.data;

            ['name_ar', 'name_en', 'sku', 'description_ar', 'description_en', 'price', 'stock', 'sale_price'].forEach((field) => $form.find('[name="' + field + '"]').val(data[field] === null ? '' : data[field]));
            $('#category_id').val(data.category_id);
            $('#status').prop('checked', data.status);
            $('#is_sale').prop('checked', data.is_sale);
            $('#has_variants').prop('checked', data.has_variants);

            if (data.image_url) { $('#image-preview').attr('src', data.image_url).removeClass('d-none'); }

            // The saved variants: tick their values and draw their rows with their data.
            (data.variants || []).forEach((variant) => {
                typed[keyOf(variant.values)] = { sku: variant.sku, price: variant.price, discount_price: variant.discount_price, quantity: variant.quantity };
                variant.values.forEach((valueId) => $('#variant-types .vv-check[value="' + valueId + '"]').prop('checked', true));
            });

            if (data.has_variants) { renderRows(); }
        }

        toggleMode();
        $('#pf-loading').addClass('d-none');
        $form.removeClass('d-none');
    }).catch((error) => {
        $('#pf-loading').addClass('d-none');

        if (!Shop.vendor.gate(error, $('#pf-root'))) { Shop.ui.error(error); }
    });

    /* ---------------- save */
    $form.on('submit', function (event) {
        event.preventDefault();
        const $button = $form.find('[type="submit"]');

        Shop.ui.busy($button, true);

        // POST for both create and update (PHP does not read the picture of a PUT request; the server accepts both).
        Shop.api.post(id ? '/vendor/products/' + id : '/vendor/products', new FormData($form[0])).then(() => {
            Shop.flash(Shop.t('vendor.saved'), 'success');
            window.location.href = '/vendor/products';
        }).catch((error) => {
            Shop.ui.busy($button, false);

            if (error.status === 422) {
                Shop.ui.showErrors($form, error.errors);
                const $first = $form.find('.form-errors:not(.d-none), .is-invalid').first();

                if ($first.length) {
                    $('html, body').animate({ scrollTop: $first.offset().top - 120 }, 250);
                }
            } else {
                Shop.ui.error(error);
            }
        });
    });
};
