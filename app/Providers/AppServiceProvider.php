<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\MailMessage;
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

        // Password reset URL generation
        ResetPassword::createUrlUsing(function (User $user, string $token) {
            return route('password.reset', [
                'token' => $token,
                'email' => $user->getEmailForPasswordReset(),
            ]);
        });

        // Non-technical human-friendly password reset email
        ResetPassword::toMailUsing(function (User $user, string $token) {
            $url = route('password.reset', [
                'token' => $token,
                'email' => $user->getEmailForPasswordReset(),
            ]);

            return (new MailMessage)
                ->subject('Reset Your Password — TUTUT')
                ->greeting("Hello {$user->name},")
                ->line('You are receiving this email because we received a password reset request for your account.')
                ->action('Reset Password', $url)
                ->line('This password reset link will expire in 60 minutes.')
                ->line('If you did not request a password reset, you can safely ignore this email.');
        });
    }
}
