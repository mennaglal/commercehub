@extends('web.layouts.app')
@section('title', __('web.nav.returns'))
@section('page', 'account-returns')

@section('content')
<div class="page-hero"><div class="container"><h1>{{ __('web.nav.returns') }}</h1></div></div>

<section class="container pb-4">
    <div class="row g-4">
        <div class="col-lg-3">@include('web.partials.account-menu')</div>
        <div class="col-lg-9"><div id="returns-list"></div><nav class="mt-3" id="returns-pagination" aria-label="pages"></nav></div>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('assets/web/js/pages/returns.js') }}?v={{ config('website.version') }}"></script>
@endpush
