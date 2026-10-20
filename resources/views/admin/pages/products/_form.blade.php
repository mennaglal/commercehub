
@php
    $isEdit = isset($product) && $product !== null;

    $selectedParentId = old(
        'parent_category_id',
        $isEdit ? $product->category?->parent_id : ''
    );

    $selectedCategoryId = old(
        'category_id',
        $isEdit ? $product->category_id : ''
    );

    $selectedTypeIds = old(
        'variant_types',
        $isEdit ? $product->variantTypes->pluck('id')->all() : []
    );

    $initialVariants = old('variants');

    if ($initialVariants === null) {
        $initialVariants = $isEdit
            ? $product->variants->map(function ($variant) {
                return [
                    'values' => $variant->values->pluck('id')->map(fn ($id) => (string) $id)->all(),
                    'price' => $variant->price,
                    'discount_price' => $variant->discount_price,
                    'quantity' => $variant->quantity,
                    'sku' => $variant->sku,
                ];
            })->values()->all()
            : [];
    }

    $selectedValueIds = old('variant_values');

    if ($selectedValueIds === null) {
        $selectedValueIds = collect($initialVariants)
            ->pluck('values')
            ->flatten()
            ->unique()
            ->values()
            ->all();

        $selectedValueIds = collect($selectedValueIds)
            ->groupBy(function ($valueId) use ($variantTypes) {
                foreach ($variantTypes as $type) {
                    if ($type->values->contains('id', (int) $valueId)) {
                        return $type->id;
                    }
                }

                return 0;
            })
            ->map(fn ($values) => $values->all())
            ->all();
    }

    $selectedValueIds = collect($selectedValueIds)
        ->flatten()
        ->map(fn ($id) => (string) $id)
        ->all();
@endphp

