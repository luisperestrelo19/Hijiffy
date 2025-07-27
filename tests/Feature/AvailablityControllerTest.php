<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Property;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AvailablityControllerTest extends TestCase
{
    public function test_index_returns_properties_list()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Property::factory()->count(2)
            ->has(Room::factory()->count(5))
            ->create();

        $response = $this->getJson(route('availabilities.index'));

        // Assert: check status and structure
        $response->assertOk()
            ->assertJsonStructure([
                '*' => [
                    'property_id',
                    'rooms' => [
                        '*' => [
                            'room_id',
                            'max_guests',
                            'daily_price',
                            'total_price',
                        ],
                    ],
                ],
            ])
            ->assertJsonCount(2);

        $this->assertCount(5, $response->json('0.rooms'));
    }

    public function test_index_returns_properties_list_without_results()
    {
        // Arrange: create properties and a user
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson(route('availabilities.index'));

        // Assert: check status and structure
        $response->assertOk()
            ->assertJsonStructure([])
            ->assertJsonCount(0);
    }

    public function test_store_creates_property_and_returns_resource()
    {
        // Arrange: create a user and prepare request data
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $requestData = [
            'property_id' => 'casa 3',
            'rooms'       => [
                [
                    'room_id'    => 'quarto 1',
                    'date'       => '2025-07-27',
                    'max_guests' => 1,
                    'price'      => 100,
                ],
            ],
        ];

        // Act: send POST request
        $response = $this->postJson(route('availabilities.store'), $requestData);

        // Assert: check status and structure
        $response->assertCreated()
            ->assertJsonStructure([
                'property_id',
                'rooms' => [
                    '*' => [
                        'room_id',
                        'max_guests',
                        'price',
                        'availabilities' => [
                            '*' => [
                                'date',
                            ],
                        ],
                    ],
                ],
            ]);

        // Assert: check if the property and room were created
        $this->assertDatabaseHas('properties', ['code' => 'casa 3']);
        $this->assertDatabaseHas('rooms', [
            'property_id' => Property::where('code', 'casa 3')->first()->id,
            'code'        => 'quarto 1',
            'max_guests'  => 1,
            'price'       => 100,
        ]);

        $this->assertDatabaseHas('availabilities', [
            'room_id' => Room::where('code', 'quarto 1')->first()->id,
            'date'    => '2025-07-27',
        ]);
    }

    public function test_store_requires_authentication()
    {
        $requestData = [
            'name'    => 'Test Property',
            'address' => '123 Main St',
            'rooms'   => [
                [
                    'name'           => 'Room 1',
                    'max_guests'     => 2,
                    'daily_price'    => 100,
                    'availabilities' => [
                        [
                            'date'         => now()->addDay()->toDateString(),
                            'is_available' => true,
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->postJson(route('availabilities.store'), $requestData);

        $response->assertUnauthorized();
    }

    public function test_rate_limit_availabilities_exceed()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $this->withoutExceptionHandling([ThrottleRequestsException::class]);

        for ($i = 0; $i < config('hijiffy.rate_limits.api.limit') + 1; $i++) {
            $response = $this->getJson(route('availabilities.index'));
        }

        $response->assertTooManyRequests();
    }
}
