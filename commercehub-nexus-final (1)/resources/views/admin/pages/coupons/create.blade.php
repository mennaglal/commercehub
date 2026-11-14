{{-- Change 'layouts.admin' / 'content' to your real admin layout names. --}}
@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">{{ __('coupons.add') }}</h4>
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary">{{ __('coupons.back') }}</a>
    </div>

    <form method="POST" action="{{ route('admin.coupons.store') }}">
        @csrf
        @include('admin.pages.coupons._form')
        <button type="submit" class="btn btn-success">{{ __('coupons.save') }}</button>
    </form>
</div>
@endsection
