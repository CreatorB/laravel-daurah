<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $max = (int) config('questions.max_length', 20000);

        return [
            'name' => ['nullable', 'string', 'max:255'],
            'question_body' => ['required', 'string', 'min:3', 'max:'.$max],
            'is_anonymous' => ['nullable', 'boolean'],
            'locale' => ['required', Rule::in(config('questions.locales', ['ar', 'id']))],
            'lesson_code' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.max' => __('questions.validation.name_max'),
            'question_body.required' => __('questions.validation.question_required'),
            'question_body.min' => __('questions.validation.question_min'),
            'question_body.max' => __('questions.validation.question_max'),
            'locale.in' => __('questions.validation.locale_invalid'),
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('questions.fields.name'),
            'question_body' => __('questions.fields.question_body'),
            'is_anonymous' => __('questions.fields.is_anonymous'),
            'locale' => __('questions.fields.locale'),
            'lesson_code' => __('questions.fields.lesson_code'),
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_anonymous' => $this->boolean('is_anonymous'),
            'locale' => $this->input('locale') ?: app()->getLocale(),
        ]);
    }
}
