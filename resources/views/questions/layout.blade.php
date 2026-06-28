@php
    $locale = app()->getLocale();
    $dir = config('questions.direction.'.$locale, 'ltr');
    $availableLocales = (array) config('questions.locales', ['ar', 'id']);
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $dir }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('questions.app_name'))</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --q-primary: #0F766E;
            --q-primary-dark: #115E59;
            --q-accent: #D4AF37;
            --q-bg: #F8FAFC;
            --q-card: #FFFFFF;
            --q-muted: #64748B;
        }
        html, body { background: var(--q-bg); }
        body {
            font-family: {{ $dir === 'rtl' ? '"Tajawal","Cairo","Segoe UI",sans-serif' : '"Inter","Segoe UI",Roboto,sans-serif' }};
            min-height: 100vh;
            color: #0F172A;
        }
        [dir="rtl"] body { text-align: right; }

        .q-navbar {
            background: linear-gradient(135deg, var(--q-primary) 0%, var(--q-primary-dark) 100%);
        }
        .q-navbar .nav-link, .q-navbar .navbar-brand { color: rgba(255,255,255,.92); }
        .q-navbar .nav-link:hover { color: #fff; }

        .q-hero {
            background: radial-gradient(circle at top right, rgba(212,175,55,.18), transparent 60%),
                        linear-gradient(135deg, var(--q-primary) 0%, var(--q-primary-dark) 100%);
            color: #fff;
            border-radius: 24px;
            padding: 3rem 2rem;
        }
        .q-card {
            background: var(--q-card);
            border: 1px solid rgba(15,23,42,.06);
            border-radius: 18px;
            box-shadow: 0 1px 2px rgba(15,23,42,.04);
            transition: transform .15s ease, box-shadow .15s ease;
        }
        .q-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(15,23,42,.08);
        }
        .q-input, .q-textarea {
            border-radius: 14px;
            border: 1px solid #E2E8F0;
            padding: .85rem 1rem;
            background: #fff;
        }
        .q-input:focus, .q-textarea:focus {
            border-color: var(--q-primary);
            box-shadow: 0 0 0 .2rem rgba(15,118,110,.15);
        }
        .q-textarea { min-height: 180px; resize: vertical; }

        .q-btn-primary {
            background: linear-gradient(135deg, var(--q-primary), var(--q-primary-dark));
            border: none;
            color: #fff;
            border-radius: 14px;
            padding: .85rem 1.6rem;
            font-weight: 600;
            letter-spacing: .02em;
        }
        .q-btn-primary:hover { color: #fff; opacity: .95; }
        .q-btn-outline {
            border-radius: 14px;
            padding: .8rem 1.4rem;
            font-weight: 600;
        }
        .q-pill {
            display: inline-flex; align-items: center; gap: .35rem;
            background: rgba(15,118,110,.08);
            color: var(--q-primary-dark);
            border-radius: 999px;
            padding: .25rem .75rem;
            font-size: .8rem;
            font-weight: 600;
        }
        .q-divider { height: 1px; background: rgba(15,23,42,.06); margin: 1rem 0; }
        .q-footer { color: var(--q-muted); }

        .q-counter {
            font-size: .8rem;
            color: var(--q-muted);
        }
        .q-counter.is-warn { color: #B45309; }
        .q-counter.is-danger { color: #B91C1C; }

        .q-lang-pill .dropdown-item.active,
        .q-lang-pill .dropdown-item:active {
            background: var(--q-primary);
            color: #fff;
        }

        .q-empty {
            border: 2px dashed #CBD5E1;
            border-radius: 18px;
            padding: 3rem 2rem;
            text-align: center;
            color: var(--q-muted);
        }

        .q-pagination .page-link { border-radius: 10px !important; margin: 0 2px; }

        @media (max-width: 575.98px) {
            .q-hero { padding: 2rem 1.25rem; border-radius: 18px; }
            .q-btn-primary, .q-btn-outline { width: 100%; }
        }
    </style>

    @stack('head')
</head>
<body>

<nav class="q-navbar navbar navbar-expand-lg shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('questions.index') }}">
            <i class="bi bi-bookmark-star-fill me-1"></i>
            {{ __('questions.app_name') }}
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#qNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="qNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('questions.index') }}">
                        <i class="bi bi-list-ul me-1"></i>{{ __('questions.nav.list') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('questions.ask') }}">
                        <i class="bi bi-pencil-square me-1"></i>{{ __('questions.nav.ask') }}
                    </a>
                </li>
            </ul>

            <div class="dropdown q-lang-pill">
                <button class="btn btn-sm btn-light dropdown-toggle d-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-translate"></i>
                    <span class="fw-semibold">{{ strtoupper($locale) }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    @foreach($availableLocales as $code)
                        <li>
                            <a class="dropdown-item d-flex align-items-center justify-content-between {{ $code === $locale ? 'active' : '' }}"
                               href="{{ route('locale.switch', $code) }}"
                               aria-current="{{ $code === $locale ? 'true' : 'false' }}">
                                <span>{{ __('questions.language_switch.'.($code === 'ar' ? 'arabic' : 'indonesian')) }}</span>
                                @if($code === $locale)
                                    <i class="bi bi-check-lg ms-2"></i>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</nav>

<main class="container py-4 py-md-5">
    @if(session('success'))
        <div class="alert alert-success q-card border-0 mb-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger q-card border-0 mb-3">{{ session('error') }}</div>
    @endif
    @if(session('info'))
        <div class="alert alert-info q-card border-0 mb-3">{{ session('info') }}</div>
    @endif

    @yield('content')
</main>

<footer class="q-footer text-center py-4 small">
    &copy; {{ date('Y') }} {{ __('questions.app_name') }}
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
