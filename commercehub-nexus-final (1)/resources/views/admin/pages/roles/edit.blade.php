@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h2 class="mb-1">{{ __('roles.edit_role') }}: {{ $role->name }}</h2>
        <p class="text-muted mb-0">{{ __('roles.edit_subtitle') }}</p>
    </div>

    @if($role->name === 'super-admin')
        <div class="alert alert-warning">{{ __('roles.super_admin_edit_warning') }}</div>
    @else
        <form action="{{ route('admin.roles.update', $role) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.pages.roles._form')
        </form>
    @endif
</div>
@endsection
