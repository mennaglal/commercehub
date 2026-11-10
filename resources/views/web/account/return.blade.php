@extends('web.layouts.app')
@section('title', __('web.returns.details'))
@section('page', 'account-return')

@section('content')
<div class="page-hero"><div class="container"><h1>{{ __('web.returns.details') }}</h1></div></div>

<section class="container pb-4">
    <div class="row g-4">
        <div class="col-lg-3">@include('web.partials.account-menu')</div>
        <div class="col-lg-9"><div id="return-root" data-return="{{ $returnNumber }}"><div class="skeleton" style="height:200px"></div></div></div>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('assets/web/js/pages/returns.js') }}?v={{ config('website.version') }}"></script>
@endpush
