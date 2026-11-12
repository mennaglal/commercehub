
@extends('admin.layouts.app')

@section('content')

    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <h4>{{ __('contact-messages.message_details') }}</h4>

            <a href="{{ route('admin.contact-messages.index') }}"
               class="btn btn-light">

                {{ __('contact-messages.back') }}

            </a>

        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="card shadow mb-4">

            <div class="card-header">
                <h5 class="mb-0">{{ $contactMessage->subject }}</h5>
            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <strong>{{ __('contact-messages.name') }}</strong>
                        <p>{{ $contactMessage->name }}</p>
                    </div>

                    <div class="col-md-6 mb-3">
                        <strong>{{ __('contact-messages.email') }}</strong>
                        <p>
                            <a href="mailto:{{ $contactMessage->email }}">
                                {{ $contactMessage->email }}
                            </a>
                        </p>
                    </div>

                    <div class="col-md-6 mb-3">
                        <strong>{{ __('contact-messages.phone') }}</strong>
                        <p>
                            @if($contactMessage->phone)
                                <a href="tel:{{ $contactMessage->phone }}">
                                    {{ $contactMessage->phone }}
                                </a>
                            @else
                                -
                            @endif
                        </p>
                    </div>

                    <div class="col-md-6 mb-3">
                        <strong>{{ __('contact-messages.date') }}</strong>
                        <p>{{ $contactMessage->created_at->format('Y-m-d H:i') }}</p>
                    </div>

                </div>

                <hr>

                <strong>{{ __('contact-messages.message') }}</strong>

                <div class="border rounded p-3 mt-2"
                     style="white-space: pre-wrap;">{{ $contactMessage->message }}</div>

            </div>

        </div>


        <div class="card shadow">

            <div class="card-header">
                <h5 class="mb-0">{{ __('contact-messages.manage_status') }}</h5>
            </div>

            <div class="card-body">

                <form action="{{ route('admin.contact-messages.update', $contactMessage->id) }}"
                      method="POST">

                    @csrf
                    @method('PUT')

                    <div class="form-group">

                        <label>{{ __('contact-messages.status') }}</label>

                        <select name="status" class="form-control" required>

                            @foreach(\App\Models\ContactMessage::statuses() as $status)

                                <option value="{{ $status }}"
                                    @selected($contactMessage->status === $status)>

                                    {{ __('contact-messages.statuses.'.$status) }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                    <button type="submit" class="btn btn-primary">

                        <i class="fe fe-save mr-1"></i>

                        {{ __('contact-messages.save_status') }}

                    </button>

                </form>

            </div>

        </div>

    </div>

@endsection
