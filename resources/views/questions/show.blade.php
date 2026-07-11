@extends('questions.layout')

@section('title', __('questions.show.page_title'))

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="q-card p-3 p-md-4 p-lg-5">
            <div class="q-show-header">
                <div class="q-show-meta-start d-flex flex-wrap align-items-center gap-2 mb-3">
                    <span class="q-pill">
                        <i class="bi bi-hash"></i>{{ __('questions.show.ref_label') }}: {{ $question->public_ref }}
                    </span>
                    <span class="text-muted small">
                        <i class="bi bi-person-circle me-1"></i>
                        {{ $question->display_name }}
                    </span>
                </div>
                <span class="text-muted small q-show-date">
                    <i class="bi bi-calendar3 me-1"></i>
                    {{ __('questions.show.published_on') }}:
                    {{ optional($question->published_at ?: $question->approved_at)->format('d M Y') }}
                </span>
            </div>

            <div class="q-divider"></div>

            <div class="fs-5 lh-lg" style="white-space: pre-line;">{!! nl2br(e($question->question_body)) !!}</div>

            <div class="mt-4">
                <a href="{{ route('questions.index') }}" class="btn btn-light q-btn-outline w-100 w-md-auto">
                    <i class="bi bi-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} me-1"></i>
                    {{ __('questions.show.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
