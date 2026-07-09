<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Session;

class ModerateQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Session::has('user_id') && Session::get('role') === 'admin';
    }

    public function rules(): array
    {
        return [
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
