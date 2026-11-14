
@extends('admin.layouts.app')

@section('title', __('products.title'))

@section('style')
    <style>
        /* Product Statistics Cards */
        .product-stat-icon {
            width: 55px;
            height: 55px;
            min-width: 55px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 23px;
        }

        .bg-primary-light {
            background-color: rgba(0, 118, 246, 0.1);
        }

        .bg-success-light {
            background-color: rgba(40, 167, 69, 0.10);
        }

        .bg-warning-light {
            background-color: rgba(255, 193, 7, 0.15);
        }

        .bg-danger-light {
            background-color: rgba(220, 53, 69, 0.10);
        }

        .product-stat-icon i {
            line-height: 1;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">

            <h4 class="mb-0">{{ __('products.management') }}</h4>

            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                <i class="fe fe-plus"></i>
                {{ __('products.add') }}
            </a>

        </div>

        @include('includes.error')
        @include('includes.success')

        {{-- Product Statistics --}}
        <div class="row mb-3">

            {{-- Total Products --}}
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card shadow-sm h-100 border">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="mb-2">
                                {{ __('products.statistics.total') }}
                            </div>

                            <h3 class="font-weight-bold text-info-dark mb-1">
                                {{ number_format($statistics->total ?? 0) }}
                            </h3>

                            <small>
                                {{ __('products.statistics.total_description') }}
                            </small>
                        </div>

                        <div class="product-stat-icon bg-primary-light text-primary">
                            <i class="fe fe-package"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Active Products --}}
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card shadow-sm h-100 border">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="mb-2">
                                {{ __('products.statistics.active') }}
                            </div>

                            <h3 class="font-weight-bold text-success mb-1">
                                {{ number_format($statistics->active ?? 0) }}
                            </h3>

                            <small>
                                {{ __('products.statistics.active_description') }}
                            </small>
                        </div>

                        <div class="product-stat-icon bg-success-light text-success">
                            <i class="fe fe-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Low Stock --}}
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card shadow-sm h-100 border">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="mb-2">
                                {{ __('products.statistics.low_stock') }}
                            </div>

                            <h3 class="font-weight-bold text-warning mb-1">
                                {{ number_format($statistics->low_stock ?? 0) }}
                            </h3>

                            <small>
                                {{ __('products.statistics.low_stock_description', [
                                    'count' => \App\Models\Product::LOW_STOCK_THRESHOLD
                                ]) }}
                            </small>
                        </div>

                        <div class="product-stat-icon bg-warning-light text-warning">
                            <i class="fe fe-alert-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Out of Stock --}}
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card shadow-sm h-100 border">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="mb-2">
                                {{ __('products.statistics.out_of_stock') }}
                            </div>

                            <h3 class="font-weight-bold text-danger mb-1">
                                {{ number_format($statistics->out_of_stock ?? 0) }}
                            </h3>

                            <small>
                                {{ __('products.statistics.out_of_stock_description') }}
                            </small>
                        </div>

                        <div class="product-stat-icon bg-danger-light text-danger">
                            <i class="fe fe-x-circle"></i>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Filters --}}
        <form action="{{ route('admin.products.index') }}" method="GET" class="row mb-3">

            {{-- Search --}}
            <div class="col-md-4 mb-2">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="form-control"
                    placeholder="{{ __('products.search_placeholder') }}"
                >
            </div>

            {{-- Product Status --}}
            <div class="col-md-2 mb-2">
                <select name="status" class="form-control">
                    <option value="">
                        {{ __('products.statistics.all_statuses') }}
                    </option>

                    <option value="active" @selected(request('status') === 'active')>
                        {{ __('products.active') }}
                    </option>

                    <option value="inactive" @selected(request('status') === 'inactive')>
                        {{ __('products.inactive') }}
                    </option>
                </select>
            </div>

            {{-- Stock Status --}}
            <div class="col-md-2 mb-2">
                <select name="stock_status" class="form-control">
                    <option value="">
                        {{ __('products.statistics.all_stock') }}
                    </option>

                    <option value="in_stock" @selected(request('stock_status') === 'in_stock')>
                        {{ __('products.statistics.in_stock') }}
                    </option>

                    <option value="low_stock" @selected(request('stock_status') === 'low_stock')>
                        {{ __('products.statistics.low_stock') }}
                    </option>

                    <option value="out_of_stock" @selected(request('stock_status') === 'out_of_stock')>
                        {{ __('products.statistics.out_of_stock') }}
                    </option>
                </select>
            </div>

            {{-- Product Type --}}
            <div class="col-md-2 mb-2">
                <select name="type" class="form-control">
                    <option value="">
                        {{ __('products.statistics.all_types') }}
                    </option>

                    <option value="simple" @selected(request('type') === 'simple')>
                        {{ __('products.statistics.simple') }}
                    </option>

                    <option value="variable" @selected(request('type') === 'variable')>
                        {{ __('products.statistics.variable') }}
                    </option>
                </select>
            </div>

            {{-- Sale Status --}}
            <div class="col-md-2 mb-2">
                <select name="sale" class="form-control">
                    <option value="">
                        {{ __('products.statistics.all_prices') }}
                    </option>

                    <option value="yes" @selected(request('sale') === 'yes')>
                        {{ __('products.statistics.on_sale') }}
                    </option>

                    <option value="no" @selected(request('sale') === 'no')>
                        {{ __('products.statistics.regular_price') }}
                    </option>
                </select>
            </div>

            {{-- Filter Actions --}}
            <div class="col-md-3 mb-2">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="fe fe-filter"></i>
                    {{ __('products.statistics.filter') }}
                </button>
            </div>

            <div class="col-md-3 mb-2">
                <a href="{{ route('admin.products.index') }}"
                   class="btn btn-outline-secondary w-100">
                    {{ __('products.reset') }}
                </a>
            </div>

        </form>

        {{-- Bulk Delete Form --}}
        <form
            id="bulkDeleteProductsForm"
            method="POST"
            action="{{ route('admin.bulk.delete') }}"
        >
            @csrf

            <input type="hidden" name="model" value="products">
            <input type="hidden" name="ids" id="selectedProductIds">

            <button
                type="button"
                onclick="confirmBulkDeleteProducts()"
                class="btn btn-danger mb-3"
            >
                {{ __('dashboard.delete_all') }}
            </button>
        </form>

        {{-- Products Table --}}
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">

                    <thead>
                    <tr>
                        <th>
                            <input
                                type="checkbox"
                                id="selectAllProducts"
                                aria-label="Select all products"
                            >
                        </th>

                        <th>#</th>
                        <th>{{ __('products.columns.image') }}</th>
                        <th>{{ __('products.columns.sku') }}</th>
                        <th>{{ __('products.columns.product') }}</th>
                        <th>{{ __('products.columns.store') }}</th>
                        <th>{{ __('products.columns.main_category') }}</th>
                        <th>{{ __('products.columns.subcategory') }}</th>
                        <th>{{ __('products.columns.price') }}</th>
                        <th>{{ __('products.columns.stock') }}</th>
                        <th>{{ __('products.statistics.stock_status') }}</th>
                        <th>{{ __('products.statistics.sale_status') }}</th>
                        <th>{{ __('products.columns.variants') }}</th>
                        <th>{{ __('products.columns.status') }}</th>
                        <th>{{ __('products.columns.actions') }}</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse($products as $product)
                        <tr>
                            {{-- Checkbox --}}
                            <td>
                                <input
                                    type="checkbox"
                                    class="product-checkbox"
                                    value="{{ $product->id }}"
                                    aria-label="Select product {{ $product->name_en }}"
                                >
                            </td>

                            {{-- Index --}}
                            <td>
                                {{ $products->firstItem() + $loop->index }}
                            </td>

                            {{-- Image --}}
                            <td>
                                @if($product->image)
                                    <img
                                        src="{{ $product->image_url }}"
                                        alt="{{ $product->name_en }}"
                                        width="60"
                                        height="60"
                                        class="rounded border"
                                        style="object-fit: cover;"
                                    >
                                @else
                                    <div
                                        class="bg-light border rounded d-flex align-items-center justify-content-center"
                                        style="width:60px;height:60px;"
                                    >
                                        <i class="fe fe-image text-muted"></i>
                                    </div>
                                @endif
                            </td>

                            {{-- SKU --}}
                            <td>
                                {{ $product->sku ?: '—' }}
                            </td>

                            {{-- Product --}}
                            <td>
                                <strong>{{ $product->name }}</strong>
                            </td>

                            {{-- Store --}}
                            <td>
                                {{ $product->store?->name ?? '—' }}
                            </td>

                            {{-- Main Category --}}
                            <td>
                                {{ $product->category?->parent?->name_lang ?? '—' }}
                            </td>

                            {{-- Subcategory --}}
                            <td>
                                {{ $product->category?->name_lang ?? '—' }}
                            </td>

                            {{-- Price --}}
                            <td>
                                @if($product->is_sale && $product->sale_price !== null)
                                    <div>
                                        <del class="text-danger small">
                                            {{ number_format((float) $product->price, 2) }}
                                        </del>

                                        <strong class="d-block">
                                            {{ number_format((float) $product->sale_price, 2) }}
                                        </strong>
                                    </div>
                                @elseif($product->has_variants)
                                    <span class="text-muted small">
                                            {{ __('products.from') }}
                                        </span>

                                    {{ number_format((float) $product->price, 2) }}
                                @else
                                    {{ number_format((float) $product->price, 2) }}
                                @endif
                            </td>

                            {{-- Stock --}}
                            <td>
                                {{ $product->stock }}
                            </td>

                            {{-- Stock Status --}}
                            <td>
                                @if($product->stock <= 0)
                                    <span class="badge badge-danger">
                                            {{ __('products.statistics.out_of_stock') }}
                                        </span>
                                @elseif($product->stock <= \App\Models\Product::LOW_STOCK_THRESHOLD)
                                    <span class="badge badge-warning">
                                            {{ __('products.statistics.low_stock') }}
                                        </span>
                                @else
                                    <span class="badge badge-success">
                                            {{ __('products.statistics.in_stock') }}
                                        </span>
                                @endif
                            </td>

                            {{-- Sale Status --}}
                            <td>
                                @if($product->is_sale && $product->sale_price !== null)
                                    <span class="badge badge-danger">
                                            {{ __('products.statistics.on_sale') }}
                                        </span>
                                @else
                                    <span class="badge badge-secondary">
                                            {{ __('products.statistics.regular_price') }}
                                        </span>
                                @endif
                            </td>

                            {{-- Variants --}}
                            <td>
                                @if($product->has_variants)
                                    <span class="badge badge-info">
                                            {{ $product->variants_count }}
                                        </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td>
                                @if($product->status)
                                    <span class="badge badge-success">
                                            {{ __('products.active') }}
                                        </span>
                                @else
                                    <span class="badge badge-secondary">
                                            {{ __('products.inactive') }}
                                        </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="text-nowrap">
                                <a
                                    href="{{ route('admin.products.edit', $product->id) }}"
                                    class="btn btn-sm btn-outline-secondary"
                                    title="{{ __('products.edit_short') }}"
                                >
                                    <i class="fe fe-edit"></i>
                                </a>

                                <form
                                    action="{{ route('admin.products.destroy', $product->id) }}"
                                    method="POST"
                                    class="d-inline delete-product-form"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-outline-danger"
                                        title="{{ __('products.delete') }}"
                                    >
                                        <i class="fe fe-trash-2"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="15" class="text-center text-muted py-4">
                                <i class="fe fe-package d-block mb-2"
                                   style="font-size: 30px;"></i>

                                {{ __('products.no_products') }}
                            </td>
                        </tr>
                    @endforelse
                    </tbody>

                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center">
            {{ $products->appends(request()->query())->links('vendor.pagination.medium') }}
        </div>

    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // Select All Products
            const selectAll = document.getElementById('selectAllProducts');

            const checkboxes = Array.from(
                document.querySelectorAll('.product-checkbox')
            );

            if (selectAll) {
                selectAll.addEventListener('change', function () {

                    checkboxes.forEach(function (checkbox) {
                        checkbox.checked = selectAll.checked;
                    });

                    selectAll.indeterminate = false;
                });

                checkboxes.forEach(function (checkbox) {

                    checkbox.addEventListener('change', function () {

                        const checkedCount = checkboxes.filter(function (item) {
                            return item.checked;
                        }).length;

                        selectAll.checked =
                            checkboxes.length > 0 &&
                            checkedCount === checkboxes.length;

                        selectAll.indeterminate =
                            checkedCount > 0 &&
                            checkedCount < checkboxes.length;
                    });

                });
            }

            // Delete Confirmation
            document.querySelectorAll('.delete-product-form').forEach(function (form) {

                form.addEventListener('submit', function (event) {

                    if (!confirm(@json(__('products.confirm_delete')))) {
                        event.preventDefault();
                    }

                });

            });

        });

        // Bulk Delete Confirmation
        window.confirmBulkDeleteProducts = function () {

            const selected = Array.from(
                document.querySelectorAll('.product-checkbox:checked')
            ).map(function (checkbox) {
                return checkbox.value;
            });

            if (selected.length === 0) {

                Swal.fire(
                    @json(__('dashboard.Warning')),
                    @json(__('dashboard.No items selected')),
                    'warning'
                );

                return;
            }

            Swal.fire({
                title: @json(__('dashboard.Are you sure?')),
                text: @json(__('dashboard.Selected items will be deleted!')),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: @json(__('dashboard.Yes, delete them!')),
                cancelButtonText: @json(__('dashboard.Cancel'))
            }).then(function (result) {

                if (result.isConfirmed) {

                    document.getElementById('selectedProductIds').value =
                        selected.join(',');

                    document.getElementById('bulkDeleteProductsForm').submit();
                }

            });
        };
    </script>
@endsection



