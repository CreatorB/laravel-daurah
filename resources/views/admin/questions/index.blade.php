@extends('layouts.app')

@section('title', __('questions.admin.title'))

@push('scripts')
<script>
(function () {
    const csrfToken = @json(csrf_token());
    const recentUrl = @json(route('admin.questions.recent'));
    const labels = {
        approve: @json(__('questions.admin.row.action_approve')),
        reject: @json(__('questions.admin.row.action_reject')),
        delete: @json(__('questions.admin.row.action_delete')),
        approveTitle: @json(__('questions.admin.row.confirm_approve')),
        rejectTitle: @json(__('questions.admin.row.confirm_reject')),
        deleteTitle: @json(__('questions.admin.row.confirm_delete')),
        flashApproved: @json(__('questions.admin.flash_approved')),
        flashRejected: @json(__('questions.admin.flash_rejected')),
        flashDeleted: @json(__('questions.admin.flash_deleted')),
        flashNewOne: @json(__('questions.admin.flash_new_one')),
        flashNewMany: @json(__('questions.admin.flash_new_many')),
        genericError: @json(__('questions.admin.flash_error')),
        anon: @json(__('questions.anonymous')),
        statusPending: @json(__('questions.status.pending')),
        statusApproved: @json(__('questions.status.approved')),
        statusRejected: @json(__('questions.status.rejected')),
    };

    const tab = @json($tab);
    const tbody = document.querySelector('table tbody');
    const countsBadge = document.querySelectorAll('[data-counts]');
    const liveIndicator = document.getElementById('live-indicator');
    const liveIndicatorText = document.getElementById('live-indicator-text');

    let isFetching = false;
    let lastId = @json($latestPendingId ?? 0);
    let pendingFlash = 0;
    let flashTimer = null;
    const seenIds = new Set();
    if (tbody) {
        tbody.querySelectorAll('tr[data-q-id]').forEach((el) => {
            const id = Number(el.getAttribute('data-q-id'));
            if (!isNaN(id)) seenIds.add(id);
        });
    }

    function escapeHtml(str) {
        return String(str ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function showFlash(text, variant) {
        if (!liveIndicator || !liveIndicatorText) return;
        liveIndicatorText.textContent = text;
        liveIndicator.classList.remove('d-none', 'alert-info', 'alert-success', 'alert-warning');
        liveIndicator.classList.add(variant === 'success' ? 'alert-success' : (variant === 'warning' ? 'alert-warning' : 'alert-info'));
    }

    function hideFlashDelayed(ms) {
        clearTimeout(flashTimer);
        flashTimer = setTimeout(() => {
            if (liveIndicator) liveIndicator.classList.add('d-none');
        }, ms || 4000);
    }

    function buildRow(item) {
        const tr = document.createElement('tr');
        tr.setAttribute('data-q-id', String(item.id));
        tr.classList.add('q-row-new');

        const nameCell = item.is_anonymous
            ? '<em class="text-muted">' + escapeHtml(labels.anon) + '</em>'
            : escapeHtml(item.name || '');

        const badgeClass = item.badge_class || 'bg-warning text-dark';
        const refDisplay = escapeHtml(item.display_ref || item.public_ref || ('#' + item.id));

        tr.innerHTML =
            '<td><input type="checkbox" name="ids[]" value="' + item.id + '"></td>' +
            '<td><span class="badge bg-light text-dark">' + refDisplay + '</span></td>' +
            '<td>' + nameCell + '</td>' +
            '<td class="text-muted small">' + escapeHtml(item.excerpt || '') + '</td>' +
            '<td><span class="badge bg-secondary">' + escapeHtml(String(item.locale || '').toUpperCase()) + '</span></td>' +
            '<td><span class="badge ' + badgeClass + '">' + escapeHtml(item.status_label || '') + '</span></td>' +
            '<td class="small text-muted">' + escapeHtml(item.created_at_human || '') + '</td>' +
            '<td class="text-end">' +
                '<div class="btn-group btn-group-sm" role="group">' +
                    (item.status !== 'approved' ? '<button type="button" class="btn btn-outline-success js-action" data-action="approve" data-id="' + item.id + '" data-url="' + item.approve_url + '" title="' + escapeHtml(labels.approveTitle) + '"><i class="fas fa-check"></i></button>' : '') +
                    (item.status !== 'rejected' ? '<button type="button" class="btn btn-outline-danger js-action" data-action="reject" data-id="' + item.id + '" data-url="' + item.reject_url + '" title="' + escapeHtml(labels.rejectTitle) + '"><i class="fas fa-times"></i></button>' : '') +
                    '<button type="button" class="btn btn-outline-secondary js-action" data-action="delete" data-id="' + item.id + '" data-url="' + item.delete_url + '" title="' + escapeHtml(labels.deleteTitle) + '"><i class="fas fa-trash"></i></button>' +
                '</div>' +
                '<a href="' + item.show_url + '" class="btn btn-sm btn-outline-primary ms-1"><i class="fas fa-eye"></i></a>' +
            '</td>';
        return tr;
    }

    function ensureEmptyRow() {
        if (!tbody) return;
        const existingEmpty = tbody.querySelector('tr[data-empty-row]');
        if (existingEmpty) existingEmpty.remove();
    }

    function ensureEmptyPlaceholder() {
        if (!tbody) return;
        const hasRows = tbody.querySelectorAll('tr[data-q-id]').length > 0;
        if (hasRows) return;
        tbody.innerHTML = '<tr data-empty-row><td colspan="8" class="text-center text-muted py-5"><i class="fas fa-inbox fa-2x d-block mb-2 opacity-50"></i>{{ __('questions.admin.empty') }}</td></tr>';
    }

    function updateCounts(counts) {
        countsBadge.forEach((el) => {
            const key = el.getAttribute('data-counts');
            if (counts && typeof counts[key] !== 'undefined') {
                el.textContent = counts[key];
            }
        });
    }

    async function fetchRecent() {
        if (isFetching || tab !== 'pending') return;
        isFetching = true;
        try {
            const params = new URLSearchParams();
            params.set('tab', tab);
            params.set('since_id', String(lastId));
            const res = await fetch(recentUrl + '?' + params.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                credentials: 'same-origin',
            });
            if (!res.ok) return;
            const payload = await res.json();
            const data = Array.isArray(payload.data) ? payload.data : [];
            updateCounts(payload.counts);

            const newOnes = data.filter((item) => !seenIds.has(Number(item.id)));
            newOnes.forEach((item) => seenIds.add(Number(item.id)));

            if (newOnes.length === 0) {
                if (data.length > 0 && data[data.length - 1] && data[data.length - 1].id) {
                    lastId = Math.max(lastId, Number(data[data.length - 1].id));
                }
                return;
            }
            ensureEmptyRow();

            newOnes.reverse().forEach((item) => {
                const row = buildRow(item);
                if (tbody.firstChild) {
                    tbody.insertBefore(row, tbody.firstChild);
                } else {
                    tbody.appendChild(row);
                }
            });
            if (newOnes[newOnes.length - 1] && newOnes[newOnes.length - 1].id) {
                lastId = Math.max(lastId, Number(newOnes[newOnes.length - 1].id));
            }

            pendingFlash += newOnes.length;
            const label = pendingFlash === 1 ? labels.flashNewOne : labels.flashNewMany.replace(':count', String(pendingFlash));
            showFlash(label, 'success');
            hideFlashDelayed(4000);
            setTimeout(() => {
                pendingFlash = Math.max(0, pendingFlash - newOnes.length);
            }, 4000);
        } catch (e) {
            // silent
        } finally {
            isFetching = false;
        }
    }

    async function performAction(btn) {
        const action = btn.getAttribute('data-action');
        const id = btn.getAttribute('data-id');
        const url = btn.getAttribute('data-url');

        if (action === 'delete') {
            if (!confirm(labels.deleteTitle)) return;
        }

        btn.disabled = true;
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        try {
            let method = 'PATCH';
            let body = null;
            const headers = {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            };
            if (action === 'delete') {
                method = 'DELETE';
            } else {
                headers['Content-Type'] = 'application/x-www-form-urlencoded';
                body = '_token=' + encodeURIComponent(csrfToken);
            }

            const res = await fetch(url, { method, headers, body, credentials: 'same-origin' });
            if (!res.ok) throw new Error('http');
            const json = await res.json().catch(() => ({}));

            const row = btn.closest('tr[data-q-id]');
            if (row) {
                row.style.transition = 'opacity .25s ease, transform .25s ease';
                row.style.opacity = '0';
                row.style.transform = 'translateX(20px)';
                setTimeout(() => {
                    row.parentNode && row.parentNode.removeChild(row);
                    ensureEmptyPlaceholder();
                }, 250);
            }

            if (json && json.counts) updateCounts(json.counts);
            if (json && json.message) showFlash(json.message, 'success');
            else if (action === 'approve') showFlash(labels.flashApproved, 'success');
            else if (action === 'reject') showFlash(labels.flashRejected, 'warning');
            else if (action === 'delete') showFlash(labels.flashDeleted, 'info');
            hideFlashDelayed(3500);

            // Refresh counts after action too
            fetch(recentUrl + '?tab=' + encodeURIComponent(tab), {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                credentials: 'same-origin',
            }).then(r => r.ok ? r.json() : null).then(p => { if (p && p.counts) updateCounts(p.counts); }).catch(() => {});
        } catch (e) {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
            showFlash(labels.genericError, 'warning');
            hideFlashDelayed(3000);
        }
    }

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.js-action');
        if (!btn) return;
        e.preventDefault();
        performAction(btn);
    });

    const POLL_MS = 8000;
    setInterval(fetchRecent, POLL_MS);
    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) fetchRecent();
    });
})();
</script>

