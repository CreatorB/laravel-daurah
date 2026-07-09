@extends('layouts.app')

@section('title', __('questions.admin.detail.page_title'))

@section('content')
<div class="container-fluid px-3 px-md-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="mb-0 gradient-text">
            <i class="fas fa-question-circle me-2"></i>{{ __('questions.admin.detail.page_title') }}
        </h2>
        <a href="{{ route('admin.questions.index', ['status' => $question->status->value]) }}" class="btn btn-light">
            <i class="fas fa-arrow-left me-1"></i>{{ __('questions.admin.detail.back') }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex flex-wrap align-items-center gap-2">
                    <span class="badge bg-light text-dark">#{{ $question->public_ref }}</span>
                    <span class="badge {{ $question->status->badgeClass() }}">{{ $question->status->label() }}</span>
                    <span class="badge bg-secondary">{{ strtoupper($question->locale) }}</span>
                    @if($question->lesson_code)
                        <span class="badge bg-info text-dark">{{ $question->lesson_code }}</span>
                    @endif
                    <span class="ms-auto text-muted small">
                        <i class="fas fa-clock me-1"></i>{{ $question->created_at->format('d M Y H:i') }}
                    </span>
                </div>
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small mb-2">
                        {{ __('questions.admin.detail.question_body') }}
                    </h6>
                    <div class="fs-6 lh-lg" style="white-space: pre-line;">{!! nl2br(e($question->question_body)) !!}</div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0">{{ __('questions.admin.detail.meta') }}</h6>
                </div>
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ __('questions.admin.detail.ref') }}</span>
                        <strong>{{ $question->public_ref }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ __('questions.fields.name') }}</span>
                        <strong>
                            @if($question->is_anonymous || blank($question->name))
                                {{ __('questions.anonymous') }}
                            @else
                                {{ $question->name }}
                            @endif
                        </strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ __('questions.fields.is_anonymous') }}</span>
                        <strong>{{ $question->is_anonymous ? __('questions.admin.detail.anonymous_yes') : __('questions.admin.detail.anonymous_no') }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ __('questions.fields.locale') }}</span>
                        <strong>{{ strtoupper($question->locale) }}</strong>
                    </li>
                    @if($question->lesson_code)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ __('questions.admin.detail.lesson_code') }}</span>
                            <strong>{{ $question->lesson_code }}</strong>
                        </li>
                    @endif
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ __('questions.admin.table.created_at') }}</span>
                        <strong>{{ $question->created_at->format('d M Y H:i') }}</strong>
                    </li>
                    @if($question->approved_at)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ __('questions.status.approved') }}</span>
                            <strong>{{ $question->approved_at->format('d M Y H:i') }}</strong>
                        </li>
                        @if($question->approver)
                            <li class="list-group-item d-flex justify-content-between">
                                <span>{{ __('questions.status.approved') }} by</span>
                                <strong>{{ $question->approver->nama ?? $question->approver->name ?? ('#'.$question->approved_by) }}</strong>
                            </li>
                        @endif
                    @endif
                    @if($question->rejected_at)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ __('questions.status.rejected') }}</span>
                            <strong>{{ $question->rejected_at->format('d M Y H:i') }}</strong>
                        </li>
                    @endif
                </ul>
            </div>

            @if(! $question->isRejected())
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.questions.reject', $question) }}">
                        @csrf
                        @method('PATCH')
                        <label class="form-label small text-muted">{{ __('questions.admin.detail.admin_note') }}</label>
                        <textarea name="admin_note" rows="3" class="form-control mb-2"
                                  placeholder="{{ __('questions.admin.detail.admin_note_placeholder') }}">{{ old('admin_note', $question->admin_note) }}</textarea>
                        <button type="submit" class="btn btn-outline-danger w-100"
                                onclick="return confirm('{{ __('questions.status.rejected') }}?')">
                            <i class="fas fa-times me-1"></i>{{ __('questions.admin.detail.reject') }}
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>

        <div class="col-12">
            @if($question->isPending() || $question->isRejected())
                <form method="POST" action="{{ route('admin.questions.approve', $question) }}" class="card shadow-sm">
                    @csrf
                    @method('PATCH')
                    <div class="card-body d-flex flex-wrap gap-2 align-items-center">
                        <div class="flex-grow-1">
                            <label class="form-label small text-muted mb-1">{{ __('questions.admin.detail.admin_note') }}</label>
                            <input type="text" name="admin_note" class="form-control"
                                   value="{{ old('admin_note', $question->admin_note) }}"
                                   placeholder="{{ __('questions.admin.detail.admin_note_placeholder') }}">
                        </div>
                        <button type="submit" class="btn btn-success align-self-end">
                            <i class="fas fa-check me-1"></i>{{ __('questions.admin.detail.approve') }}
                        </button>
                    </div>
                </form>
            @else
                <div class="alert alert-success mb-0">
                    <i class="fas fa-check-circle me-1"></i>
                    {{ __('questions.admin.approved_success') }}
                    @if($question->published_at)
                        &middot; {{ $question->published_at->format('d M Y H:i') }}
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
