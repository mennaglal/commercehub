
@extends('admin.layouts.app')

@section('title', __('dashboard.Add-Area'))

@section('content')
    @include('includes.error')
    @include('includes.success')

    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('dashboard.Add-Area') }}</h5>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.areas.store') }}">
                        @csrf

                        @php($isEdit = false)

                        @include('admin.pages.areas._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
