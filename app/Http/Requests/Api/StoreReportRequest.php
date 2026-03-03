<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'apartment_id' => ['required', 'exists:apartments,id'],
            'reason' => ['required', 'string', 'max:2000'],
        ];
    }
}
