<?php

declare(strict_types=1);

namespace App\Console\Commands\ImportProperties;

use App\Services\AvailabilityService;
use Illuminate\Console\Command;

class ImportProperties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hijiffy:import-properties';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = $this->getFilePath();

        try {
            $content = file_get_contents($path);
        } catch (\Throwable $th) {
            $this->error('Error reading file');
            return 1;
        }

        foreach (json_decode($content, true) as $propertyData) {
            (new AvailabilityService())->insertProperty($propertyData);
        }

        $this->info('Properties imported successfully.');
    }

    public function getFilePath()
    {
        $path = __DIR__ . '/property_availability.json';
        if (!file_exists($path)) {
            $this->error("File not found: $path");
            return 1;
        }

        return $path;
    }
}
