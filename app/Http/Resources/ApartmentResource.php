<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApartmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'price_usd' => (float) $this->price_usd,
            'number_of_rooms' => $this->number_of_rooms,
            'number_of_bathrooms' => $this->number_of_bathrooms,
            'size_m2' => $this->size_m2,
            'furnished' => $this->furnished,
            'parking' => $this->parking,
            'minimum_months' => $this->minimum_months,
            'latitude' => $this->latitude ? (float) $this->latitude : null,
            'longitude' => $this->longitude ? (float) $this->longitude : null,
            'status' => $this->status,
            'is_verified' => $this->is_verified,
            'views_count' => $this->views_count,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Relations
            'owner' => new UserResource($this->whenLoaded('owner')),
            'judiciary' => new JudiciaryResource($this->whenLoaded('judiciary')),
            'images' => ApartmentImageResource::collection($this->whenLoaded('images')),

            // Computed
            'favorites_count' => $this->whenCounted('favorites'),
            'reviews_count' => $this->whenCounted('reviews'),
            'average_rating' => $this->when(
                $this->reviews_avg_rating !== null,
                fn () => round((float) $this->reviews_avg_rating, 1)
            ),
            'is_favorited' => $this->when(
                $this->is_favorited !== null,
                fn () => (bool) $this->is_favorited
            ),
        ];
    }
}
