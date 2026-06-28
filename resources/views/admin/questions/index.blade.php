@extends('layouts.app')

@section('title', __('questions.admin.title'))

@push('scripts')
<script>
function toggleAll(master) {
    document.querySelectorAll('input[name="ids[]"]').forEach(cb => cb.checked = master.checked);
}
function confirmBulk() {
    const checked = document.querySelectorAll('input[name="ids[]"]:checked').length;
    if (checked === 0) {
        alert(@json(__('questions.admin.bulk.no_selection')));
        return false;
    }
    const action = document.querySelector('select[name="action"]').value;
    const labels = {
        approve: @json(__('questions.admin.bulk.confirm_approve')),
        reject: @json(__('questions.admin.bulk.confirm_reject')),
        delete: @json(__('questions.admin.bulk.confirm_delete')),
    };
    return confirm(labels[action] + ' (' + checked + ')');
}
</script>
@endpush

@section('content')
<div class="container-fluid px-3 px-md-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 gap-2">
        <h2 class="mb-0 gradient-text">
            <i class="fas fa-question-circle me-2"></i>{{ __('questions.admin.title') }}
        </h2>
        <span class="badge bg-secondary">
            {{ $questions->total() }} {{ __('questions.admin.total_items') }}
        </span>
    </div>

    <ul class="nav nav-pills mb-3 gap-2">
        @php
            $tabs = [
                'pending' => ['icon' => 'fa-hourglass-half', 'color' => 'warning'],
                'approved' => ['icon' => 'fa-check-circle', 'color' => 'success'],
                'rejected' => ['icon' => 'fa-times-circle', 'color' => 'danger'],
            ];
        @endphp
        @foreach($tabs as $key => $meta)
            <li class="nav-item">
                <a class="nav-link {{ $tab === $key ? 'active bg-'.$meta['color'] : 'text-'.$meta['color'] }} d-flex align-items-center gap-2"
                   href="{{ route('admin.questions.index', array_merge(request()->query(), ['status' => $key])) }}">
                    <i class="fas {{ $meta['icon'] }}"></i>
                    {{ __('questions.admin.tabs.'.$key) }}
                    <span class="badge bg-light text-dark">{{ $counts[$key] ?? 0 }}</span>
                </a>
            </li>
        @endforeach
    </ul>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.questions.index') }}" class="row g-2 align-items-end">
                <input type="hidden" name="status" value="{{ $tab }}">
                <div class="col-12 col-md-5">
                    <label class="form-label small mb-1">{{ __('questions.admin.table.preview') }}</label>
                    <input type="text" name="q" value="{{ $filters['q'] }}"
                           class="form-control"
                           placeholder="{{ __('questions.admin.filters.search_placeholder') }}">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small mb-1">{{ __('questions.admin.filters.locale') }}</label>
                    <select name="locale" class="form-select">
                        <option value="">{{ __('questions.admin.filters.all_locales') }}</option>
                        @foreach(($availableLocales ?? ['ar','id']) as $lc)
                            <option value="{{ $lc }}" {{ ($filters['locale'] ?? '') === $lc ? 'selected' : '' }}>
                                {{ strtoupper($lc) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small mb-1">{{ __('questions.admin.filters.lesson_code') }}</label>
                    <input type="text" name="lesson_code" value="{{ $filters['lesson_code'] }}" class="form-control">
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button class="btn btn-primary flex-fill">
                        <i class="fas fa-filter me-1"></i>{{ __('questions.admin.filters.apply') }}
                    </button>
                    <a href="{{ route('admin.questions.index', ['status' => $tab]) }}" class="btn btn-light">
                        {{ __('questions.admin.filters.reset') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.questions.bulk') }}" id="bulk-form" onsubmit="return confirmBulk()">
        @csrf
        <input type="hidden" name="status" value="{{ $tab }}">

        <div class="card shadow-sm mb-3">
            <div class="card-body py-2">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="small text-muted me-2">{{ __('questions.admin.bulk.label') }}:</span>
                    <select name="action" class="form-select form-select-sm" style="width:auto">
                        <option value="approve">{{ __('questions.admin.bulk.action_approve') }}</option>
                        <option value="reject">{{ __('questions.admin.bulk.action_reject') }}</option>
                        <option value="delete">{{ __('questions.admin.bulk.action_delete') }}</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-check me-1"></i>{{ __('questions.admin.bulk.apply') }}
                    </button>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 36px;">
                                <input type="checkbox" onchange="toggleAll(this)" aria-label="select-all">
                            </th>
                            <th style="width: 120px;">{{ __('questions.admin.table.ref') }}</th>
                            <th>{{ __('questions.admin.table.name') }}</th>
                            <th>{{ __('questions.admin.table.preview') }}</th>
                            <th style="width: 70px;">{{ __('questions.admin.table.locale') }}</th>
                            <th style="width: 130px;">{{ __('questions.admin.table.status') }}</th>
                            <th style="width: 130px;">{{ __('questions.admin.table.created_at') }}</th>
                            <th style="width: 90px;" class="text-end">{{ __('questions.admin.table.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($questions as $q)
                        <tr>
                            <td><input type="checkbox" name="ids[]" value="{{ $q->id }}"></td>
                            <td><span class="badge bg-light text-dark">{{ $q->public_ref }}</span></td>
                            <td>
                                @if($q->is_anonymous || blank($q->name))
                                    <em class="text-muted">{{ __('questions.anonymous') }}</em>
                                @else
                                    {{ $q->name }}
                                @endif
                            </td>
                            <td class="text-muted small">{{ $q->excerpt }}</td>
                            <td><span class="badge bg-secondary">{{ strtoupper($q->locale) }}</span></td>
                            <td>
                                <span class="badge {{ $q->status->badgeClass() }}">
                                    {{ $q->status->label() }}
                                </span>
                            </td>
                            <td class="small text-muted">{{ $q->created_at->format('d M Y H:i') }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.questions.show', $q) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="fas fa-inbox fa-2x d-block mb-2 opacity-50"></i>
                                {{ __('questions.admin.empty') }}
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </form>

    <div class="mt-3 d-flex justify-content-center">
        {{ $questions->links() }}
    </div>
</div>
@endsection