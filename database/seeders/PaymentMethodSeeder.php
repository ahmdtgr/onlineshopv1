<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Copy image from seeder directory to storage
     */
    private function copyImageToStorage($imageName)
    {
        $sourcePath = database_path('seeders/images/payment_method/' . $imageName);
        
        if (!File::exists($sourcePath)) {
            return null;
        }

        // Create directory if not exists
        $destinationDir = storage_path('app/public/payment-methods');
        if (!File::exists($destinationDir)) {
            File::makeDirectory($destinationDir, 0755, true);
        }

        // Get file extension
        $extension = pathinfo($imageName, PATHINFO_EXTENSION);
        $fileName = pathinfo($imageName, PATHINFO_FILENAME) . '.' . $extension;
        
        // Copy file
        $destinationPath = $destinationDir . '/' . $fileName;
        File::copy($sourcePath, $destinationPath);

        return 'payment-methods/' . $fileName;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentMethods = [
            [
                'name' => 'Bank Transfer BCA',
                'image' => $this->copyImageToStorage('bca.jpg'),
                'account_number' => '1234567890',
                'account_name' => 'PT Online Shop'
            ],
            [
                'name' => 'Bank Transfer Mandiri',
                'image' => $this->copyImageToStorage('mandiri.jpeg'),
                'account_number' => '0987654321',
                'account_name' => 'PT Online Shop'
            ]
        ];

        foreach ($paymentMethods as $method) {
            if ($method['image']) {
                PaymentMethod::create($method);
            }
        }
    }
}
