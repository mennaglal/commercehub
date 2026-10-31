@extends('admin.layouts.app')

@section('title', __('products.edit'))

@section('content')
    <div class="container-fluid py-4">
        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ __('products.edit') }}</h5>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm">
                    {{ __('products.back') }}
                </a>
            </div>

            <div class="card-body">
                @include('includes.error')
                @include('includes.success')

                <form action="{{ route('admin.products.update', $product->id) }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- The included view already sees $product, $stores, $categories, $variantTypes... --}}
                    @include('admin.pages.products._form')

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">{{ __('products.update') }}</button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-light">{{ __('products.cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
