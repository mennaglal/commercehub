@extends('web.nexus.page')
@section('title', 'Product editor')
@section('page', 'vendor-product-form')
@section('content')
<div id="nx-app" data-page="vendor-product-form" data-product-id="{{ $productId }}"><div class="nx-empty"><div class="spinner-border"></div></div></div>
@endsection
