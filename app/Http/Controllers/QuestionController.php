<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuestionRequest;
use App\Models\Question;
use Illuminate\Contracts\View\View;
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
