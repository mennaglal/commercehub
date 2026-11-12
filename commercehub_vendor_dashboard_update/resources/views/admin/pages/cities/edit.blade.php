
@extends('admin.layouts.app')

@section('title', __('dashboard.Edit-City'))

@section('content')
    @include('includes.error')
    @include('includes.success')

    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('dashboard.Edit-City') }}</h5>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.cities.update', $city) }}">
                        @csrf
                        @method('PUT')

                        @php($isEdit = true)

                        @include('admin.pages.cities._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
