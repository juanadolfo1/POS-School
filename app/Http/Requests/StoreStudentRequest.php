<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
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
            'gender' => ['required', 'string', 'size:1', 'in:M,F'],
            'birthday' => ['required', 'date'],
            'curp' => ['required', 'string', 'size:18'],
            'group_id' => ['required', 'integer', 'exists:groups,id'],
        ];
    }
}
