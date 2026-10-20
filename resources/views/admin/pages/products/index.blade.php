
@extends('admin.layouts.app')

@section('title', 'Products')

@section('content')
    <div class="container-fluid py-4">

        @include('includes.error')
        @include('includes.success')

        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0">Products Management</h5>

                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                    <i class="fe fe-plus"></i>
                    Add Product
                </a>
            </div>

            <div class="card-body">

                <form action="{{ route('admin.products.index') }}" method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-8 mb-2">
                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                placeholder="Search by product name, SKU or slug..."
                                value="{{ request('search') }}"
                            >
                        </div>

                        <div class="col-md-4 mb-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fe fe-search"></i>
                                Search
                            </button>

                            @if(request()->filled('search'))
                                <a href="{{ route('admin.products.index') }}" class="btn btn-light">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Product</th>
                            <th>Store</th>
                            <th>Main Category</th>
                            <th>Subcategory</th>
                            <th>SKU</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Variants</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>

                        <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>
                                    {{ $products->firstItem() + $loop->index }}
                                </td>

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
                                    <div class="text-muted small">
                                        {{ $product->name_ar }}
                                    </div>
                                    <div class="text-muted small">
                                        {{ $product->slug }}
                                    </div>
                                </td>

                                <td>
                                    {{ $product->store?->store_name ?? '—' }}
                                </td>

                                <td>
                                    {{ $product->category?->parent?->name_en ?? '—' }}
                                    @if($product->category?->parent?->name_ar)
                                        <div class="text-muted small">
                                            {{ $product->category->parent->name_ar }}
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    {{ $product->category?->name_en ?? '—' }}
                                    @if($product->category?->name_ar)
                                        <div class="text-muted small">
                                            {{ $product->category->name_ar }}
                                        </div>
                                    @endif
                                </td>

                                <td>{{ $product->sku }}</td>

                                <td>
                                    @if($product->is_sale && $product->sale_price !== null)
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

                                <td>{{ $product->stock }}</td>

                                <td>
                                    <span class="badge badge-info">
                                        {{ $product->variants->count() }}
                                    </span>
                                </td>

                                <td>
                                    @if($product->status)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="d-flex gap-1">
                                        <a
                                            href="{{ route('admin.products.edit', $product->id) }}"
                                            class="btn btn-sm btn-info"
                                            title="Edit"
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

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-danger"
                                                title="Delete"
                                            >
                                                <i class="fe fe-trash-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center py-4">
                                    <i class="fe fe-package d-block mb-2" style="font-size: 30px;"></i>
                                    No products found.
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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.delete-product-form').forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    const confirmed = confirm('Are you sure you want to delete this product?');

                    if (!confirmed) {
                        event.preventDefault();
                    }
                });
            });
        });
    </script>
@endpush
