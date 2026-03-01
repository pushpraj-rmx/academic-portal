<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please provide your name.',
            'email.required' => 'Please provide your email address.',
            'subject.required' => 'Please provide a subject.',
            'message.required' => 'Please provide your message.',
        ];
    }
}
