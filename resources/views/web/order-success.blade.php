@extends('web.layouts.app')
@section('title', __('web.checkout.success_title'))
@section('page', 'order-success')

@section('content')
<section class="container page-section" id="success-root" data-order="{{ $orderNumber }}">
    <div class="card-soft pad text-center mx-auto" style="max-width:620px">
        <div class="mb-3" style="font-size:4rem;color:var(--success)"><i class="bi bi-check-circle-fill"></i></div>
        <h1 class="h2">{{ __('web.checkout.success_title') }}</h1>
        <p class="text-muted">{{ __('web.checkout.success_text') }}</p>
        <div class="card-soft pad my-3 text-start" style="background:var(--brand-soft);border:0">
            <div class="summary-row"><span>{{ __('web.checkout.order_number') }}</span><strong id="success-number">{{ $orderNumber }}</strong></div>
            <div class="summary-row"><span>{{ __('web.cart.total') }}</span><strong id="success-total">-</strong></div>
            <div class="summary-row"><span>{{ __('web.orders.payment') }}</span><strong id="success-payment">-</strong></div>
        </div>
        <div class="d-flex flex-wrap gap-2 justify-content-center">
            <a class="btn btn-accent d-none" id="success-pay" target="_blank" rel="noopener noreferrer" href="#">{{ __('web.checkout.pay_now') }}</a>
            <a class="btn btn-brand" id="success-view" href="{{ route('site.account.orders') }}">{{ __('web.checkout.view_order') }}</a>
            <a class="btn btn-soft" href="{{ route('site.shop') }}">{{ __('web.cart.continue') }}</a>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('assets/web/js/pages/checkout.js') }}?v={{ config('website.version') }}"></script>
@endpush
