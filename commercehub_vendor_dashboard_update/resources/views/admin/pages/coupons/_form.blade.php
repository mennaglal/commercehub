{{--
    Shared by create and edit. $coupon is null on create.
    $categories = [id => "Parent / Child"], $bloggers = list of bloggers.
--}}
@php
    $coupon = $coupon ?? null;

    // Selected categories (old input wins after a validation error)
    $selectedCategories = collect(old('categories', $coupon ? $coupon->categories->pluck('id')->all() : []))
        ->map(fn ($id) => (string) $id)
        ->all();

    $dateValue = fn ($field) => old($field, $coupon?->{$field}?->format('Y-m-d\TH:i'));
@endphp

<div class="row">

    {{-- ===================== Discount ===================== --}}
    <div class="col-12">
        <h5 class="mb-3">{{ __('coupons.sections.discount') }}</h5>
    </div>

    <div class="col-md-4 mb-3">
        <label for="code" class="form-label">{{ __('coupons.fields.code') }} <span class="text-danger">*</span></label>
        <input type="text" name="code" id="code" class="form-control text-uppercase @error('code') is-invalid @enderror"
               value="{{ old('code', $coupon?->code) }}" maxlength="50" required>
        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="type" class="form-label">{{ __('coupons.fields.type') }} <span class="text-danger">*</span></label>
        <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
            @foreach(\App\Models\Coupon::TYPES as $type)
                <option value="{{ $type }}" @selected(old('type', $coupon?->type ?? 'percentage') === $type)>
                    {{ __('coupons.types.' . $type) }}
                </option>
            @endforeach
        </select>
        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="value" class="form-label">{{ __('coupons.fields.value') }} <span class="text-danger">*</span></label>
        <input type="number" step="0.01" min="0" name="value" id="value"
               class="form-control @error('value') is-invalid @enderror"
               value="{{ old('value', $coupon?->value) }}" required>
        @error('value') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Shown only for percentage coupons (JS below) --}}
    <div class="col-md-4 mb-3" id="max_discount_box">
        <label for="max_discount" class="form-label">{{ __('coupons.fields.max_discount') }}</label>
        <input type="number" step="0.01" min="0" name="max_discount" id="max_discount"
               class="form-control @error('max_discount') is-invalid @enderror"
               value="{{ old('max_discount', $coupon?->max_discount) }}">
        @error('max_discount') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <small class="text-muted">{{ __('coupons.fields.max_discount_hint') }}</small>
    </div>

    <div class="col-md-4 mb-3">
        <label for="min_order_amount" class="form-label">{{ __('coupons.fields.min_order_amount') }}</label>
        <input type="number" step="0.01" min="0" name="min_order_amount" id="min_order_amount"
               class="form-control @error('min_order_amount') is-invalid @enderror"
               value="{{ old('min_order_amount', $coupon?->min_order_amount) }}">
        @error('min_order_amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <small class="text-muted">{{ __('coupons.fields.min_order_hint') }}</small>
    </div>

    {{-- ===================== Limits and dates ===================== --}}
    <div class="col-12">
        <hr>
        <h5 class="mb-3">{{ __('coupons.sections.limits') }}</h5>
    </div>

    <div class="col-md-3 mb-3">
        <label for="usage_limit" class="form-label">{{ __('coupons.fields.usage_limit') }}</label>
        <input type="number" min="1" name="usage_limit" id="usage_limit"
               class="form-control @error('usage_limit') is-invalid @enderror"
               value="{{ old('usage_limit', $coupon?->usage_limit) }}">
        @error('usage_limit') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <small class="text-muted">{{ __('coupons.fields.usage_limit_hint') }}</small>
    </div>

    <div class="col-md-3 mb-3">
        <label for="starts_at" class="form-label">{{ __('coupons.fields.starts_at') }}</label>
        <input type="datetime-local" name="starts_at" id="starts_at"
               class="form-control @error('starts_at') is-invalid @enderror" value="{{ $dateValue('starts_at') }}">
        @error('starts_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3 mb-3">
        <label for="expires_at" class="form-label">{{ __('coupons.fields.expires_at') }}</label>
        <input type="datetime-local" name="expires_at" id="expires_at"
               class="form-control @error('expires_at') is-invalid @enderror" value="{{ $dateValue('expires_at') }}">
        @error('expires_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3 mb-3 d-flex align-items-end">
        {{-- The hidden input sends 0 when the checkbox is unchecked. --}}
        <input type="hidden" name="status" value="0">
        <div class="form-check">
            <input type="checkbox" name="status" id="status" value="1" class="form-check-input"
                   @checked(old('status', $coupon?->status ?? true))>
            <label for="status" class="form-check-label">{{ __('coupons.fields.status') }}</label>
        </div>
    </div>

    {{-- ===================== Categories ===================== --}}

    <div class="col-12">
        <hr>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <h5 class="mb-0">
                {{ __('coupons.sections.categories') }}
                <span class="text-danger">*</span>
            </h5>

            <button type="button" class="btn btn-sm btn-outline-primary"
                    id="toggle_all_categories">
                @lang('dashboard.select_all')
            </button>
        </div>
    </div>

    <div class="col-12 mb-3">
        <div class="row g-2" id="categories_list">
            @foreach($categories as $id => $label)
                <div class="col-12 col-sm-6 col-lg-4">
                    <label for="category_{{ $id }}"
                           class="category-checkbox-card d-flex align-items-center gap-2 p-3 border rounded h-100">

                        <input
                            type="checkbox"
                            name="categories[]"
                            id="category_{{ $id }}"
                            value="{{ $id }}"
                            class="form-check-input mt-0 category-checkbox"
                            @checked(in_array((string) $id, $selectedCategories, true))
                        >

                        <span>{{ $label }}</span>
                    </label>
                </div>
            @endforeach
        </div>

        @error('categories')
        <div class="text-danger small mt-2">{{ $message }}</div>
        @enderror

        @error('categories.*')
        <div class="text-danger small mt-2">{{ $message }}</div>
        @enderror

        <small class="text-muted d-block mt-2">
            {{ __('coupons.fields.categories_hint') }}
        </small>
    </div>


    {{-- ===================== Blogger and commission ===================== --}}
    <div class="col-12">
        <hr>
        <h5 class="mb-3">{{ __('coupons.sections.blogger') }}</h5>
    </div>

    <div class="col-md-4 mb-3">
        <label for="blogger_id" class="form-label">{{ __('coupons.fields.blogger') }}</label>
        <select name="blogger_id" id="blogger_id" class="form-control @error('blogger_id') is-invalid @enderror">
            <option value="">{{ __('coupons.fields.general_option') }}</option>
            @foreach($bloggers as $blogger)
                <option value="{{ $blogger->id }}" @selected((string) old('blogger_id', $coupon?->blogger_id) === (string) $blogger->id)>
                    {{ $blogger->name }}
                </option>
            @endforeach
        </select>
        @error('blogger_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Shown only when a blogger is selected (JS below) --}}
    <div class="col-md-8" id="commission_box">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="commission_type" class="form-label">{{ __('coupons.fields.commission_type') }} <span class="text-danger">*</span></label>
                <select name="commission_type" id="commission_type" class="form-control @error('commission_type') is-invalid @enderror">
                    @foreach(\App\Models\Coupon::TYPES as $type)
                        <option value="{{ $type }}" @selected(old('commission_type', $coupon?->commission_type ?? 'percentage') === $type)>
                            {{ __('coupons.types.' . $type) }}
                        </option>
                    @endforeach
                </select>
                @error('commission_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="commission_value" class="form-label">{{ __('coupons.fields.commission_value') }} <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0" name="commission_value" id="commission_value"
                       class="form-control @error('commission_value') is-invalid @enderror"
                       value="{{ old('commission_value', $coupon?->commission_value) }}">
                @error('commission_value') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 mb-3">
                <small class="text-muted">{{ __('coupons.fields.commission_hint') }}</small>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const typeSelect = document.getElementById('type');
        const maxDiscountBox = document.getElementById('max_discount_box');
        const maxDiscountInput = document.getElementById('max_discount');
        const bloggerSelect = document.getElementById('blogger_id');
        const commissionBox = document.getElementById('commission_box');
        const commissionType = document.getElementById('commission_type');
        const commissionValue = document.getElementById('commission_value');

        // "Maximum discount" only makes sense for a percentage coupon.
        function toggleMaxDiscount() {
            const isPercentage = typeSelect.value === 'percentage';

            maxDiscountBox.style.display = isPercentage ? '' : 'none';

            if (!isPercentage) {
                maxDiscountInput.value = '';
            }
        }

        // The commission fields are needed only with a blogger. A disabled field is not submitted
        // and is skipped by the browser validation (same trick as the product variants form).
        function toggleCommission() {
            const hasBlogger = bloggerSelect.value !== '';

            commissionBox.style.display = hasBlogger ? '' : 'none';
            commissionType.disabled = !hasBlogger;
            commissionValue.disabled = !hasBlogger;
            commissionType.required = hasBlogger;
            commissionValue.required = hasBlogger;
        }

        typeSelect.addEventListener('change', toggleMaxDiscount);
        bloggerSelect.addEventListener('change', toggleCommission);

        toggleMaxDiscount();
        toggleCommission();
    });


    const categoriesList = document.getElementById('categories_list');
    const toggleAllCategoriesButton = document.getElementById('toggle_all_categories');

    if (categoriesList && toggleAllCategoriesButton) {
        const categoryCheckboxes = categoriesList.querySelectorAll('.category-checkbox');

        function updateCategoriesButton() {
            const allChecked = categoryCheckboxes.length > 0 &&
                Array.from(categoryCheckboxes).every(checkbox => checkbox.checked);

            toggleAllCategoriesButton.textContent = allChecked
                ? @json(__('dashboard.deselect_all'))
                : @json(__('dashboard.select_all'));
        }

        toggleAllCategoriesButton.addEventListener('click', function () {
            const allChecked = Array.from(categoryCheckboxes)
                .every(checkbox => checkbox.checked);

            categoryCheckboxes.forEach(checkbox => {
                checkbox.checked = !allChecked;
            });

            updateCategoriesButton();
        });

        categoryCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateCategoriesButton);
        });

        updateCategoriesButton();
    }

</script>
