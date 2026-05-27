<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Midtrans Settings
            [
                'key' => 'midtrans_merchant_id',
                'value' => env('MIDTRANS_MERCHANT_ID', ''),
                'type' => 'text',
                'group' => 'midtrans',
            ],
            [
                'key' => 'midtrans_server_key',
                'value' => env('MIDTRANS_SERVER_KEY', ''),
                'type' => 'password',
                'group' => 'midtrans',
            ],
            [
                'key' => 'midtrans_client_key',
                'value' => env('MIDTRANS_CLIENT_KEY', ''),
                'type' => 'text',
                'group' => 'midtrans',
            ],
            [
                'key' => 'midtrans_is_production',
                'value' => env('MIDTRANS_IS_PRODUCTION', 'false'),
                'type' => 'boolean',
                'group' => 'midtrans',
            ],
            [
                'key' => 'midtrans_is_sanitized',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'midtrans',
            ],
            [
                'key' => 'midtrans_is_3ds',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'midtrans',
            ],

            // Email Notification Settings
            [
                'key' => 'email_notifications_enabled',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'email',
            ],
            [
                'key' => 'email_on_order_created',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'email',
            ],
            [
                'key' => 'email_on_order_paid',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'email',
            ],
            [
                'key' => 'email_on_order_approved',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'email',
            ],
            [
                'key' => 'email_on_order_processing',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'email',
            ],
            [
                'key' => 'email_on_order_shipped',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'email',
            ],
            [
                'key' => 'email_on_order_delivered',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'email',
            ],
            [
                'key' => 'email_on_order_cancelled',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'email',
            ],

            // SMTP Settings
            [
                'key' => 'mail_host',
                'value' => env('MAIL_HOST', 'smtp.gmail.com'),
                'type' => 'text',
                'group' => 'smtp',
            ],
            [
                'key' => 'mail_port',
                'value' => env('MAIL_PORT', '587'),
                'type' => 'number',
                'group' => 'smtp',
            ],
            [
                'key' => 'mail_username',
                'value' => env('MAIL_USERNAME', ''),
                'type' => 'text',
                'group' => 'smtp',
            ],
            [
                'key' => 'mail_password',
                'value' => env('MAIL_PASSWORD', ''),
                'type' => 'password',
                'group' => 'smtp',
            ],
            [
                'key' => 'mail_encryption',
                'value' => env('MAIL_ENCRYPTION', 'tls'),
                'type' => 'text',
                'group' => 'smtp',
            ],
            [
                'key' => 'mail_from_address',
                'value' => env('MAIL_FROM_ADDRESS', 'noreply@example.com'),
                'type' => 'text',
                'group' => 'smtp',
            ],
            [
                'key' => 'mail_from_name',
                'value' => env('MAIL_FROM_NAME', 'OnlineShop'),
                'type' => 'text',
                'group' => 'smtp',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        Setting::clearCache();
    }
}
