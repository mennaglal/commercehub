
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
            value="{{ old('name_ar', $city->name_ar ?? '') }}"
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
            value="{{ old('name_en', $city->name_en ?? '') }}"
            required
        >

        @error('name_en')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group col-md-6">
        <label for="country_id">
            {{ __('dashboard.Country') }}
            <span class="text-danger">*</span>
        </label>

        <select name="country_id" id="country_id" class="form-control" required>
            <option value="">{{ __('dashboard.Select country') }}</option>

            @foreach($countries as $option)
                <option
                    value="{{ $option->id }}"
                    {{ (string) old('country_id', $city->country_id ?? '') === (string) $option->id ? 'selected' : '' }}
                >
                    {{ $option->name_en }} ({{ $option->code }})
                </option>
            @endforeach
        </select>

        @error('country_id')
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
                {{ old('is_active', $city->is_active ?? true) ? 'checked' : '' }}
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

    <a href="{{ route('admin.cities.index') }}" class="btn btn-secondary ml-2">
        {{ __('dashboard.Cancel') }}
    </a>
</div>