<style>
.q-row-new {
    animation: qRowIn .5s ease-out;
}
@keyframes qRowIn {
    from { opacity: 0; transform: translateY(-6px); background: rgba(15,118,110,.08); }
    to { opacity: 1; transform: translateY(0); background: transparent; }
}
</style>
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

    <div id="live-indicator" class="alert alert-info py-2 small d-none mb-3" role="status">
        <i class="bi bi-dot"></i><span id="live-indicator-text">--</span>
    </div>

    <ul class="nav nav-pills mb-3 gap-2">
        @php
            $tabs = [
                'pending' => ['icon' => 'fa-hourglass-half'],
                'approved' => ['icon' => 'fa-check-circle'],
                'rejected' => ['icon' => 'fa-times-circle'],
            ];
        @endphp
        @foreach($tabs as $key => $meta)
            <li class="nav-item">
                <a class="nav-link {{ $tab === $key ? 'active bg-primary text-white' : 'text-primary bg-primary-subtle' }} d-flex align-items-center gap-2"
                   href="{{ route('admin.questions.index', array_merge(request()->query(), ['status' => $key])) }}">
                    <i class="fas {{ $meta['icon'] }}"></i>
                    {{ __('questions.admin.tabs.'.$key) }}
                    <span class="badge bg-light text-dark" data-counts="{{ $key }}">{{ $counts[$key] ?? 0 }}</span>
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
                            <th style="width: 200px;" class="text-end">{{ __('questions.admin.table.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($questions as $q)
                        <tr data-q-id="{{ $q->id }}">
                            <td><input type="checkbox" name="ids[]" value="{{ $q->id }}"></td>
                            <td><span class="badge bg-light text-dark">{{ $q->display_ref }}</span></td>
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
                                <div class="btn-group btn-group-sm" role="group">
                                    @if(! $q->isApproved())
                                        <button type="button" class="btn btn-outline-success js-action"
                                                data-action="approve" data-id="{{ $q->id }}"
                                                data-url="{{ route('admin.questions.approve', $q) }}"
                                                title="{{ __('questions.admin.row.confirm_approve') }}">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                    @if(! $q->isRejected())
                                        <button type="button" class="btn btn-outline-danger js-action"
                                                data-action="reject" data-id="{{ $q->id }}"
                                                data-url="{{ route('admin.questions.reject', $q) }}"
                                                title="{{ __('questions.admin.row.confirm_reject') }}">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                    <button type="button" class="btn btn-outline-secondary js-action"
                                            data-action="delete" data-id="{{ $q->id }}"
                                            data-url="{{ route('admin.questions.destroy', $q) }}"
                                            title="{{ __('questions.admin.row.confirm_delete') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <a href="{{ route('admin.questions.show', $q) }}" class="btn btn-sm btn-outline-primary ms-1">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr data-empty-row>
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
