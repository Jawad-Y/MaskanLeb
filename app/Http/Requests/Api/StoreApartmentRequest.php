<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreApartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isOwner() || $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'judiciary_id' => ['required', 'exists:judiciaries,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'price_usd' => ['required', 'numeric', 'min:0', 'max:9999999.99'],
            'number_of_rooms' => ['required', 'integer', 'min:1', 'max:50'],
            'number_of_bathrooms' => ['required', 'integer', 'min:1', 'max:20'],
            'size_m2' => ['required', 'integer', 'min:10', 'max:10000'],
            'furnished' => ['sometimes', 'boolean'],
            'parking' => ['sometimes', 'boolean'],
            'minimum_months' => ['sometimes', 'integer', 'min:1', 'max:120'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'images' => ['sometimes', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'images.max' => 'You can upload a maximum of 10 images.',
            'images.*.max' => 'Each image must be less than 5MB.',
        ];
    }
}
