@extends('web.layouts.app')
@section('title', $page->title)
@section('page', 'page')

@section('content')
<div class="page-hero"><div class="container"><h1 id="page-title">{{ $page->title }}</h1></div></div>

<section class="container pb-4" id="page-root" data-slug="{{ $slug }}">
    <div class="card-soft pad mx-auto" style="max-width:860px">
        <div class="rich-content" id="page-content"><div class="skeleton" style="height:200px"></div></div>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('assets/web/js/pages/contact.js') }}?v={{ config('website.version') }}"></script>
@endpush
