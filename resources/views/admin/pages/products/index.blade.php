@extends('admin.layouts.app')

@section('title', __('products.title'))

@section('content')
    <div class="container-fluid py-4">

        @include('includes.error')
        @include('includes.success')

        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0">{{ __('products.management') }}</h5>

                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                    <i class="fe fe-plus"></i>
                    {{ __('products.add') }}
                </a>
            </div>

            <div class="card-body">

                {{-- Search --}}
                <form action="{{ route('admin.products.index') }}" method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-8 mb-2">
                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                placeholder="{{ __('products.search_placeholder') }}"
                                value="{{ request('search') }}"
                            >
                        </div>

                        <div class="col-md-4 mb-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fe fe-search"></i>
                                {{ __('products.search') }}
                            </button>

                            @if(request()->filled('search'))
                                <a href="{{ route('admin.products.index') }}" class="btn btn-light">{{ __('products.reset') }}</a>
                            @endif
                        </div>
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
                </form>

                <button
                    type="button"
                    onclick="confirmBulkDeleteProducts()"
                    class="btn btn-danger mb-3"
                >
                    @lang('dashboard.delete_all')
                </button>

                {{-- Products table --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="thead-light">
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
                            <th>{{ __('products.columns.product') }}</th>
                            <th>{{ __('products.columns.store') }}</th>
                            <th>{{ __('products.columns.main_category') }}</th>
                            <th>{{ __('products.columns.subcategory') }}</th>
                            <th>{{ __('products.columns.sku') }}</th>
                            <th>{{ __('products.columns.price') }}</th>
                            <th>{{ __('products.columns.stock') }}</th>
                            <th>{{ __('products.columns.variants') }}</th>
                            <th>{{ __('products.columns.status') }}</th>
                            <th>{{ __('products.columns.actions') }}</th>
                        </tr>
                        </thead>

                        <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>
                                    <input
                                        type="checkbox"
                                        class="product-checkbox"
                                        value="{{ $product->id }}"
                                        aria-label="Select product {{ $product->name_en }}"
                                    >
                                </td>
                                <td>{{ $products->firstItem() + $loop->index }}</td>

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

                                <td>
                                    <strong>{{ $product->name_en }}</strong>
                                    <div class="text-muted small">{{ $product->name_ar }}</div>
                                    <div class="text-muted small">{{ $product->slug }}</div>
                                </td>

                                <td>{{ $product->store?->store_name ?? '—' }}</td>

                                <td>
                                    {{ $product->category?->parent?->name_en ?? '—' }}
                                    @if($product->category?->parent?->name_ar)
                                        <div class="text-muted small">{{ $product->category->parent->name_ar }}</div>
                                    @endif
                                </td>

                                <td>
                                    {{ $product->category?->name_en ?? '—' }}
                                    @if($product->category?->name_ar)
                                        <div class="text-muted small">{{ $product->category->name_ar }}</div>
                                    @endif
                                </td>

                                <td>{{ $product->sku }}</td>

                                {{-- Price: "From X" for products with variants (X = lowest variant price) --}}
                                <td>
                                    @if($product->has_variants)
                                        <span class="text-muted small">{{ __('products.from') }}</span>
                                        {{ number_format((float) $product->price, 2) }}
                                    @elseif($product->is_sale && $product->sale_price !== null)
                                        <strong class="text-danger">
                                            {{ number_format((float) $product->sale_price, 2) }}
                                        </strong>
                                        <div class="text-muted small">
                                            <del>{{ number_format((float) $product->price, 2) }}</del>
                                        </div>
                                    @else
                                        {{ number_format((float) $product->price, 2) }}
                                    @endif
                                </td>

                                {{-- For products with variants this is the total of all variants --}}
                                <td>{{ $product->stock }}</td>

                                <td>
                                    @if($product->has_variants)
                                        <span class="badge badge-info">{{ $product->variants_count }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>

                                <td>
                                    @if($product->status)
                                        <span class="badge badge-success">{{ __('products.active') }}</span>
                                    @else
                                        <span class="badge badge-secondary">{{ __('products.inactive') }}</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="d-flex gap-1">
                                        <a style="height: fit-content"
                                            href="{{ route('admin.products.edit', $product->id) }}"
                                            class="btn btn-sm btn-info"
                                            title="{{ __('products.edit_short') }}"
                                        >
                                            <i class="fe fe-edit"></i>
                                        </a>

                                        <form
                                            action="{{ route('admin.products.destroy', $product->id) }}"
                                            method="POST"
                                            class="delete-product-form"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-sm btn-danger" title="{{ __('products.delete') }}">
                                                <i class="fe fe-trash-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="text-center py-4">
                                    <i class="fe fe-package d-block mb-2" style="font-size: 30px;"></i>
                                    {{ __('products.no_products') }}
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $products->links() }}
                </div>

            </div>
        </div>
    </div>
@endsection

<script>
    // Handle select all products on the current page.
    document.addEventListener('DOMContentLoaded', function () {
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

        // Ask for confirmation before deleting a product.
        document.querySelectorAll('.delete-product-form').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!confirm(@json(__('products.confirm_delete')))) {
                    event.preventDefault();
                }
            });
        });
    });

    // Confirm and submit bulk product deletion.
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
                document.getElementById('selectedProductIds').value = selected.join(',');
                document.getElementById('bulkDeleteProductsForm').submit();
            }
        });
    };
</script>

