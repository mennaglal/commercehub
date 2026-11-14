
@extends('admin.layouts.app')

@section('title', __('dashboard.Add-City'))

@section('content')
    @include('includes.error')
    @include('includes.success')

    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('dashboard.Add-City') }}</h5>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.cities.store') }}">
                        @csrf

                        @php($isEdit = false)

                        @include('admin.pages.cities._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
