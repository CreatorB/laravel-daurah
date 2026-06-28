@extends('questions.layout')

@section('title', __('questions.list.title'))

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="q-hero mb-4 mb-md-5 text-center">
            <h1 class="h2 fw-bold mb-2">{{ __('questions.list.page_title') }}</h1>
            <p class="lead mb-0 opacity-90">{{ __('questions.list.subtitle') }}</p>
        </div>

        @if($questions->count() === 0)
            <div class="q-empty">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                {{ __('questions.list.empty') }}
            </div>
        @else
            <div class="d-flex flex-column gap-3">
                @foreach($questions as $q)
                    <article class="q-card p-4">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            <span class="q-pill">
                                <i class="bi bi-hash"></i>{{ $q->public_ref }}
                            </span>
                            <span class="badge bg-light text-dark border">
                                {{ strtoupper($q->locale) }}
                            </span>
                            <span class="text-muted small">
                                <i class="bi bi-person-circle me-1"></i>
                                {{ $q->display_name }}
                            </span>
                            <span class="text-muted small ms-auto">
                                <i class="bi bi-calendar3 me-1"></i>
                                {{ optional($q->published_at ?: $q->approved_at)->format('d M Y') }}
                            </span>
                        </div>

                        <p class="mb-3" style="white-space: pre-line;">{!! nl2br(e($q->excerpt)) !!}</p>

                        <a href="{{ route('questions.show', $q->public_ref) }}" class="btn btn-sm q-btn-outline btn-outline-secondary">
                            {{ __('questions.list.show_more') }}
                            <i class="bi bi-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} ms-1"></i>
                        </a>
                    </article>
                @endforeach
            </div>

            <div class="mt-4 d-flex justify-content-center q-pagination">
                {{ $questions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
