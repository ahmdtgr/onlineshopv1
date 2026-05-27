<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Get all products and users
        $products = Product::all();
        $users = User::where('is_admin', false)->get();

        if ($users->isEmpty()) {
            // Create some customer users if none exist
            $names = ['Ahmad Tegar', 'Budi Santoso', 'Siti Aminah', 'Dewi Lestari', 'Joko Widodo', 'Rian Hidayat', 'Mega Utami', 'Adi Wijaya'];
            foreach ($names as $name) {
                $users->push(User::create([
                    'name' => $name,
                    'email' => Str::lower(str_replace(' ', '.', $name)) . '@gmail.com',
                    'password' => bcrypt('password'),
                    'is_admin' => false,
                ]));
            }
        }

        if ($products->isEmpty()) {
            return;
        }

        $statuses = ['pending', 'awaiting_confirmation', 'processing', 'shipped', 'completed', 'cancelled'];
        $paymentStatuses = ['unpaid', 'paid'];
        $shippingProviders = ['JNE', 'J&T', 'Sicepat', 'Anteraja'];
        $shippingServices = [
            ['courier_code' => 'jne', 'courier_name' => 'JNE', 'courier_service_name' => 'REG', 'price' => 12000, 'duration' => '2-3 hari'],
            ['courier_code' => 'jnt', 'courier_name' => 'J&T', 'courier_service_name' => 'EZ', 'price' => 15000, 'duration' => '1-2 hari'],
            ['courier_code' => 'sicepat', 'courier_name' => 'Sicepat', 'courier_service_name' => 'SIUNTUNG', 'price' => 10000, 'duration' => '2-4 hari'],
        ];

        // Seed 50 orders spread across the last 30 days
        for ($i = 0; $i < 50; $i++) {
            $user = $users->random();
            $createdAt = Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59));

            // Select random products for order items
            $orderProducts = $products->random(rand(1, 3));
            
            $subtotal = 0;
            $itemsData = [];

            foreach ($orderProducts as $product) {
                $qty = rand(1, 3);
                $price = $product->price ?? rand(50000, 150000);
                $subtotal += $price * $qty;
                
                $itemsData[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $qty,
                    'price' => $price,
                ];
            }

            // Determine status combinations
            $status = $statuses[array_rand($statuses)];
            $paymentStatus = 'unpaid';
            $isApproved = false;
            $shippingStatus = null;
            $paidAt = null;

            if ($status === 'awaiting_confirmation') {
                $paymentStatus = 'paid';
                $isApproved = false;
                $paidAt = $createdAt->copy()->addMinutes(rand(10, 120));
            } elseif ($status === 'processing') {
                $paymentStatus = 'paid';
                $isApproved = true;
                $shippingStatus = 'confirmed';
                $paidAt = $createdAt->copy()->addMinutes(rand(10, 120));
            } elseif ($status === 'shipped') {
                $paymentStatus = 'paid';
                $isApproved = true;
                $shippingStatus = 'dropping_off';
                $paidAt = $createdAt->copy()->addMinutes(rand(10, 120));
            } elseif ($status === 'completed') {
                $paymentStatus = 'paid';
                $isApproved = true;
                $shippingStatus = 'delivered';
                $paidAt = $createdAt->copy()->addMinutes(rand(10, 120));
            } elseif ($status === 'cancelled') {
                $paymentStatus = rand(0, 1) ? 'paid' : 'unpaid';
                $isApproved = false;
                $shippingStatus = 'cancelled';
                if ($paymentStatus === 'paid') {
                    $paidAt = $createdAt->copy()->addMinutes(rand(10, 120));
                }
            } else { // pending
                $paymentStatus = 'unpaid';
                $isApproved = false;
            }

            $shippingCost = rand(10000, 20000);
            $totalAmount = $subtotal + $shippingCost;

            $courier = $shippingServices[array_rand($shippingServices)];

            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'subtotal' => $subtotal,
                'total_amount' => $totalAmount,
                'status' => $status,
                'payment_status' => $paymentStatus,
                'is_approved' => $isApproved,
                'paid_at' => $paidAt,
                
                'recipient_name' => $user->name,
                'phone' => '08' . rand(100000000, 999999999),
                'shipping_provider' => $courier['courier_name'],
                'shipping_cost' => $shippingCost,
                'shipping_area_id' => '12345',
                'shipping_area_name' => 'Kota Jakarta Selatan, Cilandak',
                'shipping_address' => 'Jl. TB Simatupang No. ' . rand(1, 100) . ', Cilandak, Jakarta Selatan',
                'shipping_method_detail' => json_encode($courier),
                'shipping_tracking_number' => $status === 'shipped' || $status === 'completed' ? 'TRK' . rand(100000000, 999999999) : null,
                'shipping_status' => $shippingStatus,
                'noted' => 'Tolong diproses dengan cepat ya gan, terima kasih.',
                
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // Save order items
            foreach ($itemsData as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }
        }
    }
}
