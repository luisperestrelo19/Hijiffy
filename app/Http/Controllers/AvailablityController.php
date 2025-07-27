<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\QueryParams\AvailabilityQueryParams;
use App\Http\Requests\StoreAvailabilityRequest;
use App\Http\Resources\AvailabilitiesSearchResource;
use App\Http\Resources\PropertyResource;
use App\Models\Property;
use App\Services\AvailabilityService;

class AvailablityController extends Controller
{
    public function index(AvailabilityQueryParams $request)
    {
        $properties = Property::search($request)->get();

        return response()->json(AvailabilitiesSearchResource::collection($properties), 200);
    }

    public function store(StoreAvailabilityRequest $request)
    {
        $property = (new AvailabilityService())->insertProperty($request->validated());

        return response()->json(PropertyResource::make($property->load('rooms.availabilities')), 201);
    }
}
