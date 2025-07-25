<?php

declare(strict_types=1);

namespace App\Http\Requests\QueryParams;

use Illuminate\Foundation\Http\FormRequest;

class AvailabilityQueryParams extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'property_id'      => ['string'],
            'check_in'         => ['date', 'after_or_equal:today', 'date_format:Y-m-d'],
            'check_out'        => ['date', 'after:check_in', 'date_format:Y-m-d'],
            'number_of_guests' => ['integer'],
        ];
    }
}
