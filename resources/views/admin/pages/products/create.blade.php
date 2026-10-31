@extends('admin.layouts.app')

@section('title', __('products.add'))

@section('content')
    <div class="container-fluid py-4">
        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ __('products.add_new') }}</h5>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm">
                    {{ __('products.back') }}
                </a>
            </div>

            <div class="card-body">
                @include('includes.error')
                @include('includes.success')

                <form action="{{ route('admin.products.store') }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf

                    {{-- An included view already sees the variables of this view
                         (stores, categories, variantTypes...), so only $product is passed. --}}
                    @include('admin.pages.products._form', ['product' => null])

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">{{ __('products.save') }}</button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-light">{{ __('products.cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
