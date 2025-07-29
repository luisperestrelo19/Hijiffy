<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Property;
use App\Services\CacheService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DialogflowController extends Controller
{
    /**
     * Handle the Dialogflow webhook request.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @unauthenticated
     */
    public function handleWebhook(Request $request)
    {
        $data       = $request->all();
        $parameters = $data['queryResult']['parameters'] ?? [];

        try {
            $searchFields = [
                'number_of_guests' => $parameters['guests'] ?? null,
                'check_in'         => !empty($parameters['check_in']) ? Carbon::parse($parameters['check_in'])->format('Y-m-d') : null,
                'check_out'        => !empty($parameters['check_out']) ? Carbon::parse($parameters['check_out'])->format('Y-m-d') : null,
            ];
        } catch (\Exception $e) {
            return response()->json([
                'fulfillmentText' => 'Sorry, there was an error processing the dates provided.',
            ]);
        }

        $properties = (new CacheService(config('hijiffy.cache.module_prefix_availability')))
            ->cacheWithTag('availabilities', $request->all(), function () use ($searchFields) {
                return Property::search($searchFields)->get();
            });

        if ($properties->isNotEmpty() && $properties->pluck('rooms')->flatten()->isNotEmpty()) {
            $pluckedProperties = $properties->pluck('rooms')->flatten();
            $roomTotal         = $pluckedProperties->count();
            $price             = $pluckedProperties->sortBy('price')->first()->price;

            $response = [
                'fulfillmentText' => "Yes! We have $roomTotal rooms available from {$searchFields['check_in']} to {$searchFields['check_out']}, starting at {$price}€. Want to reserve now?",
            ];
        } else {
            $response = [
                'fulfillmentText' => "Sorry, we don't have rooms available for those dates.",
            ];
        }

        return response()->json($response);
    }
}
