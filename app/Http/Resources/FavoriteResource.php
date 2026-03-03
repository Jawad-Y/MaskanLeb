<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'apartment' => new ApartmentResource($this->whenLoaded('apartment')),
            'created_at' => $this->created_at,
        ];
    }
}
