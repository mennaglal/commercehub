@extends('web.nexus.page')
@section('title', 'Store')
@section('page', 'store')
@php($storeId = $store->id)
@php($slug = $slug)
@section('content')
<div id="nx-app" data-page="store" data-store-id="{{ $storeId }}" data-slug="{{ $slug }}"><div class="nx-empty"><div class="spinner-border"></div></div></div>
@endsection
