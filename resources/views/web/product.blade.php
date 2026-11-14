@extends('web.nexus.page')
@section('title', 'Product')
@section('page', 'product')
@php($productId = $product->id)
@php($slug = $slug)
@section('content')
<div id="nx-app" data-page="product" data-product-id="{{ $productId }}" data-slug="{{ $slug }}"><div class="nx-empty"><div class="spinner-border"></div></div></div>
@endsection
