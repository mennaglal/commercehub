
@extends('admin.layouts.app')

@section('content')

    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h4>{{ __('contact-messages.title') }}</h4>

                <small class="text-muted">
                    {{ __('contact-messages.new_messages') }}:
                    <strong>{{ $newMessagesCount }}</strong>
                </small>
            </div>

        </div>

        <div class="card shadow mb-4">

            <div class="card-body">

                <form method="GET"
                      action="{{ route('admin.contact-messages.index') }}">

                    <div class="row">

                        <div class="col-md-6 mb-2">

                            <input type="text"
                                   name="search"
                                   value="{{ request('search') }}"
                                   class="form-control"
                                   placeholder="{{ __('contact-messages.search_placeholder') }}">

                        </div>

                        <div class="col-md-4 mb-2">

                            <select name="status" class="form-control">

                                <option value="">
                                    {{ __('contact-messages.all_statuses') }}
                                </option>

                                @foreach(\App\Models\ContactMessage::statuses() as $status)

                                    <option value="{{ $status }}"
                                        @selected(request('status') === $status)>

                                        {{ __('contact-messages.statuses.'.$status) }}

                                    </option>

                                @endforeach

                            </select>

                        </div>

                        <div class="col-md-2 mb-2">

                            <button type="submit" class="btn btn-primary btn-block">
                                {{ __('contact-messages.search') }}
                            </button>

                        </div>

                    </div>

                </form>

            </div>

        </div>


        <div class="card shadow">

            <div class="card-header">
                <h5 class="mb-0">{{ __('contact-messages.all_messages') }}</h5>
            </div>

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover">

                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('contact-messages.name') }}</th>
                            <th>{{ __('contact-messages.email') }}</th>
                            <th>{{ __('contact-messages.subject') }}</th>
                            <th>{{ __('contact-messages.status') }}</th>
                            <th>{{ __('contact-messages.date') }}</th>
                            <th>{{ __('contact-messages.actions') }}</th>
                        </tr>
                        </thead>

                        <tbody>

                        @forelse($messages as $message)

                            <tr>

                                <td>{{ $message->id }}</td>

                                <td>{{ $message->name }}</td>

                                <td>{{ $message->email }}</td>

                                <td>{{ $message->subject }}</td>

                                <td>

                                    @php
                                        $colors = [
                                            'new' => 'warning',
                                            'read' => 'info',
                                            'replied' => 'success',
                                            'closed' => 'secondary',
                                        ];
                                    @endphp

                                    <span class="badge badge-{{ $colors[$message->status] ?? 'secondary' }}">

                                        {{ __('contact-messages.statuses.'.$message->status) }}

                                    </span>

                                </td>

                                <td>
                                    {{ $message->created_at->format('Y-m-d H:i') }}
                                </td>

                                <td>

                                    <a href="{{ route('admin.contact-messages.show', $message->id) }}"
                                       class="btn btn-sm btn-primary">

                                        <i class="fe fe-eye"></i>
                                        {{ __('contact-messages.view') }}

                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="7" class="text-center">
                                    {{ __('contact-messages.no_messages') }}
                                </td>
                            </tr>

                        @endforelse

                        </tbody>

                    </table>

                </div>

                <div class="mt-3">
                    {{ $messages->links() }}
                </div>

            </div>

        </div>

    </div>

@endsection
