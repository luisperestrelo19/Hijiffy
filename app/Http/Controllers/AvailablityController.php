<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreAvailabilityRequest;
use App\Http\Resources\PropertyResource;
use App\Services\AvailabilityService;

class AvailablityController extends Controller
{
    public function store(StoreAvailabilityRequest $request)
    {
        $property = (new AvailabilityService())->insertProperty($request->validated());

        return response()->json(PropertyResource::make($property->load('rooms.availabilities')), 201);
    }
}
