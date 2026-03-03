<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $apartment = $this->route('apartment');

        return $this->user()->id === $apartment->owner_id || $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'judiciary_id' => ['sometimes', 'exists:judiciaries,id'],
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string', 'max:5000'],
            'price_usd' => ['sometimes', 'numeric', 'min:0', 'max:9999999.99'],
            'number_of_rooms' => ['sometimes', 'integer', 'min:1', 'max:50'],
            'number_of_bathrooms' => ['sometimes', 'integer', 'min:1', 'max:20'],
            'size_m2' => ['sometimes', 'integer', 'min:10', 'max:10000'],
            'furnished' => ['sometimes', 'boolean'],
            'parking' => ['sometimes', 'boolean'],
            'minimum_months' => ['sometimes', 'integer', 'min:1', 'max:120'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'status' => ['sometimes', 'in:available,rented,pending'],
            'images' => ['sometimes', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ];
    }
}
