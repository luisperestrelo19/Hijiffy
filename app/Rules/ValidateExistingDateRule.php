<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\Availability;
use App\Models\Room;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateExistingDateRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        preg_match('/rooms\.(\d+)\.date/', $attribute, $matches);
        $index = $matches[1] ?? null;

        $room = Room::where('code', request()->input("rooms.{$index}.id"))->first();
        if ($room) {
            Availability::where('room_id', $room->id)
                ->where('date', $value)
                ->exists()
                ? $fail('The selected date is already taken for this room.')
                : null;
        }
    }
}
