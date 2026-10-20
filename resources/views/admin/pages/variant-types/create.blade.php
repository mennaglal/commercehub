
@extends('admin.layouts.app')

@section('title', __('dashboard.variant_types.add'))

@section('content')
    @include('includes.error')
    @include('includes.success')

    <div class="card shadow mb-4">
        <div class="card-header">
            <h5 class="mb-0">{{ __('dashboard.variant_types.add') }}</h5>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.variant-types.store') }}">
                @csrf

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>{{ __('dashboard.variant_types.name_ar') }}</label>
                        <input
                            type="text"
                            name="name_ar"
                            class="form-control"
                            value="{{ old('name_ar') }}"
                            required
                        >
                        @error('name_ar')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group col-md-6">
                        <label>{{ __('dashboard.variant_types.name_en') }}</label>
                        <input
                            type="text"
                            name="name_en"
                            class="form-control"
                            value="{{ old('name_en') }}"
                            required
                        >
                        @error('name_en')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group col-md-6">
                        <label>{{ __('dashboard.variant_types.input_type') }}</label>
                        <select name="input_type" class="form-control" required>
                            <option value="text" @selected(old('input_type', 'text') === 'text')>
                                {{ __('dashboard.variant_types.text_type') }}
                            </option>
                            <option value="color" @selected(old('input_type') === 'color')>
                                {{ __('dashboard.variant_types.color_type') }}
                            </option>
                        </select>
                        @error('input_type')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group col-md-6">
                        <label>{{ __('dashboard.variant_types.status') }}</label>
                        <select name="status" class="form-control" required>
                            <option value="1" @selected(old('status', '1') === '1')>
                                {{ __('dashboard.variant_types.active') }}
                            </option>
                            <option value="0" @selected(old('status') === '0')>
                                {{ __('dashboard.variant_types.inactive') }}
                            </option>
                        </select>
                        @error('status')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    {{ __('dashboard.variant_types.save') }}
                </button>

                <a href="{{ route('admin.variant-types.index') }}" class="btn btn-light">
                    {{ __('dashboard.variant_types.cancel') }}
                </a>
            </form>
        </div>
    </div>
@endsection
