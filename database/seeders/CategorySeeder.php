<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Kemeja',
                'image' => null
            ],
            [
                'name' => 'Kaos',
                'image' => null
            ],
            [
                'name' => 'Celana',
                'image' => null
            ],
            [
                'name' => 'Jaket',
                'image' => null
            ],
            [
                'name' => 'Dress',
                'image' => null
            ]
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Category::generateUniqueSlug($category['name']),
                'image' => $category['image']
            ]);
        }
    }
}
