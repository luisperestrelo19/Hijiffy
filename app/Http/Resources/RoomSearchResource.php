<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomSearchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'room_id'     => $this->code,
            'max_guests'  => $this->max_guests,
            'daily_price' => $this->price,
            'total_price' => $this->price * $this->available_dates_count,
        ];
    }
}
