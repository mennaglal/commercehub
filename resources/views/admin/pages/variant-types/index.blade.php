
@extends('admin.layouts.app')

@section('title', __('dashboard.variant_types.title'))

@section('content')
    @include('includes.error')
    @include('includes.success')

    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ __('dashboard.variant_types.title') }}</h5>

            <a href="{{ route('admin.variant-types.create') }}" class="btn btn-primary">
                {{ __('dashboard.variant_types.add') }}
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('dashboard.variant_types.name_ar') }}</th>
                        <th>{{ __('dashboard.variant_types.name_en') }}</th>
                        <th>{{ __('dashboard.variant_types.input_type') }}</th>
                        <th>{{ __('dashboard.variant_types.values_count') }}</th>
                        <th>{{ __('dashboard.variant_types.status') }}</th>
                        <th>{{ __('dashboard.variant_types.actions') }}</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse($variantTypes as $variantType)
                        <tr>
                            <td>{{ $variantTypes->firstItem() + $loop->index }}</td>
                            <td>{{ $variantType->name_ar }}</td>
                            <td>{{ $variantType->name_en }}</td>
                            <td>
                                {{ __("dashboard.variant_types.{$variantType->input_type}_type") }}
                            </td>
                            <td>{{ $variantType->values_count }}</td>
                            <td>
                                    <span class="badge {{ $variantType->status ? 'badge-success' : 'badge-secondary' }}">
                                        {{ $variantType->status
                                            ? __('dashboard.variant_types.active')
                                            : __('dashboard.variant_types.inactive') }}
                                    </span>
                            </td>
                            <td class="text-nowrap">
                                <a
                                    href="{{ route('admin.variant-types.edit', $variantType) }}"
                                    class="btn btn-sm btn-info"
                                >
                                    {{ __('dashboard.edit') }}
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('admin.variant-types.destroy', $variantType) }}"
                                    class="d-inline"
                                    onsubmit="return confirm(@js(__('dashboard.variant_types.confirm_delete')))"
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
                            <td colspan="7" class="text-center">
                                {{ __('dashboard.variant_types.no_results') }}
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{ $variantTypes->links() }}
        </div>
    </div>
@endsection
