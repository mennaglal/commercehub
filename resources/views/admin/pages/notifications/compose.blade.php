@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">{{ __('notifications.send_title') }}</h4>
        <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary">{{ __('notifications.title') }}</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.notifications.send') }}"
          onsubmit="return confirm(@js(__('notifications.send_confirm')));">
        @csrf

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="audience" class="form-label">{{ __('notifications.fields.audience') }} <span class="text-danger">*</span></label>
                <select name="audience" id="audience" class="form-control @error('audience') is-invalid @enderror" required>
                    @foreach($audiences as $audience)
                        <option value="{{ $audience }}" @selected(old('audience', 'customers') === $audience)>
                            {{ __('notifications.audiences.' . $audience) }}
                        </option>
                    @endforeach
                </select>
                @error('audience') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Only for "One user" (JS below) --}}
            <div class="col-md-4 mb-3" id="user_box">
                <label for="user_email" class="form-label">{{ __('notifications.fields.user_email') }} <span class="text-danger">*</span></label>
                <input type="email" name="user_email" id="user_email"
                       class="form-control @error('user_email') is-invalid @enderror" value="{{ old('user_email') }}">
                @error('user_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="row">
            @foreach(['title_ar' => 'text', 'title_en' => 'text', 'body_ar' => 'textarea', 'body_en' => 'textarea'] as $field => $type)
                <div class="col-md-6 mb-3">
                    <label for="{{ $field }}" class="form-label">{{ __('notifications.fields.' . $field) }} <span class="text-danger">*</span></label>
                    @if($type === 'text')
                        <input type="text" name="{{ $field }}" id="{{ $field }}" maxlength="120"
                               class="form-control @error($field) is-invalid @enderror"
                               value="{{ old($field) }}" @if(str_ends_with($field, '_ar')) dir="rtl" @endif required>
                    @else
                        <textarea name="{{ $field }}" id="{{ $field }}" rows="3" maxlength="500"
                                  class="form-control @error($field) is-invalid @enderror"
                                  @if(str_ends_with($field, '_ar')) dir="rtl" @endif required>{{ old($field) }}</textarea>
                    @endif
                    @error($field) <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            @endforeach

            <div class="col-md-12 mb-3">
                <label for="url" class="form-label">{{ __('notifications.fields.url') }}</label>
                <input type="url" name="url" id="url" maxlength="500"
                       class="form-control @error('url') is-invalid @enderror" value="{{ old('url') }}">
                @error('url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <small class="text-muted">{{ __('notifications.fields.url_hint') }}</small>
            </div>
        </div>

        {{-- Push to the phones (Firebase) --}}
        <div class="mb-3">
            <input type="hidden" name="push" value="0">
            <div class="form-check">
                <input type="checkbox" name="push" id="push" value="1" class="form-check-input"
                       @checked(old('push', $pushEnabled)) @disabled(! $pushEnabled)>
                <label for="push" class="form-check-label">{{ __('notifications.fields.push') }}</label>
            </div>
            @unless($pushEnabled)
                <small class="text-muted">{{ __('notifications.push_disabled') }}</small>
            @endunless
        </div>

        <button type="submit" class="btn btn-success">{{ __('notifications.send') }}</button>
    </form>

    {{-- ===================== History ===================== --}}
    <h5 class="mt-5 mb-3">{{ __('notifications.history') }}</h5>
    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                <tr>
                    <th>{{ __('notifications.columns.title') }}</th>
                    <th>{{ __('notifications.columns.audience') }}</th>
                    <th>{{ __('notifications.columns.recipients') }}</th>
                    <th>{{ __('notifications.columns.push') }}</th>
                    <th>{{ __('notifications.columns.date') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($history as $row)
                    <tr>
                        <td>{{ app()->getLocale() === 'ar' ? $row->title_ar : $row->title_en }}</td>
                        <td>{{ __('notifications.audiences.' . $row->audience) }}</td>
                        <td>{{ $row->recipients_count }}</td>
                        <td>
                            <span class="badge bg-{{ $row->with_push ? 'success' : 'secondary' }}">
                                {{ $row->with_push ? __('notifications.yes') : __('notifications.no') }}
                            </span>
                        </td>
                        <td>{{ $row->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">{{ __('notifications.history_empty') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const audience = document.getElementById('audience');
        const userBox = document.getElementById('user_box');
        const userEmail = document.getElementById('user_email');

        // The email is needed only when the message goes to ONE user.
        function toggleUser() {
            const isUser = audience.value === 'user';

            userBox.style.display = isUser ? '' : 'none';
            userEmail.disabled = !isUser;
            userEmail.required = isUser;
        }

        audience.addEventListener('change', toggleUser);
        toggleUser();
    });
</script>
@endsection
