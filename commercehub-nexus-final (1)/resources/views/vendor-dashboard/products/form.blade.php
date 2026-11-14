@extends('vendor-dashboard.layouts.app')
@section('title', $product ? __('Edit Product') : __('Add Product'))
@section('content')
<div class="card shadow-sm"><div class="card-header bg-white d-flex justify-content-between align-items-center"><h5 class="mb-0">{{ $product ? __('Edit Product') : __('Add Product') }}</h5><a href="{{ route('vendor.products.index') }}" class="btn btn-light btn-sm">{{ __('Back') }}</a></div><div class="card-body"><form action="{{ $formAction }}" method="POST" enctype="multipart/form-data">@csrf @if($formMethod === 'PUT') @method('PUT') @endif
@include('vendor-dashboard.products._form', ['product' => $product])
<div class="mt-4"><button class="btn btn-primary">{{ __('Save') }}</button><a href="{{ route('vendor.products.index') }}" class="btn btn-light">{{ __('Cancel') }}</a></div></form></div></div>
@endsection
