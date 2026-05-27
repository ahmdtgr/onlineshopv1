<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use App\Models\Setting;

class MailConfigServiceProvider extends ServiceProvider
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
            // Load SMTP settings from database if they exist
            $mailHost = Setting::get('mail_host');
            $mailPort = Setting::get('mail_port');
            $mailUsername = Setting::get('mail_username');
            $mailPassword = Setting::get('mail_password');
            $mailEncryption = Setting::get('mail_encryption');
            $mailFromAddress = Setting::get('mail_from_address');
            $mailFromName = Setting::get('mail_from_name');

            // Only update config if settings exist in database
            if ($mailHost) {
                Config::set('mail.mailers.smtp.host', $mailHost);
            }
            if ($mailPort) {
                Config::set('mail.mailers.smtp.port', $mailPort);
            }
            if ($mailUsername) {
                Config::set('mail.mailers.smtp.username', $mailUsername);
            }
            if ($mailPassword) {
                Config::set('mail.mailers.smtp.password', $mailPassword);
            }
            if ($mailEncryption) {
                Config::set('mail.mailers.smtp.encryption', $mailEncryption);
            }
            if ($mailFromAddress) {
                Config::set('mail.from.address', $mailFromAddress);
            }
            if ($mailFromName) {
                Config::set('mail.from.name', $mailFromName);
            }
        } catch (\Exception $e) {
            // If database is not ready or settings table doesn't exist, use .env values
            // This prevents errors during migrations
        }
    }
}
