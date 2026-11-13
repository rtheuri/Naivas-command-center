<?php

namespace App\Providers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production') || env('APP_ENV') === 'production' || isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
            URL::forceScheme('https');
        }

        try {
            if (!Schema::hasTable('budget_reallocations') || !Schema::hasColumn('store_tasks', 'asset_id')) {
                Artisan::call('migrate', ['--force' => true]);
            }
        } catch (\Throwable $e) {
            // Silently continue if database is already migrated
        }
    }
}

