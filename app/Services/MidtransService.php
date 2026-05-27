<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = false;
    }

    public function createTransaction($order, $items)
    {
        // Build item details from order items
        $itemDetails = [];
        
        \Log::info('Items received in MidtransService', [
            'items_count' => $items->count(),
            'items_data' => $items->toArray()
        ]);
        
        foreach ($items as $item) {
            $itemDetails[] = [
                'id' => $item->product_id,
                'price' => (int) $item->price,
                'quantity' => $item->quantity,
                'name' => substr($item->product_name, 0, 50)
            ];
        }
        
        \Log::info('Item details after loop', [
            'itemDetails' => $itemDetails
        ]);

        // Add shipping cost
        $itemDetails[] = [
            'id' => 'SHIPPING',
            'price' => (int) $order->shipping_cost,
            'quantity' => 1,
            'name' => 'Biaya Pengiriman'
        ];

        // Add voucher discount as negative item if exists
        if ($order->voucher_discount > 0) {
            $itemDetails[] = [
                'id' => 'VOUCHER-' . $order->voucher_code,
                'price' => -(int) $order->voucher_discount,
                'quantity' => 1,
                'name' => 'Diskon Voucher ' . $order->voucher_code
            ];
        }

        $params = [
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => (int) $order->total_amount,
            ],
            'customer_details' => [
                'first_name' => $order->recipient_name,
                'email' => auth()->user()->email,
                'phone' => $order->phone,
                'shipping_address' => [
                    'first_name' => $order->recipient_name,
                    'phone' => $order->phone,
                    'address' => $order->shipping_address,
                    'city' => $order->shipping_area_name,
                    'postal_code' => '',
                    'country_code' => 'IDN'
                ]
            ],
            'item_details' => $itemDetails
        ];

        \Log::info('Midtrans Transaction Params', [
            'order_number' => $order->order_number,
            'total_amount' => $order->total_amount,
            'gross_amount' => (int) $order->total_amount,
            'item_details' => $itemDetails,
            'params' => $params
        ]);

        try {
            $snapToken = Snap::getSnapToken($params);
            return [
                'success' => true,
                'token' => $snapToken,
                'redirect_url' => "https://" .
                    (Config::$isProduction ? "app.midtrans.com" : "app.sandbox.midtrans.com") .
                    "/snap/v2/vtweb" . $snapToken
            ];
        } catch(\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function getStatus($order)
    {
        try {
            $status = Transaction::status($order->order_number);
            return [
                'success' => true,
                'message' => 'Success get transaction status',
                'data' => $status
            ];
        } catch(\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }
}