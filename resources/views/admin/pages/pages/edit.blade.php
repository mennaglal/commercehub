
@extends('admin.layouts.app')

@section('content')

    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h4 class="mb-1">{{ __('pages.edit_page') }}</h4>
                <small class="text-muted">{{ $page->slug }}</small>
            </div>

            <a href="{{ route('admin.pages.index') }}"
               class="btn btn-light">

                <i class="fe fe-arrow-left mr-1"></i>
                {{ __('pages.back') }}

            </a>

        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.pages.update', $page->id) }}"
              method="POST">

            @csrf
            @method('PUT')

            <div class="card shadow mb-4">

                <div class="card-header">
                    <h5 class="mb-0">{{ __('pages.basic_information') }}</h5>
                </div>

                <div class="card-body">

                    <div class="form-group">
                        <label>{{ __('pages.slug') }}</label>

                        <input type="text"
                               name="slug"
                               value="{{ old('slug', $page->slug) }}"
                               class="form-control @error('slug') is-invalid @enderror"
                               required>

                        @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">

                        <div class="col-md-6">

                            <div class="form-group">
                                <label>{{ __('pages.title_ar') }}</label>

                                <input type="text"
                                       name="title_ar"
                                       dir="rtl"
                                       value="{{ old('title_ar', $page->title_ar) }}"
                                       class="form-control @error('title_ar') is-invalid @enderror"
                                       required>

                                @error('title_ar')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="form-group">
                                <label>{{ __('pages.title_en') }}</label>

                                <input type="text"
                                       name="title_en"
                                       dir="ltr"
                                       value="{{ old('title_en', $page->title_en) }}"
                                       class="form-control @error('title_en') is-invalid @enderror"
                                       required>

                                @error('title_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                    </div>

                    <div class="custom-control custom-switch mt-3">

                        <input type="checkbox"
                               class="custom-control-input"
                               id="is_active"
                               name="is_active"
                               value="1"
                            @checked(old('is_active', $page->is_active))>

                        <label class="custom-control-label" for="is_active">
                            {{ __('pages.is_active') }}
                        </label>

                    </div>

                </div>

            </div>


            <div class="card shadow mb-4">

                <div class="card-header">
                    <h5 class="mb-0">
                        {{ __('pages.arabic_content') }}
                    </h5>
                </div>

                <div class="card-body">

                <textarea name="content_ar"
                          id="content_ar"
                          class="form-control summernote"
                          dir="rtl">{{ old('content_ar', $page->content_ar) }}</textarea>

                    @error('content_ar')
                    <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror

                </div>

            </div>


            <div class="card shadow mb-4">

                <div class="card-header">
                    <h5 class="mb-0">
                        {{ __('pages.english_content') }}
                    </h5>
                </div>

                <div class="card-body">

                <textarea name="content_en"
                          id="content_en"
                          class="form-control summernote"
                          dir="ltr">{{ old('content_en', $page->content_en) }}</textarea>

                    @error('content_en')
                    <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror

                </div>

            </div>


            <div class="d-flex justify-content-end mb-4">

                <a href="{{ route('admin.pages.index') }}"
                   class="btn btn-light mr-2">

                    {{ __('pages.cancel') }}

                </a>

                <button type="submit" class="btn btn-primary">

                    <i class="fe fe-save mr-1"></i>
                    {{ __('pages.save') }}

                </button>

            </div>

        </form>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs4.min.js"></script>

    <script>

        $(document).ready(function () {

            $('.summernote').summernote({

                height: 350,

                placeholder: '{{ __('pages.editor_placeholder') }}',

                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'picture', 'table', 'hr']],
                    ['view', ['fullscreen', 'codeview']]
                ]

            });

        });

    </script>
@endsection


@push('styles')

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs4.min.css">

@endpush





