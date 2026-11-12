{{--
    Shared by create and edit.
    $product is null on create.

    Variants UI idea:
      - The checkboxes of option values are ONLY a helper (they have no "name", so they are not submitted).
      - "Generate" builds one row per combination.
      - Only the rows are submitted: variants[i][values][], price, discount_price, quantity, sku.
        The server derives the option types from those values.
--}}
@php
    $isEdit = $product !== null;

    // Selected categories (old input wins after a validation error)
    $selectedParentId = old('parent_category_id', $isEdit ? $product->category?->parent_id : '');
    $selectedCategoryId = old('category_id', $isEdit ? $product->category_id : '');

    // Variant rows shown when the page opens:
    // old input after a validation error, otherwise the saved variants of the product.
    $initialRows = old('variants', $isEdit
        ? $product->variants->map(fn ($variant) => [
            'values' => $variant->values->pluck('id')->map(fn ($id) => (string) $id)->all(),
            'sku' => $variant->sku,
            'price' => $variant->price,
            'discount_price' => $variant->discount_price,
            'quantity' => $variant->quantity,
        ])->values()->all()
        : []);

    $hasVariants = (bool) old('has_variants', $isEdit ? $product->has_variants : false);
@endphp

<div class="row">

    {{-- ===================== Basic information ===================== --}}
    <div class="col-12">
        <h5 class="mb-3">{{ __('products.sections.basic') }}</h5>
    </div>

    <div class="col-md-6 mb-3">
        <label for="store_id" class="form-label">{{ __('products.columns.store') }} <span class="text-danger">*</span></label>
        <select name="store_id" id="store_id" class="form-control @error('store_id') is-invalid @enderror" required>
            <option value="">{{ __('products.fields.select_store') }}</option>
            @foreach($stores as $store)
                <option value="{{ $store->id }}" @selected(old('store_id', $product?->store_id) == $store->id)>
                    {{ $store->name }}
                </option>
            @endforeach
        </select>
        @error('store_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="sku" class="form-label">{{ __('products.fields.sku') }} <span class="text-danger">*</span></label>
        <input type="text" name="sku" id="sku"
               class="form-control @error('sku') is-invalid @enderror"
               value="{{ old('sku', $product?->sku) }}" required>
        @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="name_ar" class="form-label">{{ __('products.fields.name_ar') }} <span class="text-danger">*</span></label>
        <input type="text" name="name_ar" id="name_ar"
               class="form-control @error('name_ar') is-invalid @enderror"
               value="{{ old('name_ar', $product?->name_ar) }}" required>
        @error('name_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="name_en" class="form-label">{{ __('products.fields.name_en') }} <span class="text-danger">*</span></label>
        <input type="text" name="name_en" id="name_en"
               class="form-control @error('name_en') is-invalid @enderror"
               value="{{ old('name_en', $product?->name_en) }}" required>
        @error('name_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>


    {{-- ===================== Categories ===================== --}}
    <div class="col-12">
        <hr>
        <h5 class="mb-3">{{ __('products.sections.categories') }}</h5>
    </div>

    <div class="col-md-6 mb-3">
        <label for="parent_category_id" class="form-label">{{ __('products.columns.main_category') }} <span class="text-danger">*</span></label>
        <select name="parent_category_id" id="parent_category_id"
                class="form-control @error('parent_category_id') is-invalid @enderror" required>
            <option value="">{{ __('products.fields.select_main_category') }}</option>
            @foreach($parentCategories as $parentCategory)
                <option value="{{ $parentCategory->id }}"
                    @selected((string) $selectedParentId === (string) $parentCategory->id)>
                    {{ (app()->getLocale()=="ar") ? $parentCategory->name_ar :$parentCategory->name}}
                </option>
            @endforeach
        </select>
        @error('parent_category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="category_id" class="form-label">{{ __('products.columns.subcategory') }} <span class="text-danger">*</span></label>
        <select name="category_id" id="category_id"
                class="form-control @error('category_id') is-invalid @enderror" required>
            <option value="">{{ __('products.fields.select_subcategory') }}</option>
            @foreach($categories as $category)
                {{-- data-parent is used by the JS below to show only the subcategories of the chosen main category --}}
                <option value="{{ $category->id }}" data-parent="{{ $category->parent_id }}"
                    @selected((string) $selectedCategoryId === (string) $category->id)>
                    {{ (app()->getLocale()=="ar") ? $category->name_ar :$category->name}}
                </option>
            @endforeach
        </select>
        @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <small class="text-muted">{{ __('products.fields.category_hint') }}</small>
    </div>

    {{-- ===================== Descriptions ===================== --}}
    <div class="col-12">
        <hr>
        <h5 class="mb-3">{{ __('products.sections.descriptions') }}</h5>
    </div>

    <div class="col-md-6 mb-3">
        <label for="description_ar" class="form-label">{{ __('products.fields.description_ar') }}</label>
        <textarea name="description_ar" id="description_ar" rows="4"
                  class="form-control @error('description_ar') is-invalid @enderror">{{ old('description_ar', $product?->description_ar) }}</textarea>
        @error('description_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="description_en" class="form-label">{{ __('products.fields.description_en') }}</label>
        <textarea name="description_en" id="description_en" rows="4"
                  class="form-control @error('description_en') is-invalid @enderror">{{ old('description_en', $product?->description_en) }}</textarea>
        @error('description_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- ===================== Image and status ===================== --}}
    <div class="col-12">
        <hr>
        <h5 class="mb-3">{{ __('products.sections.image_status') }}</h5>
    </div>

    <div class="col-md-6 mb-3">
        <label for="image" class="form-label">{{ __('products.fields.image') }}<span class="text-danger">*</span></label>
        <input type="file" name="image" id="image" @if(!$isEdit) required @endif
               class="form-control @error('image') is-invalid @enderror"
               accept=".jpg,.jpeg,.png,.webp">
        @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror

        @if($isEdit && $product->image)
            <div class="mt-2">
                <img src="{{ $product->image_url }}" alt="Product image"
                     style="width:100px;height:100px;object-fit:cover" class="rounded border">
            </div>
        @endif
    </div>

    <div class="col-md-6 mb-3">
        <label for="status" class="form-label">{{ __('products.columns.status') }}</label>
        <select name="status" id="status" class="form-control">
            <option value="1" @selected((int) old('status', $product?->status ?? 1) === 1)>{{ __('products.active') }}</option>
            <option value="0" @selected((int) old('status', $product?->status ?? 1) === 0)>{{ __('products.inactive') }}</option>
        </select>
    </div>

    {{-- ===================== Pricing and variants ===================== --}}
    <div class="col-12">
        <hr>
        <h5 class="mb-3">{{ __('products.sections.pricing') }}</h5>
    </div>

    <div class="col-12 mb-3">
        {{-- The hidden input sends 0 when the checkbox is unchecked (an unchecked checkbox sends nothing). --}}
        <input type="hidden" name="has_variants" value="0">
        <div class="form-check">
            <input type="checkbox" name="has_variants" id="has_variants" value="1"
                   class="form-check-input" @checked($hasVariants)>
            <label for="has_variants" class="form-check-label fw-bold">
                {{ __('products.variants.has_variants') }}
            </label>
        </div>
        <small class="text-muted">
            {{ __('products.variants.has_variants_hint') }}
        </small>
    </div>

    {{-- Simple product: shown only when "has variants" is unchecked --}}
    <div class="col-12" id="simple_pricing">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="price" class="form-label">{{ __('products.fields.regular_price') }} <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0" name="price" id="price"
                       class="form-control @error('price') is-invalid @enderror"
                       value="{{ old('price', $product?->has_variants ? '' : $product?->price) }}" required>
                @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="stock" class="form-label">{{ __('products.columns.stock') }} <span class="text-danger">*</span></label>
                <input type="number" min="0" name="stock" id="stock"
                       class="form-control @error('stock') is-invalid @enderror"
                       value="{{ old('stock', $product?->has_variants ? '' : ($product?->stock ?? 0)) }}" required>
                @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 mb-3">
                <div class="form-check">
                    <input type="checkbox" name="is_sale" id="is_sale" value="1" class="form-check-input"
                        @checked(old('is_sale', $product?->is_sale ?? false))>
                    <label for="is_sale" class="form-check-label">{{ __('products.fields.enable_sale') }}</label>
                </div>
            </div>

            <div class="col-md-6 mb-3" id="sale_price_wrapper">
                <label for="sale_price" class="form-label">{{ __('products.fields.sale_price') }}</label>
                <input type="number" step="0.01" min="0" name="sale_price" id="sale_price"
                       class="form-control @error('sale_price') is-invalid @enderror"
                       value="{{ old('sale_price', $product?->sale_price) }}">
                @error('sale_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>

    {{-- Product with variants: shown only when "has variants" is checked --}}
    <div class="col-12" id="variants_section">
        <h6 class="mb-2">1. {{ __('products.variants.step1') }}</h6>
        <p class="text-muted">{{ __('products.variants.step1_hint') }}</p>

        @error('variants')
        <div class="alert alert-danger">{{ $message }}</div>
        @enderror

        @forelse($variantTypes as $variantType)
            {{-- One card per option type (Color, Size...) --}}
            <div class="card mb-3 variant-type-card">
                <div class="card-header fw-bold">
                    {{ $variantType->name}}
                </div>

                <div class="card-body">
                    @if($variantType->values->isEmpty())
                        <p class="text-muted mb-0">{{ __('products.variants.no_values') }}</p>
                    @else
                        <div class="row">
                            @foreach($variantType->values as $value)
                                <div class="col-md-3 col-sm-6 mb-2">
                                    <div class="form-check">
                                        {{-- No "name": this checkbox only feeds the generator --}}
                                        <input type="checkbox"
                                               class="form-check-input variant-value-checkbox"
                                               id="variant_value_{{ $value->id }}"
                                               value="{{ $value->id }}"
                                               data-label="{{ $value->value }}">
                                        <label class="form-check-label" for="variant_value_{{ $value->id }}">
                                            @if($variantType->input_type === 'color' && $value->color_code)
                                                <span style="display:inline-block;width:12px;height:12px;border-radius:50%;border:1px solid #ccc;background:{{ $value->color_code }}"></span>
                                            @endif
                                            {{ $value->value }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="alert alert-info">{{ __('products.variants.no_types') }}</div>
        @endforelse

        <h6 class="mt-4 mb-2">2. {{ __('products.variants.step2') }}</h6>

        <button type="button" id="generate_variants" class="btn btn-outline-primary mb-3">
            {{ __('products.variants.generate') }}
        </button>

        {{-- Filled by JS --}}
        <div id="variants_container"></div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const $ = (id) => document.getElementById(id);

        const parentSelect = $('parent_category_id');
        const categorySelect = $('category_id');
        const hasVariantsBox = $('has_variants');
        const simplePricing = $('simple_pricing');
        const variantsSection = $('variants_section');
        const saleCheckbox = $('is_sale');
        const saleWrapper = $('sale_price_wrapper');
        const salePriceInput = $('sale_price');
        const rowsContainer = $('variants_container');

        // Texts used by this script (from lang/xx/products.php, in the current language).
        const t = @json(__('products.js'));

        // Rows saved in the DB (or sent back after a validation error).
        const initialRows = @json($initialRows);

        // What the admin typed, remembered per combination so "Generate" never wipes it.
        // Key = value ids joined with "-" (example: "3-7"), same idea as the server.
        const typed = {};

        const keyOf = (ids) => ids.map(Number).sort((a, b) => a - b).join('-');

        initialRows.forEach(function (row) {
            typed[keyOf(row.values)] = row;
        });

        /* ---------------- Categories ---------------- */

        // Show only the subcategories that belong to the selected main category.
        function filterSubcategories(resetSelection) {
            let selectedStillAvailable = false;

            Array.from(categorySelect.options).forEach(function (option, index) {
                if (index === 0) {
                    return; // keep the "Select Subcategory" option
                }

                const matches = option.dataset.parent === parentSelect.value;
                option.hidden = !matches;
                option.disabled = !matches;

                if (matches && option.selected) {
                    selectedStillAvailable = true;
                }
            });

            if (resetSelection || !selectedStillAvailable) {
                categorySelect.value = '';
            }
        }

        /* ---------------- Simple product vs product with variants ---------------- */

        // A disabled input is not submitted and is skipped by the browser validation.
        // This is what makes the hidden part safe: hidden "required" inputs would
        // otherwise block the form silently.
        function setDisabled(box, disabled) {
            box.querySelectorAll('input, select, textarea').forEach(function (element) {
                element.disabled = disabled;
            });
        }

        function toggleSalePrice() {
            const enabled = saleCheckbox.checked;

            saleWrapper.style.display = enabled ? '' : 'none';
            salePriceInput.required = enabled;

            if (!enabled) {
                salePriceInput.value = '';
            }
        }

        function toggleMode() {
            const withVariants = hasVariantsBox.checked;

            simplePricing.style.display = withVariants ? 'none' : '';
            variantsSection.style.display = withVariants ? '' : 'none';

            setDisabled(simplePricing, withVariants);
            setDisabled(variantsSection, !withVariants);

            if (!withVariants) {
                toggleSalePrice();
            }
        }

        /* ---------------- Variant rows ---------------- */

        // Ticked values grouped by option type. Types with nothing ticked are skipped.
        function selectedGroups() {
            return Array.from(document.querySelectorAll('.variant-type-card'))
                .map(function (card) {
                    return Array.from(card.querySelectorAll('.variant-value-checkbox:checked'))
                        .map(function (input) {
                            return {id: input.value, label: input.dataset.label};
                        });
                })
                .filter(function (group) {
                    return group.length > 0;
                });
        }

        // [[red, blue], [S, M]] => [[red, S], [red, M], [blue, S], [blue, M]]
        function makeCombinations(groups) {
            return groups.reduce(function (combinations, group) {
                const result = [];

                combinations.forEach(function (combination) {
                    group.forEach(function (item) {
                        result.push([...combination, item]);
                    });
                });

                return result;
            }, [[]]);
        }

        // Remember what is currently typed in the rows before drawing them again.
        function rememberTypedValues() {
            rowsContainer.querySelectorAll('[data-key]').forEach(function (card) {
                typed[card.dataset.key] = {
                    sku: card.querySelector('.v-sku').value,
                    price: card.querySelector('.v-price').value,
                    discount_price: card.querySelector('.v-discount').value,
                    quantity: card.querySelector('.v-quantity').value,
                };
            });
        }

        function renderRows() {
            rememberTypedValues();

            const groups = selectedGroups();

            if (groups.length === 0) {
                rowsContainer.innerHTML = '<div class="alert alert-info">' + t.pick_one + '</div>';
                return;
            }

            rowsContainer.innerHTML = '';

            makeCombinations(groups).forEach(function (combination, index) {
                const key = keyOf(combination.map(item => item.id));
                const saved = typed[key] || {};

                const card = document.createElement('div');
                card.className = 'card mb-3';
                card.dataset.key = key;

                // Header: "Red / أحمر + M" and a remove button
                const header = document.createElement('div');
                header.className = 'card-header d-flex justify-content-between align-items-center';

                const title = document.createElement('strong');
                title.textContent = combination.map(item => item.label).join(' + ');
                header.appendChild(title);

                const removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.className = 'btn btn-sm btn-outline-danger remove-variant';
                removeButton.textContent = t.remove;
                header.appendChild(removeButton);

                card.appendChild(header);

                // Body: the value ids (hidden) + the editable fields
                const body = document.createElement('div');
                body.className = 'card-body row';

                combination.forEach(function (item) {
                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = `variants[${index}][values][]`;
                    hidden.value = item.id;
                    body.appendChild(hidden);
                });

                body.insertAdjacentHTML('beforeend', `
                    <div class="col-md-3 mb-3">
                        <label class="form-label">${t.sku}</label>
                        <input type="text" name="variants[${index}][sku]" class="form-control v-sku">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">${t.price} <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" name="variants[${index}][price]" class="form-control v-price" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">${t.discount_price}</label>
                        <input type="number" step="0.01" min="0" name="variants[${index}][discount_price]" class="form-control v-discount">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">${t.quantity} <span class="text-danger">*</span></label>
                        <input type="number" min="0" name="variants[${index}][quantity]" class="form-control v-quantity" required>
                    </div>
                `);

                // Values are set from JS (not inside the HTML string) so quotes in a SKU cannot break the markup.
                body.querySelector('.v-sku').value = saved.sku ?? '';
                body.querySelector('.v-price').value = saved.price ?? '';
                body.querySelector('.v-discount').value = saved.discount_price ?? '';
                body.querySelector('.v-quantity').value = saved.quantity ?? 0;

                card.appendChild(body);
                rowsContainer.appendChild(card);
            });
        }

        /* ---------------- Events ---------------- */

        parentSelect.addEventListener('change', function () {
            filterSubcategories(true);
        });

        saleCheckbox.addEventListener('change', toggleSalePrice);
        hasVariantsBox.addEventListener('change', toggleMode);
        $('generate_variants').addEventListener('click', renderRows);

        // Remove one combination you do not sell (example: Red + XL).
        // Generating again brings it back, because the ticked values still include it.
        rowsContainer.addEventListener('click', function (event) {
            if (!event.target.classList.contains('remove-variant')) {
                return;
            }

            const card = event.target.closest('[data-key]');
            delete typed[card.dataset.key];
            card.remove();
        });

        /* ---------------- Start ---------------- */

        // Tick the values used by the existing / old variants, then draw their rows.
        // NOTE: a value that became inactive is not listed anymore, so a variant using it
        // will not be drawn and is removed when the product is saved again.
        const usedValueIds = new Set(initialRows.flatMap(row => row.values.map(String)));

        document.querySelectorAll('.variant-value-checkbox').forEach(function (input) {
            input.checked = usedValueIds.has(input.value);
        });

        filterSubcategories(false);
        toggleSalePrice();

        if (initialRows.length > 0) {
            renderRows();
        }

        // Last on purpose: it disables the inputs of the hidden part (including the rows just drawn).
        toggleMode();
    });
</script>
