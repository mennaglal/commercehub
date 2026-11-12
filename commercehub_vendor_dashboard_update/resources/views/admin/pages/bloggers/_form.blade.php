{{-- Shared by create and edit. $blogger is null on create. --}}
@php($blogger = $blogger ?? null)

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="name" class="form-label">{{ __('bloggers.fields.name') }} <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $blogger?->name) }}" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="phone" class="form-label">{{ __('bloggers.fields.phone') }}</label>
        <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror"
               value="{{ old('phone', $blogger?->phone) }}">
        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="email" class="form-label">{{ __('bloggers.fields.email') }}</label>
        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $blogger?->email) }}">
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6 mb-3 d-flex align-items-end">
        {{-- The hidden input sends 0 when the checkbox is unchecked. --}}
        <input type="hidden" name="status" value="0">
        <div class="form-check">
            <input type="checkbox" name="status" id="status" value="1" class="form-check-input"
                   @checked(old('status', $blogger?->status ?? true))>
            <label for="status" class="form-check-label">{{ __('bloggers.fields.status') }}</label>
        </div>
    </div>
</div>
