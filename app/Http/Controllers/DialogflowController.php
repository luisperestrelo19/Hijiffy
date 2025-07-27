<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Property;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DialogflowController extends Controller
{
    public function handleWebhook(Request $request)
    {
        // Recupera os dados enviados pelo Dialogflow
        $data = $request->all();

        $parameters = $data['queryResult']['parameters'] ?? [];

        $searchFields = [
            'number_of_guests' => $parameters['guests'] ?? null,
            'check_in'         => !empty($parameters['check_in']) ? Carbon::parse($parameters['check_in'])->format('Y-m-d') : null,
            'check_out'        => !empty($parameters['check_out']) ? Carbon::parse($parameters['check_out'])->format('Y-m-d') : null,
        ];

        $properties = Property::search($searchFields)->get();

        // Se estiver disponível, envia a resposta de confirmação
        if ($properties->isNotEmpty()) {
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
