<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(config('hijiffy.rate_limits.api.limit'))
                ->by($request->user()->id);
        });

        RateLimiter::for('sync', function ($req) {
            return Limit::perMinute(config('hijiffy.rate_limits.sync-endpoint.limit'))
                ->by($req->user()->id);
        });

        RateLimiter::for('guest', function (Request $request) {
            return Limit::perMinute(config('hijiffy.rate_limits.guest.limit'))
                ->by($request->ip());
        });
    }
}
