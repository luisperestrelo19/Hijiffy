<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\QueryParams\AvailabilityQueryParams;
use App\Http\Requests\StoreAvailabilityRequest;
use App\Http\Resources\AvailabilitiesSearchResource;
use App\Http\Resources\PropertyResource;
use App\Models\Property;
use App\Services\AvailabilityService;
use App\Services\CacheService;

class AvailablityController extends Controller
{
    public function index(AvailabilityQueryParams $request)
    {
        $properties = (new CacheService(config('hijiffy.cache.module_prefix_availability')))
            ->cacheWithTag('availabilities', $request->all(), function () use ($request) {
                return Property::search($request)->get();
            });

        return response()->json(AvailabilitiesSearchResource::collection($properties));
    }

    public function store(StoreAvailabilityRequest $request)
    {
        $property = (new AvailabilityService())->insertProperty($request->validated());

        (new CacheService(config('hijiffy.cache.module_prefix_availability')))->forgetTag('availabilities');
        return response()->json(PropertyResource::make($property->load('rooms.availabilities')), 201);
    }
}
