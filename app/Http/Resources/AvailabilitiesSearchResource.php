<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AvailabilitiesSearchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'property_id' => $this->code,
            'rooms'       => $this->rooms->map(function ($room) {
                return [
                    'room_id'     => $room->code,
                    'max_guests'  => $room->max_guests,
                    'daily_price' => $room->price,
                    'total_price' => $room->price * $room->available_dates_count,
                ];
            }),
        ];
    }
}
