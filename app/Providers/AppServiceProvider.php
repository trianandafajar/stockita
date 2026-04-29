<?php

namespace App\Providers;

use App\Models\Setting;
use App\Models\Stock;
use App\Models\User;
use App\Observers\StockObserver;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
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
        // URL::forceScheme('https');
        Stock::observe(StockObserver::class);

        if (Schema::hasTable('settings')) {
            $appName = Setting::where('key', 'app.name')->value('value');

            if ($appName) {
                Config::set('app.name', $appName);
            }
        }
    }
}
