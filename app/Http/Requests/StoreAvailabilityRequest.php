<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Rules\ValidateExistingDateRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAvailabilityRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'property_id'     => ['required', 'string'],
            'rooms'           => ['required', 'array'],
            'rooms.*.room_id' => ['required', 'string'],
            // 'rooms.*.date'       => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:today', new ValidateExistingDateRule()],
            'rooms.*.date'       => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:today'],
            'rooms.*.max_guests' => ['required', 'integer', 'min:1'],
            'rooms.*.price'      => ['required', 'numeric', 'min:0'],
        ];
    }
}
