{{-- Change 'layouts.admin' / 'content' to your real admin layout names. --}}
@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">{{ __('bloggers.add') }}</h4>
        <a href="{{ route('admin.bloggers.index') }}" class="btn btn-outline-secondary">{{ __('bloggers.back') }}</a>
    </div>

    <form method="POST" action="{{ route('admin.bloggers.store') }}">
        @csrf
        @include('admin.pages.bloggers._form')
        <button type="submit" class="btn btn-success">{{ __('bloggers.save') }}</button>
    </form>
</div>
@endsection
