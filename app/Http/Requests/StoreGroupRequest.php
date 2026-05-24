<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:6'],
            'scholar_year_id' => ['required', 'integer', 'exists:scholar_years,id'],
            'academic_level_id' => ['required', 'integer', 'exists:cat_academic_levels,id'],
        ];
    }
}
