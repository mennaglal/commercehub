
@extends('admin.layouts.app')

@section('title', 'Add Product')

@section('content')
    <div class="container-fluid py-4">
        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Add New Product</h5>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm">
                    Back to Products
                </a>
            </div>

            <div class="card-body">
                @include('includes.error')
                @include('includes.success')

                <form action="{{ route('admin.products.store') }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf

                    @include('admin.pages.products._form', [
                        'product' => null,
                        'stores' => $stores,
                        'parentCategories' => $parentCategories,
                        'categories' => $categories,
                        'variantTypes' => $variantTypes,
                    ])

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            Save Product
                        </button>

                        <a href="{{ route('admin.products.index') }}" class="btn btn-light">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
