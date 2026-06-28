<?php

namespace App\Http\Controllers\Admin;

use App\Enums\QuestionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ModerateQuestionRequest;
use App\Models\Question;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class QuestionController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->query('status', QuestionStatus::Pending->value);
        $allowed = [QuestionStatus::Pending->value, QuestionStatus::Approved->value, QuestionStatus::Rejected->value];
        if (! in_array($tab, $allowed, true)) {
            $tab = QuestionStatus::Pending->value;
        }

        $query = Question::query()
            ->search($request->query('q'))
            ->when($request->filled('locale'), fn ($q) => $q->where('locale', $request->query('locale')))
            ->when($request->filled('lesson_code'), fn ($q) => $q->where('lesson_code', $request->query('lesson_code')));

        match ($tab) {
            QuestionStatus::Approved->value => $query->approved()->latestPublished(),
            QuestionStatus::Rejected->value => $query->rejected()->orderByDesc('rejected_at')->orderByDesc('id'),
            default => $query->pending()->orderByDesc('id'),
        };

        $questions = $query->paginate(15)->withQueryString();

        return view('admin.questions.index', [
            'questions' => $questions,
            'tab' => $tab,
            'filters' => [
                'q' => $request->query('q'),
                'locale' => $request->query('locale'),
                'lesson_code' => $request->query('lesson_code'),
            ],
            'counts' => [
                'pending' => Question::pending()->count(),
                'approved' => Question::approved()->count(),
                'rejected' => Question::rejected()->count(),
            ],
            'latestPendingId' => (int) (Question::query()->pending()->orderByDesc('id')->value('id') ?? 0),
        ]);
    }

    public function show(Question $question): View
    {
        return view('admin.questions.show', [
            'question' => $question->loadMissing(['approver', 'rejecter']),
        ]);
    }

    public function approve(ModerateQuestionRequest $request, Question $question)
    {
        if ($question->isApproved()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'ok' => false,
                    'info' => __('questions.admin.already_approved'),
                ]);
            }
            return back()->with('info', __('questions.admin.already_approved'));
        }

        $publishedAt = $question->published_at ?: now();

        $question->forceFill([
            'status' => QuestionStatus::Approved,
            'approved_at' => now(),
            'approved_by' => Session::get('user_id'),
            'rejected_at' => null,
            'rejected_by' => null,
            'published_at' => $publishedAt,
            'public_ref' => $question->public_ref ?: Question::generatePublicRefForDate($publishedAt),
            'admin_note' => $request->input('admin_note', $question->admin_note),
        ])->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'id' => $question->id,
                'message' => __('questions.admin.approved_success'),
                'counts' => [
                    'pending' => Question::pending()->count(),
                    'approved' => Question::approved()->count(),
                    'rejected' => Question::rejected()->count(),
                ],
            ]);
        }

        return redirect()
            ->route('admin.questions.index', ['status' => QuestionStatus::Approved->value])
            ->with('success', __('questions.admin.approved_success'));
    }

    public function reject(ModerateQuestionRequest $request, Question $question)
    {
        if ($question->isRejected()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'ok' => false,
                    'info' => __('questions.admin.already_rejected'),
                ]);
            }
            return back()->with('info', __('questions.admin.already_rejected'));
        }

        $question->forceFill([
            'status' => QuestionStatus::Rejected,
            'rejected_at' => now(),
            'rejected_by' => Session::get('user_id'),
            'approved_at' => null,
            'approved_by' => null,
            'published_at' => null,
            'admin_note' => $request->input('admin_note', $question->admin_note),
        ])->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'id' => $question->id,
                'message' => __('questions.admin.rejected_success'),
                'counts' => [
                    'pending' => Question::pending()->count(),
                    'approved' => Question::approved()->count(),
                    'rejected' => Question::rejected()->count(),
                ],
            ]);
        }

        return redirect()
            ->route('admin.questions.index', ['status' => QuestionStatus::Rejected->value])
            ->with('success', __('questions.admin.rejected_success'));
    }

    public function recent(Request $request): JsonResponse
    {
        $tab = $request->query('tab');
        if (! in_array($tab, ['pending', 'approved', 'rejected'], true)) {
            $tab = 'pending';
        }
        $sinceId = (int) $request->query('since_id', 0);

        $query = Question::query()->pending()->orderByDesc('id')->limit(20);
        if ($sinceId > 0) {
            $query->where('id', '>', $sinceId);
        }
        $newItems = $tab === 'pending' ? $query->get() : collect();

        $payload = $newItems->map(function (Question $q) {
            return [
                'id' => $q->id,
                'public_ref' => $q->public_ref,
                'display_ref' => $q->display_ref,
                'name' => $q->display_name,
                'is_anonymous' => (bool) $q->is_anonymous,
                'excerpt' => $q->excerpt,
                'locale' => $q->locale,
                'status' => $q->status->value,
                'status_label' => $q->status->label(),
                'badge_class' => $q->status->badgeClass(),
                'created_at' => optional($q->created_at)->toIso8601String(),
                'created_at_human' => $q->created_at?->format('d M Y H:i'),
                'show_url' => route('admin.questions.show', $q),
                'approve_url' => route('admin.questions.approve', $q),
                'reject_url' => route('admin.questions.reject', $q),
                'delete_url' => route('admin.questions.destroy', $q),
            ];
        })->values();

        return response()->json([
            'data' => $payload,
            'counts' => [
                'pending' => Question::pending()->count(),
                'approved' => Question::approved()->count(),
                'rejected' => Question::rejected()->count(),
            ],
            'server_time' => now()->toIso8601String(),
        ]);
    }

    public function destroy(Request $request, Question $question)
    {
        $question->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'id' => $question->id,
                'message' => __('questions.admin.deleted_success'),
                'counts' => [
                    'pending' => Question::pending()->count(),
                    'approved' => Question::approved()->count(),
                    'rejected' => Question::rejected()->count(),
                ],
            ]);
        }

        return redirect()
            ->route('admin.questions.index')
            ->with('success', __('questions.admin.deleted_success'));
    }

    public function bulk(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:questions,id'],
            'action' => ['required', 'in:approve,reject,delete'],
        ]);

        $ids = $validated['ids'];
        $action = $validated['action'];
        $adminId = Session::get('user_id');
        $count = count($ids);

        if ($action === 'delete') {
            Question::whereIn('id', $ids)->delete();

            return redirect()
                ->route('admin.questions.index', ['status' => $request->query('status')])
                ->with('success', trans_choice('questions.admin.bulk.deleted', $count, ['count' => $count]));
        }

        if ($action === 'approve') {
            $now = now();
            $questions = Question::whereIn('id', $ids)->get();
            foreach ($questions as $q) {
                $publishedAt = $q->published_at ?: $now;
                $q->forceFill([
                    'status' => QuestionStatus::Approved,
                    'approved_at' => $now,
                    'approved_by' => $adminId,
                    'rejected_at' => null,
                    'rejected_by' => null,
                    'published_at' => $publishedAt,
                    'public_ref' => $q->public_ref ?: Question::generatePublicRefForDate($publishedAt),
                    'updated_at' => $now,
                ])->save();
            }

            return redirect()
                ->route('admin.questions.index', ['status' => QuestionStatus::Approved->value])
                ->with('success', trans_choice('questions.admin.bulk.approved', $count, ['count' => $count]));
        }

        Question::whereIn('id', $ids)->update([
            'status' => QuestionStatus::Rejected->value,
            'rejected_at' => now(),
            'rejected_by' => $adminId,
            'approved_at' => null,
            'approved_by' => null,
            'published_at' => null,
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('admin.questions.index', ['status' => QuestionStatus::Rejected->value])
            ->with('success', trans_choice('questions.admin.bulk.rejected', $count, ['count' => $count]));
    }
}
