<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentVerificationSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'query' => ['required', 'string', 'max:255'],
        ];
    }
}
