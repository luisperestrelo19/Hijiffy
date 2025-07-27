<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Property;

class AvailabilityService
{
    public function insertProperty(array $data)
    {
        $property = Property::create(
            ['code' => $data['property_id']]
        );

        foreach ($data['rooms'] as $room) {
            //this way we can create a room that doest exists, but if it does we can have multiple availabilities for the same room
            $newRoom = $property->rooms()->firstOrCreate(
                [
                    'code' => $room['room_id'],
                ],
                [
                    'max_guests' => $room['max_guests'],
                    'price'      => $room['price'],
                ]
            );

            $newRoom->availabilities()->create([
                'date' => $room['date'],
            ]);
        }

        return $property;
    }
}
