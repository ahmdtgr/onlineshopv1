<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Store;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Store::create([
            'name' => 'Fashion Store',
            'description' => 'Toko pakaian terlengkap dengan berbagai pilihan fashion terkini',
            'image' => null,
            'banner' => null,
            'address' => 'Jl. Kenangan 2 Sokaraja Wetan',
            'whatsapp' => '62081234567890',
            'shipping_provider' => 'biteship',
            'shipping_api_key' => 'biteship_live.eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJuYW1lIjoib25saW5lc2hvcCIsInVzZXJJZCI6IjZhMTYxMTI1MmU1MmY1NjRiYjZlNjA3YSIsImlhdCI6MTc3OTgzMTI0Nn0.CfDVEAIxdr9foCsMOLPoQ0SGKaP4D4D55dgPbaLnrO8',
            'shipping_area_id' => 'IDNP10IDNC40IDND4848IDZ53181',
            'email_notification' => 'notification@fashionstore.com',
            'is_use_payment_gateway' => 0,
            'requires_customer_email_verification' => 0,
            'area_name' => 'Sokaraja, Banyumas, Jawa Tengah. 53181',
            'primary_color' => '#3498db',
            'secondary_color' => '#2ecc71',
            'shipping_courier' => 'jne,pos,tiki',
        ]);

        echo "✓ Store created: Fashion Store\n";
    }
}
