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

        if (!file_exists($path)) {
            $this->error('File not found');
            return 1;
        }

        $content = file_get_contents($path);

        foreach (json_decode($content, true) as $propertyData) {
            (new AvailabilityService())->insertProperty($propertyData);
        }

        $this->info('Properties imported successfully.');
    }

    public function getFilePath()
    {
        return __DIR__ . '/property_availability.json';
    }
}
