<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        // Strict Eloquent Model behavior in Non-Production (catches N+1, lazy loading, and missing attributes)
        Model::shouldBeStrict(! $this->app->isProduction());

        // Prohibit destructive database commands in Production (blocks migrate:fresh, db:wipe, etc.)
        DB::prohibitDestructiveCommands($this->app->isProduction());

        // Force HTTPS in Production environment
        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }

        // Default secure password requirements
        Password::defaults(function () {
            return Password::min(8)
                ->letters()
                ->numbers();
        });
    }
}
