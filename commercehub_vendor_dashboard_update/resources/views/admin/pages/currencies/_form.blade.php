
<div class="row">

    {{-- Arabic Currency Name --}}
    <div class="form-group col-md-6">
        <label for="name_ar">
            {{ __('dashboard.Currency Name Arabic') }}
            <span class="text-danger">*</span>
        </label>

        <input
            type="text"
            name="name_ar"
            id="name_ar"
            class="form-control"
            value="{{ old('name_ar', $isEdit ? $currency->name_ar : '') }}"
            required
        >

        @error('name_ar')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    {{-- English Currency Name --}}
    <div class="form-group col-md-6">
        <label for="name_en">
            {{ __('dashboard.Currency Name English') }}
            <span class="text-danger">*</span>
        </label>

        <input
            type="text"
            name="name_en"
            id="name_en"
            class="form-control"
            value="{{ old('name_en', $isEdit ? $currency->name_en : '') }}"
            required
        >

        @error('name_en')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    {{-- Currency Code --}}
    <div class="form-group col-md-6">
        <label for="code">
            {{ __('dashboard.Currency Code') }}
            <span class="text-danger">*</span>
        </label>

        <input
            type="text"
            name="code"
            id="code"
            class="form-control text-uppercase"
            maxlength="3"
            pattern="[A-Za-z]{3}"
            value="{{ old('code', $isEdit ? $currency->code : '') }}"
            @if ($isEdit && $currency->code === 'EGP') readonly @endif
            required
        >

        @error('code')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    {{-- Currency Symbol --}}
    <div class="form-group col-md-6">
        <label for="symbol">
            {{ __('dashboard.Currency Symbol') }}
            <span class="text-danger">*</span>
        </label>

        <input
            type="text"
            name="symbol"
            id="symbol"
            class="form-control"
            maxlength="10"
            value="{{ old('symbol', $isEdit ? $currency->symbol : '') }}"
            required
        >

        @error('symbol')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    {{-- Decimal Places --}}
    <div class="form-group col-md-6">
        <label for="decimal_places">
            {{ __('dashboard.Decimal Places') }}
            <span class="text-danger">*</span>
        </label>

        <input
            type="number"
            name="decimal_places"
            id="decimal_places"
            class="form-control"
            min="0"
            max="9"
            step="1"
            value="{{ old('decimal_places', $isEdit ? $currency->decimal_places : 2) }}"
            required
        >

        @error('decimal_places')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    {{-- Exchange Rate --}}
    <div class="form-group col-md-6">
        <label for="rate_to_egp">
            {{ __('dashboard.Exchange rate to EGP') }}
            <span class="text-danger">*</span>
        </label>

        <input
            type="number"
            name="rate_to_egp"
            id="rate_to_egp"
            class="form-control"
            min="0.00000001"
            step="0.00000001"
            value="{{ old('rate_to_egp', $isEdit ? $currency->rate_to_egp : '') }}"
            @if ($isEdit && $currency->code === 'EGP') readonly @else required @endif
        >

        <small class="form-text text-muted">
            @lang('dashboard.exchange_rate_help')
        </small>

        @if (strtoupper(old('code', $currency->code ?? '')) === 'EGP')
            <small class="form-text text-muted">
                @lang('dashboard.egp_exchange_rate_note')
            </small>
        @endif

        @error('rate_to_egp')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    {{-- Active Status --}}
    <div class="form-group col-md-6">
        <label for="is_active">
            {{ __('dashboard.Status') }}
        </label>

        <select
            name="is_active"
            id="is_active"
            class="form-control"
            @if ($isEdit && $currency->code === 'EGP') disabled @endif
        >
            <option
                value="1"
                @selected((string) old('is_active', $isEdit ? (int) $currency->is_active : 1) === '1')
            >
                {{ __('dashboard.Active') }}
            </option>

            <option
                value="0"
                @selected((string) old('is_active', $isEdit ? (int) $currency->is_active : 1) === '0')
            >
                {{ __('dashboard.Inactive') }}
            </option>
        </select>

        @if ($isEdit && $currency->code === 'EGP')
            <small class="form-text text-muted">
                The base currency must remain active.
            </small>
        @endif

        @error('is_active')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

</div>

<div class="mt-3">
    <button type="submit" class="btn btn-primary">
        {{ $isEdit ? __('dashboard.Update') : __('dashboard.Save') }}
    </button>

    <a href="{{ route('admin.currencies.index') }}" class="btn btn-secondary">
        {{ __('dashboard.Cancel') }}
    </a>
</div>
