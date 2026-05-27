<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Setting;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        try {
            // Load settings from database and override config
            if (\Schema::hasTable('settings')) {
                $settings = Setting::getAllAsArray();

                // Override Midtrans config
                if (isset($settings['midtrans_server_key'])) {
                    config(['midtrans.server_key' => $settings['midtrans_server_key']]);
                }
                if (isset($settings['midtrans_client_key'])) {
                    config(['midtrans.client_key' => $settings['midtrans_client_key']]);
                }
                if (isset($settings['midtrans_is_production'])) {
                    config(['midtrans.is_production' => $settings['midtrans_is_production'] === 'true']);
                }
                if (isset($settings['midtrans_is_sanitized'])) {
                    config(['midtrans.is_sanitized' => $settings['midtrans_is_sanitized'] === 'true']);
                }
                if (isset($settings['midtrans_is_3ds'])) {
                    config(['midtrans.is_3ds' => $settings['midtrans_is_3ds'] === 'true']);
                }

                // Override mail config
                if (isset($settings['email_notifications_enabled']) && $settings['email_notifications_enabled'] === 'true') {
                    // Only use SMTP when email notifications are enabled
                    config(['mail.default' => 'smtp']);

                    if (isset($settings['mail_host'])) {
                        config(['mail.mailers.smtp.host' => trim($settings['mail_host'])]);
                    }
                    if (isset($settings['mail_port'])) {
                        config(['mail.mailers.smtp.port' => trim($settings['mail_port'])]);
                    }
                    if (isset($settings['mail_username'])) {
                        config(['mail.mailers.smtp.username' => trim($settings['mail_username'])]);
                    }
                    if (isset($settings['mail_password'])) {
                        config(['mail.mailers.smtp.password' => trim($settings['mail_password'])]);
                    }
                    if (isset($settings['mail_encryption'])) {
                        config(['mail.mailers.smtp.encryption' => trim($settings['mail_encryption'])]);
                    }
                    if (isset($settings['mail_from_address'])) {
                        config(['mail.from.address' => trim($settings['mail_from_address'])]);
                    }
                    if (isset($settings['mail_from_name'])) {
                        config(['mail.from.name' => trim($settings['mail_from_name'])]);
                    }
                } else {
                    // Use log driver when email notifications are disabled
                    config(['mail.default' => 'log']);
                }
            }
        } catch (\Exception $e) {
            // Silently fail if settings table doesn't exist yet (during migration)
        }
    }
}
