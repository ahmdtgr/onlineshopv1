<?php

namespace App\Services;

use App\Models\Setting;

class EmailNotificationService
{
    /**
     * Check if email notifications are enabled
     */
    public static function isEnabled(): bool
    {
        return Setting::get('email_notifications_enabled', 'false') === 'true';
    }

    /**
     * Check if should send email for specific event
     */
    public static function shouldSendFor(string $event): bool
    {
        if (!self::isEnabled()) {
            return false;
        }

        $settingKey = "email_on_order_{$event}";
        return Setting::get($settingKey, 'true') === 'true';
    }

    /**
     * Check if should send email when order is created
     */
    public static function shouldSendOnOrderCreated(): bool
    {
        return self::shouldSendFor('created');
    }

    /**
     * Check if should send email when order is paid
     */
    public static function shouldSendOnOrderPaid(): bool
    {
        return self::shouldSendFor('paid');
    }

    /**
     * Check if should send email when order is approved
     */
    public static function shouldSendOnOrderApproved(): bool
    {
        return self::shouldSendFor('approved');
    }

    /**
     * Check if should send email when order is processing
     */
    public static function shouldSendOnOrderProcessing(): bool
    {
        return self::shouldSendFor('processing');
    }

    /**
     * Check if should send email when order is shipped
     */
    public static function shouldSendOnOrderShipped(): bool
    {
        return self::shouldSendFor('shipped');
    }

    /**
     * Check if should send email when order is delivered
     */
    public static function shouldSendOnOrderDelivered(): bool
    {
        return self::shouldSendFor('delivered');
    }

    /**
     * Check if should send email when order is cancelled
     */
    public static function shouldSendOnOrderCancelled(): bool
    {
        return self::shouldSendFor('cancelled');
    }
}
