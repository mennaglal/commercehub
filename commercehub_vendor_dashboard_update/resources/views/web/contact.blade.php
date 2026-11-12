@php
    $isAr = app()->getLocale() === 'ar';
    $c = config('website.contact');
    $whatsapp = preg_replace('/\D+/', '', $c['whatsapp']);
@endphp
@extends('web.layouts.app')
@section('title', __('web.contact.title'))
@section('page', 'contact')

@section('content')
<div class="page-hero"><div class="container"><h1>{{ __('web.contact.title') }}</h1><p class="text-muted mb-0">{{ __('web.contact.subtitle') }}</p></div></div>

<section class="container pb-4">
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="row g-3">
                @foreach([
                    ['bi-geo-alt', __('web.contact.address'), $c['address'][$isAr ? 'ar' : 'en'], null],
                    ['bi-telephone', __('web.contact.phone'), $c['phone'], 'tel:' . preg_replace('/\s+/', '', $c['phone'])],
                    ['bi-envelope', __('web.contact.email'), $c['email'], 'mailto:' . $c['email']],
                    ['bi-clock', __('web.contact.hours'), $c['hours'][$isAr ? 'ar' : 'en'], null],
                ] as [$icon, $label, $value, $href])
                    <div class="col-12"><div class="feature d-flex gap-3 align-items-center"><div class="ico mb-0"><i class="bi {{ $icon }}"></i></div>
                        <div><div class="text-muted small">{{ $label }}</div>@if($href)<a class="fw-bold" href="{{ $href }}" dir="ltr">{{ $value }}</a>@else<div class="fw-bold">{{ $value }}</div>@endif</div></div></div>
                @endforeach
                @if($whatsapp)
                    <div class="col-12"><a class="btn btn-accent w-100" target="_blank" rel="noopener noreferrer" href="https://wa.me/{{ $whatsapp }}"><i class="bi bi-whatsapp me-1"></i>{{ __('web.contact.whatsapp') }}</a></div>
                @endif
            </div>
        </div>

        <div class="col-lg-7">
            <form class="card-soft pad" id="contact-form" novalidate>
                <h5 class="mb-3">{{ __('web.contact.form_title') }}</h5>
                <div class="alert alert-danger form-errors d-none" role="alert"></div>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="c-name">{{ __('web.contact.name') }} *</label><input class="form-control" id="c-name" name="name" maxlength="100" required></div>
                    <div class="col-md-6"><label class="form-label" for="c-email">{{ __('web.contact.email') }} *</label><input class="form-control" type="email" id="c-email" name="email" maxlength="150" required></div>
                    <div class="col-md-6"><label class="form-label" for="c-phone">{{ __('web.contact.phone') }}</label><input class="form-control" id="c-phone" name="phone" maxlength="30" inputmode="tel"></div>
                    <div class="col-md-6"><label class="form-label" for="c-subject">{{ __('web.contact.subject') }} *</label><input class="form-control" id="c-subject" name="subject" maxlength="150" required></div>
                    <div class="col-12"><label class="form-label" for="c-message">{{ __('web.contact.message') }} *</label><textarea class="form-control" id="c-message" name="message" rows="5" minlength="10" maxlength="3000" required></textarea></div>
                    {{-- A hidden field for bots: a real person never sees it --}}
                    <div style="position:absolute;inset-inline-start:-9999px" aria-hidden="true"><input type="text" name="website" tabindex="-1" autocomplete="off"></div>
                </div>
                <button class="btn btn-brand btn-lg mt-3" type="submit">{{ __('web.contact.send') }}</button>
            </form>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('assets/web/js/pages/contact.js') }}?v={{ config('website.version') }}"></script>
@endpush
