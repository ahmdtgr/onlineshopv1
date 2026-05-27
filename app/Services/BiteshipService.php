<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class BiteshipService
{
    protected $baseUrl = 'https://api.biteship.com';
    protected $apiKey;

    public function __construct($apiKey = null)
    {
        $this->apiKey = $apiKey;
    }

    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    public function validateApiKey($apiKey)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $apiKey
            ])->get("{$this->baseUrl}/v1/maps/areas", [
                'countries' => 'ID',
                'input' => 'Jakarta',
                'type' => 'single'
            ]);
            
            return $response->successful() && !empty($response->json('areas'));
        } catch (\Exception $e) {
            return false;
        }
    }

    public function searchAreas($query)
    {
        try {
            if (!$this->apiKey) {
                \Log::warning('Biteship API Key not set');
                return [];
            }

            $response = Http::withHeaders([
                'Authorization' => $this->apiKey
            ])->get("{$this->baseUrl}/v1/maps/areas", [
                'countries' => 'ID',
                'input' => $query,
                'type' => 'single'
            ]);

            // Handle HTTP response errors
            if (!$response->successful()) {
                $statusCode = $response->status();
                $errorMessage = $response->json('message', 'Unknown error');
                
                // Only show notification for authentication errors (401/403)
                if (in_array($statusCode, [401, 403])) {
                    Notification::make()
                        ->title('API Key Tidak Valid')
                        ->body('Silakan periksa kembali API key Biteship Anda')
                        ->danger()
                        ->send();
                    \Log::error('Biteship API authentication failed', [
                        'status' => $statusCode,
                        'message' => $errorMessage
                    ]);
                } else {
                    // Log other HTTP errors but don't show notification
                    \Log::error('Biteship API error', [
                        'status' => $statusCode,
                        'message' => $errorMessage,
                        'query' => $query
                    ]);
                }
                
                return [];
            }

            $areas = $response->json('areas', []);
            
            // Empty results is normal when searching - don't show error notification
            if (empty($areas)) {
                \Log::info('Biteship search returned no results', ['query' => $query]);
                return [];
            }

            return $areas;
            
        } catch (\Exception $e) {
            // Log the actual exception but only show notification for likely API key issues
            \Log::error('Biteship searchAreas exception', [
                'message' => $e->getMessage(),
                'query' => $query
            ]);
            
            // Only show notification if the error suggests an API key issue
            if (strpos(strtolower($e->getMessage()), 'auth') !== false || 
                strpos(strtolower($e->getMessage()), 'unauthorized') !== false ||
                strpos(strtolower($e->getMessage()), 'forbidden') !== false) {
                
                Notification::make()
                    ->title('Masalah Autentikasi API')
                    ->body('Periksa kembali API key Biteship Anda')
                    ->danger()
                    ->send();
            }
            
            return [];
        }
    }

    public function getRates($originAreaId, $destinationAreaId, $items)
    {
        try {
            if (!$this->apiKey) {
                throw new \Exception('API Key not set');
            }

            $requestData = [
                'origin_area_id' => $originAreaId,
                'destination_area_id' => $destinationAreaId,
                'couriers' => 'jne,sicepat,jnt,pos',
                'items' => collect($items)->map(function ($item) {
                    return [
                        'name' => $item['name'],
                        'description' => $item['description'] ?? '',
                        'value' => $item['value'],
                        'length' => $item['length'] ?? 10,
                        'width' => $item['width'] ?? 10,
                        'height' => $item['height'] ?? 10,
                        'weight' => $item['weight'],
                        'quantity' => $item['quantity']
                    ];
                })->toArray()
            ];

            \Log::info('Biteship getRates Request:', $requestData);

            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
                'Content-Type' => 'application/json'
            ])->post("{$this->baseUrl}/v1/rates/couriers", $requestData);

            \Log::info('Biteship getRates Response:', [
                'status' => $response->status(),
                'success' => $response->successful(),
                'body' => $response->json()
            ]);

            if ($response->successful()) {
                $pricing = $response->json('pricing', []);
                \Log::info('Biteship Pricing Data:', ['count' => count($pricing), 'data' => $pricing]);
                return $pricing;
            }

            if ($response->failed()) {
                \Log::error('Biteship Error:', [
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);
                throw new \Exception($response->json('message', 'Failed to get rates'));
            }

            return [];
        } catch (\Exception $e) {
            \Log::error('Biteship Exception:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Error getting shipping rates: ' . $e->getMessage());
        }
    }

    public function getCouriers()
    {
        try {
            if (!$this->apiKey) {
                throw new \Exception('API Key not set');
            }

            $response = Http::withHeaders([
                'Authorization' => $this->apiKey
            ])->get("{$this->baseUrl}/v1/couriers");

            if (!$response->successful()) {
                \Log::error('Biteship Couriers Error:', $response->json());
                throw new \Exception($response->json('message', 'Failed to get couriers'));
            }

            return $response->json('couriers', []);
            
        } catch (\Exception $e) {
            \Log::error('Biteship Couriers Exception:', ['message' => $e->getMessage()]);
            throw new \Exception('Error getting couriers list: ' . $e->getMessage());
        }
    }

    public function createOrder($orderData)
    {
        try {
            if (!$this->apiKey) {
                throw new \Exception('API Key not set');
            }

            // Prepare order payload
            $payload = [
                'shipper_contact_name' => $orderData['shipper_name'],
                'shipper_contact_phone' => $orderData['shipper_phone'],
                'shipper_contact_email' => $orderData['shipper_email'] ?? '',
                'shipper_organization' => $orderData['shipper_organization'] ?? '',
                'origin_contact_name' => $orderData['shipper_name'],
                'origin_contact_phone' => $orderData['shipper_phone'],
                'origin_address' => $orderData['origin_address'],
                'origin_note' => $orderData['origin_note'] ?? '',
                'origin_area_id' => $orderData['origin_area_id'],
                'origin_coordinate' => $orderData['origin_coordinate'] ?? null,
                'destination_contact_name' => $orderData['destination_name'],
                'destination_contact_phone' => $orderData['destination_phone'],
                'destination_contact_email' => $orderData['destination_email'] ?? '',
                'destination_address' => $orderData['destination_address'],
                'destination_area_id' => $orderData['destination_area_id'],
                'destination_coordinate' => $orderData['destination_coordinate'] ?? null,
                'destination_note' => $orderData['destination_note'] ?? '',
                'courier_company' => $orderData['courier_company'],
                'courier_type' => $orderData['courier_type'],
                'courier_insurance' => $orderData['courier_insurance'] ?? null,
                'delivery_type' => $orderData['delivery_type'] ?? 'now',
                'delivery_date' => $orderData['delivery_date'] ?? null,
                'delivery_time' => $orderData['delivery_time'] ?? null,
                'order_note' => $orderData['order_note'] ?? '',
                'metadata' => $orderData['metadata'] ?? [],
                'items' => collect($orderData['items'])->map(function ($item) {
                    return [
                        'name' => $item['name'],
                        'description' => $item['description'] ?? '',
                        'value' => $item['value'],
                        'quantity' => $item['quantity'],
                        'height' => $item['height'] ?? 10,
                        'length' => $item['length'] ?? 10,
                        'weight' => $item['weight'],
                        'width' => $item['width'] ?? 10,
                    ];
                })->toArray(),
                'reference_id' => $orderData['reference_id'] ?? null,
            ];

            // Remove null values to clean the payload
            $payload = array_filter($payload, function($value) {
                return $value !== null && $value !== '';
            });

            \Log::info('Biteship Order Payload:', $payload);

            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
                'Content-Type' => 'application/json'
            ])->post("{$this->baseUrl}/v1/orders", $payload);

            if (!$response->successful()) {
                $error = $response->json();
                \Log::error('Biteship Order Error:', [
                    'status' => $response->status(),
                    'body' => $error,
                    'payload' => $payload
                ]);
                
                $errorMessage = $error['error'] ?? $error['message'] ?? 'Failed to create order';
                $errorCode = $error['code'] ?? '';
                
                if ($errorCode) {
                    $errorMessage .= " (Code: {$errorCode})";
                }
                
                throw new \Exception($errorMessage);
            }

            $orderResult = $response->json();
            \Log::info('Biteship Order Success:', $orderResult);

            return $orderResult;
            
        } catch (\Exception $e) {
            \Log::error('Biteship Create Order Exception:', [
                'message' => $e->getMessage(),
                'orderData' => $orderData
            ]);
            throw new \Exception('Error creating shipping order: ' . $e->getMessage());
        }
    }

    public function trackOrder($orderId)
    {
        try {
            if (!$this->apiKey) {
                throw new \Exception('API Key not set');
            }

            $response = Http::withHeaders([
                'Authorization' => $this->apiKey
            ])->get("{$this->baseUrl}/v1/orders/{$orderId}");

            if (!$response->successful()) {
                \Log::error('Biteship Track Order Error:', $response->json());
                throw new \Exception($response->json('message', 'Failed to track order'));
            }

            return $response->json();
            
        } catch (\Exception $e) {
            \Log::error('Biteship Track Order Exception:', ['message' => $e->getMessage()]);
            throw new \Exception('Error tracking order: ' . $e->getMessage());
        }
    }

    /**
     * Get tracking information by waybill ID and courier code
     */
    public function getTracking($waybillId, $courierCode)
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->get("{$this->baseUrl}/v1/trackings/{$waybillId}/couriers/{$courierCode}");

            if ($response->successful()) {
                \Log::info('Biteship tracking retrieved successfully', [
                    'waybill_id' => $waybillId,
                    'courier_code' => $courierCode
                ]);
                return $response->json();
            }

            \Log::error('Biteship API error (getTracking)', [
                'waybill_id' => $waybillId,
                'courier_code' => $courierCode,
                'response' => $response->body()
            ]);
            return null;
        } catch (\Exception $e) {
            \Log::error('Biteship API exception (getTracking)', [
                'waybill_id' => $waybillId,
                'courier_code' => $courierCode,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Sync tracking status from Biteship public API
     */
    public function syncTrackingStatus($order)
    {
        try {
            if (!$order->shipping_tracking_number) {
                throw new \Exception('No tracking number found');
            }

            // Get courier code from shipping method detail
            $shippingDetail = $order->shipping_method_detail;
            if (is_string($shippingDetail)) {
                $shippingDetail = json_decode($shippingDetail, true);
            }
            
            $courierCode = $shippingDetail['courier_code'] ?? 'jne';

            // Call Biteship public tracking API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->get('https://api.biteship.com/v1/trackings', [
                'courier' => $courierCode,
                'waybill' => $order->shipping_tracking_number,
            ]);

            if ($response->successful()) {
                $trackingData = $response->json();
                
                // Update order shipping status based on tracking data
                if (isset($trackingData['status'])) {
                    $this->updateOrderStatus($order, $trackingData);
                    
                    Log::info('Tracking status synced successfully', [
                        'order_id' => $order->id,
                        'tracking_number' => $order->shipping_tracking_number,
                        'status' => $trackingData['status']
                    ]);
                    
                    return $trackingData;
                }
            }

            throw new \Exception('Failed to get tracking data: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('Failed to sync tracking status', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Update order status based on tracking data
     */
    private function updateOrderStatus($order, $trackingData)
    {
        $status = $trackingData['status'] ?? '';
        $newShippingStatus = $this->mapTrackingStatusToShippingStatus($status);
        
        if ($newShippingStatus && $order->shipping_status !== $newShippingStatus) {
            $order->update([
                'shipping_status' => $newShippingStatus,
                'shipping_tracking_data' => json_encode($trackingData)
            ]);
            
            // Note: Order status is now determined by combination of payment_status, is_approved, and shipping_status
            // No need to update main order status - it's computed dynamically
        }
    }

    /**
     * Map Biteship tracking status to internal shipping status
     */
    private function mapTrackingStatusToShippingStatus($trackingStatus)
    {
        $statusMap = [
            'confirmed' => 'confirmed',
            'picking_up' => 'picking_up', 
            'picked_up' => 'picked_up',
            'dropping_off' => 'dropping_off',
            'delivered' => 'delivered',
            'cancelled' => 'cancelled',
            'returned' => 'returned',
        ];

        return $statusMap[strtolower($trackingStatus)] ?? null;
    }

    /**
     * Sync all orders with tracking numbers
     */
    public function syncAllTrackingStatus()
    {
        $orders = \App\Models\Order::whereNotNull('shipping_tracking_number')
            ->whereIn('shipping_status', ['confirmed', 'picking_up', 'picked_up', 'dropping_off'])
            ->get();

        $results = [
            'success' => 0,
            'failed' => 0,
            'total' => $orders->count()
        ];

        foreach ($orders as $order) {
            try {
                $this->syncTrackingStatus($order);
                $results['success']++;
            } catch (\Exception $e) {
                $results['failed']++;
                Log::error('Failed to sync order: ' . $order->id, ['error' => $e->getMessage()]);
            }
        }

        return $results;
    }
}