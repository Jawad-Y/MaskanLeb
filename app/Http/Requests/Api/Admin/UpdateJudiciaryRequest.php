<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateJudiciaryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:100', Rule::unique('judiciaries')->ignore($this->route('judiciary'))],
            'name_ar' => ['nullable', 'string', 'max:100'],
        ];
    }
}
