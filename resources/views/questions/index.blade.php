@extends('questions.layout')

@section('title', __('questions.list.title'))

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="q-hero mb-4 mb-md-5 text-center">
            <h1 class="h2 fw-bold mb-2">{{ __('questions.list.page_title') }}</h1>
            <p class="lead mb-0 opacity-90">{{ __('questions.list.subtitle') }}</p>
        </div>

        <div id="qLiveStatus" class="text-center small text-muted mb-2" style="display:none;">
            <span class="q-pill" id="qLiveStatusPill">
                <i class="bi bi-dot"></i><span id="qLiveStatusText">--</span>
            </span>
        </div>

        @if($questions->count() === 0)
            <div class="q-empty" id="qEmpty">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                {{ __('questions.list.empty') }}
            </div>
            <div class="d-flex flex-column gap-3" id="qList"></div>
        @else
            <div class="d-flex flex-column gap-3" id="qList">
                @foreach($questions as $q)
                    <article class="q-card p-4" data-q-id="{{ $q->id }}" data-q-ref="{{ $q->public_ref }}">
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

            <div class="mt-4 d-flex justify-content-center q-pagination" id="qPagination">
                {{ $questions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const list = document.getElementById('qList');
    const emptyEl = document.getElementById('qEmpty');
    const paginationEl = document.getElementById('qPagination');
    const liveStatus = document.getElementById('qLiveStatus');
    const liveStatusText = document.getElementById('qLiveStatusText');
    const liveStatusPill = document.getElementById('qLiveStatusPill');

    if (!list) return;

    const seenIds = new Set();
    list.querySelectorAll('[data-q-id]').forEach((el) => {
        seenIds.add(el.getAttribute('data-q-id'));
    });

    const locale = @json($locale);
    const since = @json($latestApprovedAt ?? null);
    const recentUrl = @json(route('questions.recent'));
    const showMoreText = @json(__('questions.list.show_more'));
    const flashNewOne = @json(__('questions.list.flash_new_one'));
    const flashNewMany = @json(__('questions.list.flash_new_many'));
    const flashRemovedOne = @json(__('questions.list.flash_removed_one'));
    const flashRemovedMany = @json(__('questions.list.flash_removed_many'));
    const arrowClass = 'bi-arrow-' + (locale === 'ar' ? 'left' : 'right');

    let pendingNew = 0;
    let pendingRemoved = 0;
    let lastSince = since;
    let isFetching = false;

    function escapeHtml(str) {
        return String(str ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function nl2br(str) {
        return escapeHtml(str).replace(/\r?\n/g, '<br>');
    }

    function formatDate(iso) {
        if (!iso) return '';
        try {
            const d = new Date(iso);
            const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            const dd = String(d.getDate()).padStart(2, '0');
            const mm = months[d.getMonth()];
            const yy = d.getFullYear();
            return dd + ' ' + mm + ' ' + yy;
        } catch (e) {
            return '';
        }
    }

    function buildCard(item) {
        const article = document.createElement('article');
        article.className = 'q-card p-4 q-card-new';
        article.setAttribute('data-q-id', String(item.id));
        article.setAttribute('data-q-ref', item.public_ref || '');

        const ref = escapeHtml(item.public_ref || '');
        const lc = escapeHtml(String(item.locale || '').toUpperCase());
        const name = escapeHtml(item.display_name || '');
        const date = formatDate(item.published_at || item.approved_at);
        const excerpt = nl2br(item.excerpt || '');
        const url = item.show_url || '#';

        article.innerHTML =
            '<div class="d-flex flex-wrap align-items-center gap-2 mb-2">' +
                '<span class="q-pill"><i class="bi bi-hash"></i>' + ref + '</span>' +
                '<span class="badge bg-light text-dark border">' + lc + '</span>' +
                '<span class="text-muted small"><i class="bi bi-person-circle me-1"></i>' + name + '</span>' +
                '<span class="text-muted small ms-auto"><i class="bi bi-calendar3 me-1"></i>' + date + '</span>' +
            '</div>' +
            '<p class="mb-3" style="white-space: pre-line;">' + excerpt + '</p>' +
            '<a href="' + url + '" class="btn btn-sm q-btn-outline btn-outline-secondary">' +
                showMoreText +
                ' <i class="bi ' + arrowClass + ' ms-1"></i>' +
            '</a>';

        return article;
    }

    function showFlash(text, variant) {
        if (!liveStatus || !liveStatusText || !liveStatusPill) return;
        liveStatusText.textContent = text;
        liveStatusPill.classList.remove('q-flash-info', 'q-flash-success');
        liveStatusPill.classList.add(variant === 'success' ? 'q-flash-success' : 'q-flash-info');
        liveStatus.style.display = 'block';
    }

    function hideFlash() {
        if (!liveStatus) return;
        liveStatus.style.display = 'none';
    }

    function currentIdList() {
        return Array.from(list.querySelectorAll('[data-q-id]')).map(
            (el) => el.getAttribute('data-q-id')
        );
    }

    function refreshEmptyState() {
        if (!emptyEl) return;
        if (list.querySelectorAll('[data-q-id]').length === 0) {
            emptyEl.style.display = '';
        } else {
            emptyEl.style.display = 'none';
        }
    }

    function applyRemovals(removedIds) {
        if (!Array.isArray(removedIds) || removedIds.length === 0) return 0;
        let count = 0;
        removedIds.forEach((id) => {
            const idStr = String(id);
            const el = list.querySelector('[data-q-id="' + idStr + '"]');
            if (el) {
                el.classList.add('q-card-removing');
                seenIds.delete(idStr);
                count += 1;
            }
        });
        if (count > 0) {
            setTimeout(() => {
                removedIds.forEach((id) => {
                    const el = list.querySelector('[data-q-id="' + String(id) + '"]');
                    if (el && el.parentNode) el.parentNode.removeChild(el);
                });
                refreshEmptyState();
            }, 350);
        }
        return count;
    }

    function formatNewFlash(n) {
        if (n === 1) return flashNewOne;
        return flashNewMany.replace(':count', String(n));
    }

    function formatRemovedFlash(n) {
        if (n === 1) return flashRemovedOne;
        return flashRemovedMany.replace(':count', String(n));
    }

    async function fetchRecent() {
        if (isFetching) return;
        isFetching = true;
        try {
            const params = new URLSearchParams();
            params.set('locale', locale);
            if (lastSince) params.set('since', lastSince);
            const currentIds = currentIdList();
            if (currentIds.length > 0) {
                params.set('current', currentIds.join(','));
            }
            const res = await fetch(recentUrl + '?' + params.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                credentials: 'same-origin',
            });
            if (!res.ok) return;
            const payload = await res.json();
            const data = Array.isArray(payload.data) ? payload.data : [];
            const removedIds = Array.isArray(payload.removed_ids) ? payload.removed_ids : [];

            const removedCount = applyRemovals(removedIds);

            if (data.length > 0 && emptyEl) {
                emptyEl.style.display = 'none';
            }

            const newOnes = [];
            data.forEach((item) => {
                const id = String(item.id);
                if (seenIds.has(id)) return;
                seenIds.add(id);
                newOnes.push(item);
                if (item.approved_at) lastSince = item.approved_at;
            });

            if (newOnes.length > 0) {
                newOnes.reverse().forEach((item) => {
                    const card = buildCard(item);
                    list.appendChild(card);
                });
                if (paginationEl) paginationEl.style.display = 'none';
            }

            if (newOnes.length === 0 && removedCount === 0) return;

            if (newOnes.length > 0) {
                pendingNew += newOnes.length;
                showFlash(formatNewFlash(pendingNew), 'success');
                setTimeout(() => {
                    pendingNew = Math.max(0, pendingNew - newOnes.length);
                    if (pendingNew === 0 && pendingRemoved === 0) hideFlash();
                }, 4000);
            }
            if (removedCount > 0) {
                pendingRemoved += removedCount;
                showFlash(formatRemovedFlash(pendingRemoved), 'info');
                setTimeout(() => {
                    pendingRemoved = Math.max(0, pendingRemoved - removedCount);
                    if (pendingNew === 0 && pendingRemoved === 0) hideFlash();
                }, 4000);
            }
        } catch (e) {
            // silent
        } finally {
            isFetching = false;
        }
    }

    const POLL_MS = 8000;
    setInterval(fetchRecent, POLL_MS);
    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) fetchRecent();
    });
})();
</script>

<style>
.q-card-new {
    animation: qFadeIn .6s ease-out;
}
.q-card-removing {
    animation: qFadeOut .35s ease-out forwards;
}
@keyframes qFadeIn {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes qFadeOut {
    from { opacity: 1; transform: translateY(0); max-height: 600px; margin-bottom: 1rem; }
    to { opacity: 0; transform: translateY(-6px); max-height: 0; margin-bottom: 0; padding-top: 0; padding-bottom: 0; }
}
.q-flash-info { background: rgba(15,118,110,.08) !important; color: var(--q-primary-dark) !important; }
.q-flash-success { background: rgba(22,163,74,.12) !important; color: #15803D !important; }
</style>
@endpush