
@extends('admin.layouts.app')

@section('title', __('dashboard.variant_types.edit'))

@section('content')
    @include('includes.error')
    @include('includes.success')

    {{-- Edit variant type --}}
    <div class="card shadow mb-4">
        <div class="card-header">
            <h5 class="mb-0">{{ __('dashboard.variant_types.edit') }}</h5>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.variant-types.update', $variantType) }}">
                @csrf
                @method('PUT')

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>{{ __('dashboard.variant_types.name_ar') }}</label>
                        <input
                            type="text"
                            name="name_ar"
                            class="form-control"
                            value="{{ old('name_ar', $variantType->name_ar) }}"
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
                            value="{{ old('name_en', $variantType->name_en) }}"
                            required
                        >
                        @error('name_en')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group col-md-6">
                        <label>{{ __('dashboard.variant_types.input_type') }}</label>
                        <select name="input_type" class="form-control" required>
                            <option
                                value="text"
                                @selected(old('input_type', $variantType->input_type) === 'text')
                            >
                                {{ __('dashboard.variant_types.text_type') }}
                            </option>

                            <option
                                value="color"
                                @selected(old('input_type', $variantType->input_type) === 'color')
                            >
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
                            <option
                                value="1"
                                @selected((string) old('status', (int) $variantType->status) === '1')
                            >
                                {{ __('dashboard.variant_types.active') }}
                            </option>

                            <option
                                value="0"
                                @selected((string) old('status', (int) $variantType->status) === '0')
                            >
                                {{ __('dashboard.variant_types.inactive') }}
                            </option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    {{ __('dashboard.variant_types.update') }}
                </button>

                <a href="{{ route('admin.variant-types.index') }}" class="btn btn-light">
                    {{ __('dashboard.variant_types.cancel') }}
                </a>
            </form>
        </div>
    </div>

    {{-- Add a value --}}
    <div class="card shadow mb-4">
        <div class="card-header">
            <h5 class="mb-0">{{ __('dashboard.variant_types.add_value') }}</h5>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.variant-types.values.store', $variantType) }}">
                @csrf

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>{{ __('dashboard.variant_types.value_ar') }}</label>
                        <input
                            type="text"
                            name="value_ar"
                            class="form-control"
                            value="{{ old('value_ar') }}"
                            required
                        >
                        @error('value_ar')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group col-md-4">
                        <label>{{ __('dashboard.variant_types.value_en') }}</label>
                        <input
                            type="text"
                            name="value_en"
                            class="form-control"
                            value="{{ old('value_en') }}"
                            required
                        >
                        @error('value_en')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    @if($variantType->input_type === 'color')
                        <div class="form-group col-md-4">
                            <label>{{ __('dashboard.variant_types.color_code') }}</label>
                            <input
                                type="color"
                                name="color_code"
                                class="form-control"
                                value="{{ old('color_code', '#000000') }}"
                                required
                            >
                            @error('color_code')
                            <small class="text-danger d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    @endif

                    <div class="form-group col-md-4">
                        <label>{{ __('dashboard.variant_types.status') }}</label>
                        <select name="status" class="form-control" required>
                            <option value="1" @selected(old('status', '1') === '1')>
                                {{ __('dashboard.variant_types.active') }}
                            </option>
                            <option value="0" @selected(old('status') === '0')>
                                {{ __('dashboard.variant_types.inactive') }}
                            </option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-success">
                    {{ __('dashboard.variant_types.add_value') }}
                </button>
            </form>
        </div>
    </div>

    {{-- Existing values --}}
    <div class="card shadow mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                {{ __('dashboard.variant_types.values') }} — {{ $variantType->name_ar }}
            </h5>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('dashboard.variant_types.value_ar') }}</th>
                        <th>{{ __('dashboard.variant_types.value_en') }}</th>

                        @if($variantType->input_type === 'color')
                            <th>{{ __('dashboard.variant_types.color_code') }}</th>
                        @endif

                        <th>{{ __('dashboard.variant_types.status') }}</th>
                        <th>{{ __('dashboard.variant_types.actions') }}</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse($variantType->values as $value)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td colspan="{{ $variantType->input_type === 'color' ? 5 : 4 }}">
                                <form
                                    method="POST"
                                    action="{{ route('admin.variant-types.values.update', [$variantType, $value]) }}"
                                >
                                    @csrf
                                    @method('PUT')

                                    <div class="form-row align-items-center">
                                        <div class="form-group col-md-3 mb-2">
                                            <input
                                                type="text"
                                                name="value_ar"
                                                class="form-control"
                                                value="{{ $value->value_ar }}"
                                                required
                                            >
                                        </div>

                                        <div class="form-group col-md-3 mb-2">
                                            <input
                                                type="text"
                                                name="value_en"
                                                class="form-control"
                                                value="{{ $value->value_en }}"
                                                required
                                            >
                                        </div>

                                        @if($variantType->input_type === 'color')
                                            <div class="form-group col-md-2 mb-2">
                                                <input
                                                    type="color"
                                                    name="color_code"
                                                    class="form-control"
                                                    value="{{ $value->color_code ?? '#000000' }}"
                                                    required
                                                >
                                            </div>
                                        @endif

                                        <div class="form-group col-md-2 mb-2">
                                            <select name="status" class="form-control" required>
                                                <option value="1" @selected($value->status)>
                                                    {{ __('dashboard.variant_types.active') }}
                                                </option>
                                                <option value="0" @selected(!$value->status)>
                                                    {{ __('dashboard.variant_types.inactive') }}
                                                </option>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-2 mb-2">
                                            <button type="submit" class="btn btn-sm btn-primary">
                                                {{ __('dashboard.variant_types.update') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>

                                <form
                                    method="POST"
                                    action="{{ route('admin.variant-types.values.destroy', [$variantType, $value]) }}"
                                    onsubmit="return confirm(@js(__('dashboard.variant_types.confirm_delete_value')))"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-sm btn-danger">
                                        {{ __('dashboard.variant_types.delete') }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="{{ $variantType->input_type === 'color' ? 6 : 5 }}"
                                class="text-center"
                            >
                                {{ __('dashboard.variant_types.no_values') }}
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
