<?php

namespace App\Http\Controllers\Admin;

use App\Enums\QuestionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ModerateQuestionRequest;
use App\Models\Question;
use Illuminate\Contracts\View\View;
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
        ]);
    }

    public function show(Question $question): View
    {
        return view('admin.questions.show', [
            'question' => $question->loadMissing(['approver', 'rejecter']),
        ]);
    }

    public function approve(ModerateQuestionRequest $request, Question $question): RedirectResponse
    {
        if ($question->isApproved()) {
            return back()->with('info', __('questions.admin.already_approved'));
        }

        $question->forceFill([
            'status' => QuestionStatus::Approved,
            'approved_at' => now(),
            'approved_by' => Session::get('user_id'),
            'rejected_at' => null,
            'rejected_by' => null,
            'published_at' => $question->published_at ?: now(),
            'admin_note' => $request->input('admin_note', $question->admin_note),
        ])->save();

        return redirect()
            ->route('admin.questions.index', ['status' => QuestionStatus::Approved->value])
            ->with('success', __('questions.admin.approved_success'));
    }

    public function reject(ModerateQuestionRequest $request, Question $question): RedirectResponse
    {
        if ($question->isRejected()) {
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

        return redirect()
            ->route('admin.questions.index', ['status' => QuestionStatus::Rejected->value])
            ->with('success', __('questions.admin.rejected_success'));
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
            Question::whereIn('id', $ids)->update([
                'status' => QuestionStatus::Approved->value,
                'approved_at' => now(),
                'approved_by' => $adminId,
                'rejected_at' => null,
                'rejected_by' => null,
                'published_at' => \DB::raw('COALESCE(published_at, NOW())'),
                'updated_at' => now(),
            ]);

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
