<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\Store;
use App\Models\Setting;
use App\Models\OrderItem;
use App\Observers\OrderObserver;
use Illuminate\Support\Facades\URL;
use App\Observers\OrderItemObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;

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
        // Load email settings from database
        try {
            $settings = Setting::all()->pluck('value', 'key')->toArray();
            
            // Check if email notifications are enabled
            $emailEnabled = ($settings['email_notifications_enabled'] ?? 'false') === 'true';
            
            if ($emailEnabled && !empty($settings['mail_host'])) {
                Config::set('mail.mailers.smtp.host', $settings['mail_host']);
                Config::set('mail.mailers.smtp.port', $settings['mail_port'] ?? '587');
                Config::set('mail.mailers.smtp.username', $settings['mail_username']);
                Config::set('mail.mailers.smtp.password', $settings['mail_password']);
                Config::set('mail.mailers.smtp.encryption', $settings['mail_encryption'] ?? 'tls');
                Config::set('mail.from.address', $settings['mail_from_address']);
                Config::set('mail.from.name', $settings['mail_from_name']);
            }
        } catch (\Exception $e) {
            // Database might not be ready (e.g., during migration)
            // Skip loading settings
        }

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
        View::composer('*', function ($view) {
            $view->with('store', Store::first());
        });
        OrderItem::observe(OrderItemObserver::class);
        Order::observe(OrderObserver::class);
    }
}
