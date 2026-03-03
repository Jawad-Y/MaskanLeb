<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reason' => $this->reason,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'reporter' => new UserResource($this->whenLoaded('reporter')),
            'apartment' => new ApartmentResource($this->whenLoaded('apartment')),
        ];
    }
}
