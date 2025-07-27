<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Availability;
use App\Models\Property;
use App\Models\Room;
use Tests\TestCase;

class DialogflowControllerTest extends TestCase
{
    public function test_webhook_dialogflow_no_rooms(): void
    {
        Property::factory()->count(2)
            ->has(
                Room::factory()->has(
                    Availability::factory()->count(5)
                )
                    ->count(5)
            )
            ->create();

        $response = $this->postJson(route('webhook'), [
            'responseId'  => '64bd84ea-64e5-4b92-875f-6795f10150b3-6583c630',
            'queryResult' => [
                'queryText'  => 'I want to book from July 10 to July 12 for 2 people',
                'parameters' => [
                    'guests'    => 2,
                    'check_in'  => '2025-08-03T12:00:00+01:00',
                    'check_out' => '2025-08-04T12:00:00+01:00',
                ],
            ],
        ]);

        $response->assertOk()
            ->assertJson([
                'fulfillmentText' => 'Sorry, we don\'t have rooms available for those dates.',
            ]);
    }

    public function test_webhook_dialogflow(): void
    {
        $date       = now();
        $properties = Property::factory()->count(2)
            ->has(
                Room::factory()->has(
                    Availability::factory(['date' => $date->format('Y-m-d')])->count(5)
                )
                    ->count(5)
            )
            ->create();

        $month = $date->month;
        $day   = $date->day;

        $pluckedProperties = $properties->pluck('rooms')->flatten();
        $roomTotal         = $pluckedProperties->count();
        $price             = $pluckedProperties->sortBy('price')->first()->price;

        $response = $this->postJson(route('webhook'), [
            'responseId'  => '64bd84ea-64e5-4b92-875f-6795f10150b3-6583c630',
            'queryResult' => [
                'queryText'  => "I want to book from $month $day to $month $day for 2 people",
                'parameters' => [
                    'guests'    => 2,
                    'check_in'  => $date->format('Y-m-d\TH:i:sP'),
                    'check_out' => $date->format('Y-m-d\TH:i:sP'),
                ],
            ],
        ]);

        $response->assertOk()
            ->assertJson([
                'fulfillmentText' => "Yes! We have $roomTotal rooms available from 2025-07-27 to 2025-07-27, starting at {$price}€. Want to reserve now?",
            ]);
    }
}
