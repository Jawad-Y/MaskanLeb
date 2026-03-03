<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'apartment_id' => $this->apartment_id,
            'apartment' => new ApartmentResource($this->whenLoaded('apartment')),
            'other_user' => new UserResource($this->whenLoaded('otherUser')),
            'last_message' => $this->last_message,
            'unread_count' => $this->unread_count ?? 0,
            'updated_at' => $this->updated_at,
        ];
    }
}
