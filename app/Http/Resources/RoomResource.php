<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'room_id'        => $this->code,
            'max_guests'     => $this->max_guests,
            'price'          => $this->price,
            'availabilities' => AvailabilityResource::collection($this->whenLoaded('availabilities')),
        ];
    }
}
