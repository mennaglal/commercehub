@extends('web.layouts.app')
@section('title', __('web.stores.title'))
@section('page', 'stores')

@section('content')
<div class="page-hero"><div class="container d-flex flex-wrap justify-content-between align-items-center gap-3">
    <h1>{{ __('web.stores.title') }}</h1>
    <form class="d-flex gap-2" style="max-width:380px;width:100%" role="search">
        <input class="form-control" type="search" id="stores-search" maxlength="100" placeholder="{{ __('web.stores.search') }}">
        <button class="btn btn-brand" type="submit"><i class="bi bi-search"></i></button>
    </form>
</div></div>

<section class="container pb-4" data-scroll-top>
    <div class="row g-3" id="stores-grid"></div>
    <nav class="mt-4" id="stores-pagination" aria-label="pages"></nav>
</section>
@endsection

@push('scripts')
<script src="{{ asset('assets/web/js/pages/stores.js') }}?v={{ config('website.version') }}"></script>
@endpush
