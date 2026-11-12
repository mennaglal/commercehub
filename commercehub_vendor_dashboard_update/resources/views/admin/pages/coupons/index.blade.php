
@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <h4 class="mb-0">{{ __('coupons.title') }}</h4>

            <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
                {{ __('coupons.add') }}
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @include('includes.error')

        <div class="card shadow">
            <div class="card-body">

                {{-- Search --}}
                <form
                    action="{{ route('admin.coupons.index') }}"
                    method="GET"
                    class="d-flex align-items-center flex-wrap mb-3"
                    style="gap: 10px;"
                >
                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        style="max-width: 300px;"
                        placeholder="{{ __('dashboard.Search...') }}"
                        value="{{ request('search') }}"
                    >

                    <button type="submit" class="btn btn-primary">
                        <i class="fe fe-search"></i>
                        {{ __('dashboard.Search') }}
                    </button>

                    @if(request()->filled('search'))
                        <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">
                            {{ __('dashboard.Reset') }}
                        </a>
                    @endif
                </form>

                {{-- Bulk Delete Form --}}
                <form
                    id="bulkDeleteCouponsForm"
                    method="POST"
                    action="{{ route('admin.bulk.delete') }}"
                    data-bulk-delete
                    data-selection-group="coupons"
                >
                    @csrf

                    <input type="hidden" name="model" value="coupons">
                    <input type="hidden" name="ids">

                    <button type="submit" class="btn btn-danger mb-3">
                        @lang('dashboard.delete_all')
                    </button>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                        <tr>
                            <th>
                                <input
                                    type="checkbox"
                                    data-select-all="coupons"
                                    aria-label="Select all coupons"
                                >
                            </th>
                            <th>ID</th>
                            <th>{{ __('coupons.columns.code') }}</th>
                            <th>{{ __('coupons.columns.discount') }}</th>
                            <th>{{ __('coupons.columns.categories') }}</th>
                            <th>{{ __('coupons.columns.owner') }}</th>
                            <th>{{ __('coupons.columns.commission') }}</th>
                            <th>{{ __('coupons.columns.usage') }}</th>
                            <th>{{ __('coupons.columns.validity') }}</th>
                            <th>{{ __('coupons.columns.status') }}</th>
                            <th></th>
                        </tr>
                        </thead>

                        <tbody>
                        @forelse($coupons as $coupon)
                            <tr>
                                <td>
                                    <input
                                        type="checkbox"
                                        data-select-item="coupons"
                                        value="{{ $coupon->id }}"
                                        aria-label="Select coupon {{ $coupon->code }}"
                                    >
                                </td>

                                <td>{{ $coupon->id }}</td>

                                <td><strong>{{ $coupon->code }}</strong></td>

                                <td>
                                    {{ $coupon->type === 'percentage'
                                        ? rtrim(rtrim($coupon->value, '0'), '.') . '%'
                                        : number_format($coupon->value, 2) }}
                                </td>

                                <td>{{ $coupon->categories_count }}</td>

                                <td>{{ $coupon->blogger?->name ?? __('coupons.general') }}</td>

                                <td>
                                    @if($coupon->blogger_id)
                                        {{ $coupon->commission_type === 'percentage'
                                            ? rtrim(rtrim($coupon->commission_value, '0'), '.') . '%'
                                            : number_format($coupon->commission_value, 2) }}
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    {{ $coupon->used_count }}
                                    /
                                    {{ $coupon->usage_limit ?? __('coupons.unlimited') }}
                                </td>

                                <td>
                                    <small>
                                        {{ $coupon->starts_at?->format('Y-m-d') ?? '...' }}
                                        &rarr;
                                        {{ $coupon->expires_at?->format('Y-m-d') ?? '...' }}
                                    </small>
                                </td>

                                <td>
                                <span class="badge bg-{{ $coupon->status ? 'success' : 'secondary' }}">
                                    {{ $coupon->status ? __('coupons.active') : __('coupons.inactive') }}
                                </span>
                                </td>


                                <td class="text-nowrap">
                                    <a href="{{ route('admin.coupons.edit', $coupon) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        {{ __('coupons.edit') }}
                                    </a>

                                    <form action="{{ route('admin.coupons.destroy', $coupon) }}"
                                          method="POST"
                                          class="d-inline"
                                          data-confirm-delete>
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            {{ __('dashboard.delete') }}
                                        </button>
                                    </form>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">
                                    {{ __('coupons.empty') }}
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        {{-- Pagination --}}
        <div class="d-flex justify-content-center">
            {{ $coupons->links('vendor.pagination.medium') }}
        </div>
    </div>
@endsection
