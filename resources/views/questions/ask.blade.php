@extends('questions.layout')

@section('title', __('questions.ask.title'))

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="q-hero mb-4 mb-md-5 text-center">
            <h1 class="h2 fw-bold mb-2">{{ __('questions.home.have_question') }}</h1>
            <p class="lead mb-0 opacity-90">{{ __('questions.home.intro') }}</p>
        </div>

        <div class="q-card p-4 p-md-5">
            <h2 class="h4 fw-bold mb-1">{{ __('questions.ask.title') }}</h2>
            <p class="text-muted mb-4">{{ __('questions.ask.intro') }}</p>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('questions.store') }}" id="qForm" novalidate>
                @csrf
                <input type="hidden" name="locale" value="{{ $locale }}">

                <div class="mb-3">
                    <label for="qName" class="form-label fw-semibold">
                        {{ __('questions.fields.name') }}
                        <span class="text-muted fw-normal">({{ __('questions.fields.name_help') }})</span>
                    </label>
                    <input
                        type="text"
                        id="qName"
                        name="name"
                        class="form-control q-input"
                        value="{{ old('name') }}"
                        maxlength="255"
                        placeholder="{{ __('questions.fields.name_placeholder') }}"
                        {{ old('is_anonymous') ? 'disabled' : '' }}
                    >
                </div>

                <div class="mb-3">
                    <label for="qBody" class="form-label fw-semibold">
                        {{ __('questions.fields.question_body') }}
                        <span class="text-danger">*</span>
                    </label>
                    <textarea
                        id="qBody"
                        name="question_body"
                        class="form-control q-textarea"
                        maxlength="{{ $maxLength }}"
                        placeholder="{{ __('questions.fields.question_body_placeholder') }}"
                        required
                    >{{ old('question_body') }}</textarea>
                    <div class="d-flex justify-content-between mt-1">
                        <small class="text-muted">{{ __('questions.ask.intro') }}</small>
                        <small class="q-counter" id="qCounter" data-max="{{ $maxLength }}">
                            <span id="qCounterValue">0</span> / {{ $maxLength }} {{ __('questions.ask.counter_label') }}
                        </small>
                    </div>
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" role="switch"
                           id="qAnon" name="is_anonymous" value="1"
                           {{ old('is_anonymous') ? 'checked' : '' }}>
                    <label class="form-check-label" for="qAnon">
                        {{ __('questions.fields.is_anonymous') }}
                    </label>
                </div>

                <div class="d-grid d-sm-flex gap-2">
                    <button type="submit" class="btn q-btn-primary" id="qSubmit">
                        <i class="bi bi-send-fill me-1"></i>
                        {{ __('questions.ask.submit_button') }}
                    </button>
                    <a href="{{ route('questions.index') }}" class="btn btn-light q-btn-outline">
                        {{ __('questions.show.back_to_list') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const body = document.getElementById('qBody');
        const counter = document.getElementById('qCounterValue');
        const counterWrap = document.getElementById('qCounter');
        const max = parseInt(counterWrap.dataset.max || '20000', 10);
        const anon = document.getElementById('qAnon');
        const name = document.getElementById('qName');
        const form = document.getElementById('qForm');
        const submit = document.getElementById('qSubmit');

        function update() {
            const len = body.value.length;
            counter.textContent = len.toLocaleString();
            counterWrap.classList.toggle('is-warn', len > max * 0.8 && len < max);
            counterWrap.classList.toggle('is-danger', len >= max);
        }

        body.addEventListener('input', update);
        update();

        anon.addEventListener('change', function () {
            if (anon.checked) {
                name.value = '';
                name.setAttribute('disabled', 'disabled');
            } else {
                name.removeAttribute('disabled');
            }
        });

        form.addEventListener('submit', function () {
            submit.disabled = true;
            submit.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> {{ __('questions.ask.sending') }}';
        });
    })();
</script>
@endpush
