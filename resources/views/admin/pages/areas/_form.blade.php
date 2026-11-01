
<div class="form-row">
    <div class="form-group col-md-6">
        <label for="name_ar">
            {{ __('dashboard.Arabic name') }}
            <span class="text-danger">*</span>
        </label>

        <input
            type="text"
            name="name_ar"
            id="name_ar"
            class="form-control"
            value="{{ old('name_ar', $area->name_ar ?? '') }}"
            required
        >

        @error('name_ar')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group col-md-6">
        <label for="name_en">
            {{ __('dashboard.English name') }}
            <span class="text-danger">*</span>
        </label>

        <input
            type="text"
            name="name_en"
            id="name_en"
            class="form-control"
            value="{{ old('name_en', $area->name_en ?? '') }}"
            required
        >

        @error('name_en')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group col-md-6">
        <label for="city_id">
            {{ __('dashboard.City') }}
            <span class="text-danger">*</span>
        </label>

        <select name="city_id" id="city_id" class="form-control" required>
            <option value="">{{ __('dashboard.Select city') }}</option>

            @foreach($cities as $option)
                <option
                    value="{{ $option->id }}"
                    {{ (string) old('city_id', $area->city_id ?? '') === (string) $option->id ? 'selected' : '' }}
                >
                    {{ $option->name }} — {{ $option->country?->name ?? '-' }}
                </option>
            @endforeach
        </select>

        @error('city_id')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="form-group col-md-6">
        <label for="shipping_price">
            {{ __('dashboard.Shipping price') }}
            <span class="text-danger">*</span>
        </label>

        <input
            type="number"
            name="shipping_price"
            id="shipping_price"
            class="form-control"
            value="{{ old('shipping_price', $area->shipping_price ?? '0.00') }}"
            min="0"
            max="999999.99"
            step="0.01"
            required
        >

        @error('shipping_price')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group col-md-6">
        <label for="is_active">{{ __('dashboard.Status') }}</label>

        <div class="custom-control custom-checkbox">
            <input
                type="checkbox"
                name="is_active"
                value="1"
                id="is_active"
                class="custom-control-input"
                {{ old('is_active', $area->is_active ?? true) ? 'checked' : '' }}
            >

            <label class="custom-control-label" for="is_active">
                {{ __('dashboard.Active') }}
            </label>
        </div>
    </div>
</div>

<div class="text-center mt-3">
    <button type="submit" class="btn btn-primary">
        {{ $isEdit ? __('dashboard.Update') : __('dashboard.Create') }}
    </button>

    <a href="{{ route('admin.areas.index') }}" class="btn btn-secondary ml-2">
        {{ __('dashboard.Cancel') }}
    </a>
</div>
