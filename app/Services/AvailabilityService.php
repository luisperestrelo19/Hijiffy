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
            $newRoom = $property->rooms()->create(
                [
                    'code'       => $room['room_id'],
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
