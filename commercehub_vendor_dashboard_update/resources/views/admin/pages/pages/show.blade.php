
@extends('layouts.app')

@section('title', $page->display_title)

@section('content')

    <div class="container py-5">

        <div class="row justify-content-center">

            <div class="col-lg-10">

                <div class="card shadow-sm border-0">

                    <div class="card-body p-4 p-md-5">

                        <h1 class="mb-4"
                            dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

                            {{ $page->display_title }}

                        </h1>

                        <div class="page-content"
                             dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
                             style="line-height: 2;">

                            {!! $page->display_content !!}

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection
