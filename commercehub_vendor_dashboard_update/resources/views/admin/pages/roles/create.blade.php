@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h2 class="mb-1">{{ __('roles.add') }}</h2>
        <p class="text-muted mb-0">{{ __('roles.create_subtitle') }}</p>
    </div>
    <form action="{{ route('admin.roles.store') }}" method="POST">
        @csrf
        @include('admin.pages.roles._form')
    </form>
</div>
@endsection
