@php
    $isAr = app()->getLocale() === 'ar';
    $text = $isAr ? ($product->description_ar ?: $product->description_en) : ($product->description_en ?: $product->description_ar);
@endphp
@extends('web.layouts.app')
@section('title', $product->name)
@section('description', \Illuminate\Support\Str::limit(strip_tags((string) $text), 160, '...'))
@section('image', $product->image_url ?: asset('assets/web/img/logo.svg'))
@section('page', 'product')

@section('content')
<div class="container page-section">
    <div id="product-root" data-slug="{{ $slug }}">
        <div class="row g-4"><div class="col-lg-6"><div class="skeleton" style="aspect-ratio:1/1;border-radius:28px"></div></div>
            <div class="col-lg-6"><div class="skeleton mb-3" style="height:34px;width:70%"></div><div class="skeleton mb-3" style="height:22px;width:35%"></div><div class="skeleton" style="height:180px"></div></div></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/web/js/pages/product.js') }}?v={{ config('website.version') }}"></script>
@endpush
