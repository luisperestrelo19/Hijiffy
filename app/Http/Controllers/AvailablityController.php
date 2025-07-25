<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\QueryParams\AvailabilityQueryParams;
use App\Http\Requests\StoreAvailabilityRequest;
use App\Http\Resources\PropertyResource;
use App\Models\Property;
use App\Services\AvailabilityService;

class AvailablityController extends Controller
{
    public function index(AvailabilityQueryParams $request)
    {
        $properties = Property::with(['rooms.availabilities'])->search($request)
            ->paginate();

        return response()->json([
            'data' => PropertyResource::collection($properties->items()),
            'meta' => [
                'current_page' => $properties->currentPage(),
                'last_page'    => $properties->lastPage(),
                'per_page'     => $properties->perPage(),
                'total'        => $properties->total(),
            ],
        ]);
    }

    public function store(StoreAvailabilityRequest $request)
    {
        $property = (new AvailabilityService())->insertProperty($request->validated());

        return response()->json(PropertyResource::make($property->load('rooms.availabilities')), 201);
    }
}
