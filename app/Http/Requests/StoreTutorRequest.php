<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTutorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:128'],
            'first_lastname' => ['required', 'string', 'max:64'],
            'second_lastname' => ['nullable', 'string', 'max:64'],
            'email' => ['nullable', 'email', 'max:128'],
            'tutor_type' => ['required', 'string', 'size:1', 'in:P,M,O'],
            'relation' => ['required', 'string', 'max:32'],
            'student_id' => ['required', 'integer', 'exists:students,id'],
        ];
    }
}
