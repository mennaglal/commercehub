@extends('web.layouts.app')
@section('title', __('web.nav.notifications'))
@section('page', 'account-notifications')

@section('content')
<div class="page-hero"><div class="container d-flex justify-content-between align-items-center flex-wrap gap-2"><h1>{{ __('web.nav.notifications') }}</h1>
    <button type="button" class="btn btn-soft" id="notifications-read-all"><i class="bi bi-check2-all me-1"></i>{{ __('web.notifications.read_all') }}</button></div></div>

<section class="container pb-4">
    <div class="row g-4">
        <div class="col-lg-3">@include('web.partials.account-menu')</div>
        <div class="col-lg-9"><div id="notifications-list"></div><nav class="mt-3" id="notifications-pagination" aria-label="pages"></nav></div>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('assets/web/js/pages/notifications.js') }}?v={{ config('website.version') }}"></script>
@endpush
