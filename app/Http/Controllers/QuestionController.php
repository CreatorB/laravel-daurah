<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuestionRequest;
use App\Models\Question;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    public function index(Request $request): View
    {
        $locale = app()->getLocale();

        $questions = Question::query()
            ->approved()
            ->orderBy('id')
            ->paginate((int) config('questions.per_page', 10))
            ->withQueryString();

        return view('questions.index', [
            'questions' => $questions,
            'locale' => $locale,
            'latestApprovedAt' => optional(
                Question::query()->approved()->orderByDesc('approved_at')->orderByDesc('id')->first()
            )->approved_at?->toIso8601String(),
        ]);
    }

    public function recent(Request $request): JsonResponse
    {
        $locale = $request->query('locale') ?: app()->getLocale();
        $since = $request->query('since');
        $currentRaw = (string) $request->query('current', '');

        $currentIds = array_values(array_filter(array_map('intval', explode(',', $currentRaw))));

        $query = Question::query()
            ->approved()
            ->when($locale, fn ($q) => $q->where('locale', $locale))
            ->orderByDesc('approved_at')
            ->orderByDesc('id')
            ->limit(20);

        if ($since) {
            try {
                $sinceDt = \Carbon\Carbon::parse($since);
            } catch (\Throwable $e) {
                $sinceDt = null;
            }

            if ($sinceDt) {
                $query->where('approved_at', '>', $sinceDt);
            }
        }

        $items = $query->get()->map(function (Question $q) {
            return [
                'id' => $q->id,
                'public_ref' => $q->public_ref,
                'locale' => $q->locale,
                'display_name' => $q->display_name,
                'excerpt' => $q->excerpt,
                'approved_at' => optional($q->approved_at)->toIso8601String(),
                'published_at' => optional($q->published_at)->toIso8601String(),
                'show_url' => route('questions.show', $q->public_ref),
            ];
        })->values();

        $removedIds = [];
        if (! empty($currentIds)) {
            $stillApproved = Question::query()
                ->approved()
                ->whereIn('id', $currentIds)
                ->pluck('id')
                ->map(fn ($v) => (int) $v)
                ->all();
            $removedIds = array_values(array_diff($currentIds, $stillApproved));
        }

        return response()->json([
            'data' => $items,
            'removed_ids' => $removedIds,
            'server_time' => now()->toIso8601String(),
        ]);
    }

    public function create(Request $request): View
    {
        return view('questions.ask', [
            'locale' => app()->getLocale(),
            'maxLength' => (int) config('questions.max_length', 20000),
        ]);
    }

    public function store(StoreQuestionRequest $request): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request) {
                Question::create([
                    'name' => $request->boolean('is_anonymous') ? null : $request->input('name'),
                    'question_body' => trim((string) $request->input('question_body')),
                    'is_anonymous' => $request->boolean('is_anonymous'),
                    'locale' => $request->input('locale'),
                    'status' => \App\Enums\QuestionStatus::Pending,
                    'lesson_code' => $request->input('lesson_code'),
                ]);
            });
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', __('questions.flash.send_error'));
        }

        return redirect()
            ->route('questions.ask', ['locale' => app()->getLocale()])
            ->with('success', __('questions.flash.sent_success'));
    }

    public function show(Question $question): View
    {
        if (! $question->isApproved()) {
            abort(404);
        }

        return view('questions.show', [
            'question' => $question,
            'locale' => $question->locale ?: app()->getLocale(),
        ]);
    }
}
