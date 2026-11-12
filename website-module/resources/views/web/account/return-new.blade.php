@extends('web.layouts.app')
@section('title', __('web.returns.request_title'))
@section('page', 'account-return-new')

@section('content')
<div class="page-hero"><div class="container"><h1>{{ __('web.returns.request_title') }}</h1><p class="text-muted mb-0">{{ __('web.orders.order') }} {{ $orderNumber }}</p></div></div>

<section class="container pb-4">
    <div class="row g-4">
        <div class="col-lg-3">@include('web.partials.account-menu')</div>

        <div class="col-lg-9" id="return-new-root" data-order="{{ $orderNumber }}">
            <div class="alert alert-warning d-none" id="return-error" role="alert"></div>

            <form id="return-form" novalidate>
                <div class="card-soft table-card mb-3">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead><tr><th>{{ __('web.orders.items') }}</th><th>{{ __('web.returns.ordered') }}</th><th>{{ __('web.returns.can_return') }}</th><th>{{ __('web.returns.refund_piece') }}</th><th>{{ __('web.returns.qty') }}</th></tr></thead>
                            <tbody id="return-items"><tr><td colspan="5"><div class="skeleton" style="height:50px"></div></td></tr></tbody>
                        </table>
                    </div>
                </div>

                <div class="card-soft pad">
                    <div class="row g-3">
                        <div class="col-md-5"><label class="form-label" for="return-reason">{{ __('web.returns.reason') }} *</label><select class="form-select" id="return-reason" name="reason"></select></div>
                        <div class="col-md-7"><label class="form-label" for="customer_note">{{ __('web.returns.note') }}</label><textarea class="form-control" id="customer_note" name="customer_note" rows="2" maxlength="1000"></textarea></div>
                    </div>
                    <p class="small text-muted mt-3 mb-0"><i class="bi bi-info-circle"></i> {{ __('web.returns.refund_note') }}</p>
                    <button class="btn btn-brand mt-3" type="submit">{{ __('web.returns.send') }}</button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('assets/web/js/pages/returns.js') }}?v={{ config('website.version') }}"></script>
@endpush
