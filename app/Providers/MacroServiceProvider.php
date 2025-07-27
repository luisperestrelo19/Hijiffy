<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;

class MacroServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->queryBuilderMacros();
    }

    private function queryBuilderMacros()
    {
        Builder::macro('whereLikeInsensitive', function (string $column, string $value): Builder {
            return $this->whereRaw("LOWER($column) like ?", [strtolower($value)]);
        });

        Builder::macro('orWhereLikeInsensitive', function (string $column, string $value): Builder {
            return $this->orWhereRaw("LOWER($column) like ?", [strtolower($value)]);
        });
    }
}