<div class="row">
    <div class="col-12">
        <h5 class="mb-3">Basic Information</h5>
    </div>

    <div class="col-md-6 mb-3">
        <label for="store_id" class="form-label">Store <span class="text-danger">*</span></label>
        <select name="store_id" id="store_id" class="form-control @error('store_id') is-invalid @enderror" required>
            <option value="">Select Store</option>
            @foreach($stores as $store)
                <option value="{{ $store->id }}"
                    @selected(old('store_id', $product->store_id ?? '') == $store->id)>
                    {{ $store->name }}
                </option>
            @endforeach
        </select>
        @error('store_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="sku" class="form-label">SKU <span class="text-danger">*</span></label>
        <input type="text" name="sku" id="sku"
               class="form-control @error('sku') is-invalid @enderror"
               value="{{ old('sku', $product->sku ?? '') }}" required>
        @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="name_ar" class="form-label">Product Name (Arabic) <span class="text-danger">*</span></label>
        <input type="text" name="name_ar" id="name_ar"
               class="form-control @error('name_ar') is-invalid @enderror"
               value="{{ old('name_ar', $product->name_ar ?? '') }}" required>
        @error('name_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="name_en" class="form-label">Product Name (English) <span class="text-danger">*</span></label>
        <input type="text" name="name_en" id="name_en"
               class="form-control @error('name_en') is-invalid @enderror"
               value="{{ old('name_en', $product->name_en ?? '') }}" required>
        @error('name_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="slug" class="form-label">Slug</label>
        <input type="text" name="slug" id="slug"
               class="form-control @error('slug') is-invalid @enderror"
               value="{{ old('slug', $product->slug ?? '') }}">
        @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12">
        <hr>
        <h5 class="mb-3">Product Categories</h5>
    </div>

    <div class="col-md-6 mb-3">
        <label for="parent_category_id" class="form-label">
            Main Category <span class="text-danger">*</span>
        </label>

        <select name="parent_category_id" id="parent_category_id"
                class="form-control @error('parent_category_id') is-invalid @enderror" required>
            <option value="">Select Main Category</option>
            @foreach($parentCategories as $parentCategory)
                <option value="{{ $parentCategory->id }}"
                    @selected((string) $selectedParentId === (string) $parentCategory->id)>
                    {{ $parentCategory->name_en }} / {{ $parentCategory->name_ar }}
                </option>
            @endforeach
        </select>

        @error('parent_category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="category_id" class="form-label">
            Subcategory <span class="text-danger">*</span>
        </label>

        <select name="category_id" id="category_id"
                class="form-control @error('category_id') is-invalid @enderror" required>
            <option value="">Select Subcategory</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}"
                        data-parent="{{ $category->parent_id }}"
                    @selected((string) $selectedCategoryId === (string) $category->id)>
                    {{ $category->name_en }} / {{ $category->name_ar }}
                </option>
            @endforeach
        </select>

        @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <small class="text-muted">Select a main category first to filter its subcategories.</small>
    </div>

    <div class="col-12">
        <hr>
        <h5 class="mb-3">Descriptions</h5>
    </div>

    <div class="col-md-6 mb-3">
        <label for="description_ar" class="form-label">Description (Arabic)</label>
        <textarea name="description_ar" id="description_ar" rows="4"
                  class="form-control @error('description_ar') is-invalid @enderror">{{ old('description_ar', $product->description_ar ?? '') }}</textarea>
        @error('description_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="description_en" class="form-label">Description (English)</label>
        <textarea name="description_en" id="description_en" rows="4"
                  class="form-control @error('description_en') is-invalid @enderror">{{ old('description_en', $product->description_en ?? '') }}</textarea>
        @error('description_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12">
        <hr>
        <h5 class="mb-3">Pricing and Inventory</h5>
    </div>

    <div class="col-md-4 mb-3">
        <label for="price" class="form-label">Regular Price <span class="text-danger">*</span></label>
        <input type="number" step="0.01" min="0" name="price" id="price"
               class="form-control @error('price') is-invalid @enderror"
               value="{{ old('price', $product->price ?? '') }}" required>
        @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="stock" class="form-label">Stock <span class="text-danger">*</span></label>
        <input type="number" min="0" name="stock" id="stock"
               class="form-control @error('stock') is-invalid @enderror"
               value="{{ old('stock', $product->stock ?? 0) }}" required>
        @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <small class="text-muted">When variants are added, stock is calculated from their quantities.</small>
    </div>

    <div class="col-md-4 mb-3">
        <label for="image" class="form-label">Product Image</label>
        <input type="file" name="image" id="image"
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

    <div class="col-12 mb-3">
        <div class="form-check">
            <input type="checkbox" name="is_sale" id="is_sale" value="1"
                   class="form-check-input"
                @checked(old('is_sale', $product->is_sale ?? false))>
            <label for="is_sale" class="form-check-label">Enable Sale Price</label>
        </div>
    </div>

    <div class="col-md-4 mb-3" id="sale_price_wrapper">
        <label for="sale_price" class="form-label">Sale Price</label>
        <input type="number" step="0.01" min="0" name="sale_price" id="sale_price"
               class="form-control @error('sale_price') is-invalid @enderror"
               value="{{ old('sale_price', $product->sale_price ?? '') }}">
        @error('sale_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="status" class="form-label">Status</label>
        <select name="status" id="status" class="form-control">
            <option value="1" @selected(old('status', $product->status ?? 1) == 1)>Active</option>
            <option value="0" @selected((string) old('status', $product->status ?? 1) === '0')>Inactive</option>
        </select>
    </div>

    <div class="col-12">
        <hr>
        <h5 class="mb-2">Product Variants</h5>
        <p class="text-muted">Choose variant types and values, then generate combinations.</p>
    </div>

    @error('variant_types')
    <div class="col-12"><div class="alert alert-danger">{{ $message }}</div></div>
    @enderror

    @error('variant_values')
    <div class="col-12"><div class="alert alert-danger">{{ $message }}</div></div>
    @enderror

    <div class="col-12 mb-3">
        @forelse($variantTypes as $variantType)
            <div class="card mb-3">
                <div class="card-header">
                    <div class="form-check">
                        <input type="checkbox"
                               class="form-check-input variant-type-checkbox"
                               id="variant_type_{{ $variantType->id }}"
                               name="variant_types[]"
                               value="{{ $variantType->id }}"
                            @checked(in_array((string) $variantType->id, array_map('strval', $selectedTypeIds), true))>
                        <label class="form-check-label fw-bold" for="variant_type_{{ $variantType->id }}">
                            {{ $variantType->name_en }} / {{ $variantType->name_ar }}
                        </label>
                    </div>
                </div>

                <div class="card-body">
                    @if($variantType->values->isEmpty())
                        <p class="text-muted mb-0">No active values for this type.</p>
                    @else
                        <div class="row">
                            @foreach($variantType->values as $value)
                                <div class="col-md-3 col-sm-6 mb-2">
                                    <div class="form-check">
                                        <input type="checkbox"
                                               class="form-check-input variant-value-checkbox"
                                               id="variant_value_{{ $value->id }}"
                                               data-type="{{ $variantType->id }}"
                                               name="variant_values[{{ $variantType->id }}][]"
                                               value="{{ $value->id }}"
                                            @checked(in_array((string) $value->id, $selectedValueIds, true))>
                                        <label class="form-check-label" for="variant_value_{{ $value->id }}">
                                            {{ $value->name_en }} / {{ $value->name_ar }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="alert alert-info">No active variant types are available.</div>
        @endforelse
    </div>

    <div class="col-12 mb-3">
        <button type="button" id="generate_variants" class="btn btn-outline-primary">
            Generate Variant Combinations
        </button>
    </div>

    <div class="col-12">
        @error('variants')
        <div class="alert alert-danger">{{ $message }}</div>
        @enderror

        <div id="variants_container"></div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const parentSelect = document.getElementById('parent_category_id');
        const categorySelect = document.getElementById('category_id');
        const saleCheckbox = document.getElementById('is_sale');
        const saleWrapper = document.getElementById('sale_price_wrapper');
        const salePriceInput = document.getElementById('sale_price');
        const variantsContainer = document.getElementById('variants_container');
        const generateButton = document.getElementById('generate_variants');

        const initialVariants = @json($initialVariants);
        const oldVariantTypes = @json(array_map('strval', $selectedTypeIds));

        function filterSubcategories(resetSelection = false) {
            const parentId = parentSelect.value;
            let selectedStillAvailable = false;

            Array.from(categorySelect.options).forEach(function (option, index) {
                if (index === 0) {
                    option.hidden = false;
                    option.disabled = false;
                    return;
                }

                const matches = option.dataset.parent === parentId;
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

        function toggleSalePrice() {
            const enabled = saleCheckbox.checked;
            saleWrapper.style.display = enabled ? '' : 'none';
            salePriceInput.required = enabled;

            if (!enabled) {
                salePriceInput.value = '';
            }
        }

        function getSelectedTypes() {
            return Array.from(document.querySelectorAll('.variant-type-checkbox:checked'))
                .map(input => input.value);
        }

        function getSelectedValuesByType() {
            const selected = {};

            document.querySelectorAll('.variant-value-checkbox:checked').forEach(function (input) {
                const typeId = input.dataset.type;

                if (!selected[typeId]) {
                    selected[typeId] = [];
                }

                selected[typeId].push({
                    id: input.value,
                    label: input.nextElementSibling.textContent.trim()
                });
            });

            return selected;
        }

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

        function renderVariants() {
            const typeIds = getSelectedTypes();
            const valuesByType = getSelectedValuesByType();

            if (typeIds.length === 0) {
                variantsContainer.innerHTML = '<div class="alert alert-info">Select at least one variant type.</div>';
                return;
            }

            const groups = [];

            for (const typeId of typeIds) {
                if (!valuesByType[typeId] || valuesByType[typeId].length === 0) {
                    variantsContainer.innerHTML = '<div class="alert alert-warning">Select at least one value for every selected variant type.</div>';
                    return;
                }

                groups.push(valuesByType[typeId]);
            }

            const combinations = makeCombinations(groups);
            const existingByKey = {};

            initialVariants.forEach(function (variant) {
                const key = (variant.values || []).map(String).sort().join('-');
                existingByKey[key] = variant;
            });

            variantsContainer.innerHTML = '';

            combinations.forEach(function (combination, index) {
                const ids = combination.map(item => String(item.id)).sort();
                const key = ids.join('-');
                const existing = existingByKey[key] || {};
                const labels = combination.map(item => item.label).join(' + ');

                const card = document.createElement('div');
                card.className = 'card mb-3';

                const header = document.createElement('div');
                header.className = 'card-header fw-bold';
                header.textContent = labels;
                card.appendChild(header);

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
                    <label class="form-label">SKU</label>
                    <input type="text" name="variants[${index}][sku]" class="form-control">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Price <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" name="variants[${index}][price]" class="form-control" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Discount Price</label>
                    <input type="number" step="0.01" min="0" name="variants[${index}][discount_price]" class="form-control">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Quantity <span class="text-danger">*</span></label>
                    <input type="number" min="0" name="variants[${index}][quantity]" class="form-control" required>
                </div>
            `);

                body.querySelector(`[name="variants[${index}][sku]"]`).value = existing.sku || '';
                body.querySelector(`[name="variants[${index}][price]"]`).value = existing.price ?? '';
                body.querySelector(`[name="variants[${index}][discount_price]"]`).value = existing.discount_price ?? '';
                body.querySelector(`[name="variants[${index}][quantity]"]`).value = existing.quantity ?? 0;

                card.appendChild(body);
                variantsContainer.appendChild(card);
            });
        }

        parentSelect.addEventListener('change', function () {
            filterSubcategories(true);
        });

        saleCheckbox.addEventListener('change', toggleSalePrice);

        document.querySelectorAll('.variant-type-checkbox').forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                if (!checkbox.checked) {
                    document.querySelectorAll(`.variant-value-checkbox[data-type="${checkbox.value}"]`)
                        .forEach(input => input.checked = false);
                }
            });
        });

        generateButton.addEventListener('click', renderVariants);

        filterSubcategories(false);
        toggleSalePrice();

        if (initialVariants.length > 0) {
            renderVariants();
        } else if (oldVariantTypes.length > 0) {
            renderVariants();
        }
    });
</script>
