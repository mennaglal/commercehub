@extends('web.layouts.app')
@section('title', $title ?? 'CommerceHub')
@section('page', $page ?? 'home')
@section('content')
<div id="nx-app" data-page="{{ $page ?? 'home' }}" @isset($productId) data-product-id="{{ $productId }}" @endisset @isset($storeId) data-store-id="{{ $storeId }}" @endisset @isset($slug) data-slug="{{ $slug }}" @endisset @isset($orderNumber) data-order-number="{{ $orderNumber }}" @endisset @isset($returnNumber) data-return-number="{{ $returnNumber }}" @endisset>
    <div class="nx-empty"><div class="spinner-border" role="status"></div><p class="mt-3">{{ app()->getLocale()==='ar' ? 'جاري تحميل المحتوى...' : 'Loading...' }}</p></div>
</div>
@endsection
