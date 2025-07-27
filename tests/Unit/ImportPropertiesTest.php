<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Console\Commands\ImportProperties\ImportProperties;
use App\Models\Property;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportPropertiesTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_properties()
    {
        $this->artisan('hijiffy:import-properties')
            ->expectsOutput('Properties imported successfully.')
            ->assertExitCode(0);

        $path = app(ImportProperties::class)->getFilePath();
        foreach (json_decode(file_get_contents($path), true) as $propertyData) {
            $this->assertDatabaseHas('properties', [
                'code' => $propertyData['property_id'],
            ]);

            $propertyModel = Property::where('code', $propertyData['property_id'])->first();
            foreach ($propertyData['rooms'] as $room) {
                $this->assertDatabaseHas('rooms', [
                    'property_id' => $propertyModel->id,
                    'code'        => (string) $room['room_id'],
                ]);

                $roomModel = Room::where('code', $room['room_id'])
                    ->where('property_id', $propertyModel->id)
                    ->first();

                $this->assertDatabaseHas('availabilities', [
                    'room_id' => $roomModel->id,
                    'date'    => $room['date'],
                ]);
            }
        }
    }

    public function test_file_not_found()
    {
        $mock = $this->getMockBuilder(ImportProperties::class)
            ->onlyMethods(['getFilePath'])
            ->getMock();

        $mock->method('getFilePath')
            ->willReturn('not_found/property_availability.json');

        // Swap the command in the application container with the mock
        $this->app->instance(ImportProperties::class, $mock);

        $this->artisan('hijiffy:import-properties')
            ->expectsOutput('File not found')
            ->assertExitCode(1);
    }
}
