<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'receiver_id' => ['required', 'exists:users,id'],
            'apartment_id' => ['required', 'exists:apartments,id'],
            'message' => ['required', 'string', 'max:2000'],
        ];
    }
}
