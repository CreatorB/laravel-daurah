<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ModerateQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
