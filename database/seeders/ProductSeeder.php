<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Storage;

class ProductSeeder extends Seeder
{
    /**
     * Copy image from seeder directory to storage
     */
    private function copyImageToStorage($productName, $imageNumber): ?string
    {
        try {
            // Path ke folder seeder images
            $sourceDir = database_path('seeders/images/products');

            // Generate nama file yang dicari berdasarkan nama produk
            $slug = \Illuminate\Support\Str::slug($productName);
            $sourceFile = "{$sourceDir}/{$slug}-{$imageNumber}.jpg";

            // Cek apakah file exists
            if (!file_exists($sourceFile)) {
                // Coba format lain (.png, .jpeg)
                $sourceFile = "{$sourceDir}/{$slug}-{$imageNumber}.png";
                if (!file_exists($sourceFile)) {
                    $sourceFile = "{$sourceDir}/{$slug}-{$imageNumber}.jpeg";
                    if (!file_exists($sourceFile)) {
                        echo "    Warning: Image not found: {$slug}-{$imageNumber}.*\n";
                        return null;
                    }
                }
            }

            // Generate target filename
            $extension = pathinfo($sourceFile, PATHINFO_EXTENSION);
            $filename = 'products/' . $slug . '-' . $imageNumber . '.' . $extension;

            // Copy file ke storage/app/public
            Storage::disk('public')->put($filename, file_get_contents($sourceFile));

            return $filename;
        } catch (\Exception $e) {
            echo "    Warning: " . $e->getMessage() . "\n";
        }

        return null;
    }

    /**
     * Download image from Unsplash and save to storage
     */
    private function downloadImageFromUnsplash($query, $productName): ?string
    {
        try {
            // Menggunakan Unsplash Source dengan query yang lebih spesifik
            // Format: https://source.unsplash.com/featured/?query
            $randomParam = '&sig=' . uniqid();
            $url = "https://images.unsplash.com/photo-1523381210434-271e8be1f52b?w=800&h=600&fit=crop" . $randomParam;

            // Mapping query ke URL foto yang sesuai dari Unsplash
            $imageUrls = [
                'formal shirt men' => [
                    'https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1620012253295-c15cc3e65df4?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1603252109303-2751441dd157?w=800&h=600&fit=crop',
                ],
                'casual shirt women' => [
                    'https://images.unsplash.com/photo-1551488831-00ddcb6c6bd3?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1591369822096-ffd140ec948f?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1525507119028-ed4c629a60a3?w=800&h=600&fit=crop',
                ],
                'plain t-shirt' => [
                    'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1622445275463-afa2ab738c34?w=800&h=600&fit=crop',
                ],
                'graphic tshirt vintage' => [
                    'https://images.unsplash.com/photo-1576566588028-4147f3842f27?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1562157873-818bc0726f68?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1618354691373-d851c5c3a990?w=800&h=600&fit=crop',
                ],
                'jeans slim fit' => [
                    'https://images.unsplash.com/photo-1542272604-787c3835535d?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1604176354204-9268737828e4?w=800&h=600&fit=crop',
                ],
                'chino pants' => [
                    'https://images.unsplash.com/photo-1473966968600-fa801b869a1a?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1624378440070-b4b0b58b1c22?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1506629082955-511b1aa562c8?w=800&h=600&fit=crop',
                ],
                'denim jacket vintage' => [
                    'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1523205771623-e0faa4d2813d?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1527719327859-c6ce80353573?w=800&h=600&fit=crop',
                ],
                'bomber jacket' => [
                    'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1544923408-75c5cef46f14?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1548126032-079fe2c446a6?w=800&h=600&fit=crop',
                ],
                'elegant casual dress' => [
                    'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1566174053879-31528523f8ae?w=800&h=600&fit=crop',
                ],
                'floral summer dress' => [
                    'https://images.unsplash.com/photo-1515372039744-b8f02a3ae446?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1585487000160-6ebcfceb0d03?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=800&h=600&fit=crop',
                ],
            ];

            // Ambil URL berdasarkan query, dengan random index
            if (isset($imageUrls[$query])) {
                $urls = $imageUrls[$query];
                $randomIndex = abs(crc32($productName)) % count($urls);
                $url = $urls[$randomIndex] . '&sig=' . uniqid();
            }

            $response = Http::withOptions([
                'verify' => false,
            ])->timeout(15)->get($url);

            if ($response->successful()) {
                // Generate unique filename
                $filename = 'products/' . \Illuminate\Support\Str::slug($productName) . '.jpg';

                // Save image to storage/app/public
                Storage::disk('public')->put($filename, $response->body());

                return $filename;
            }
        } catch (\Exception $e) {
            // Log error but continue seeding
            echo "    Warning: " . $e->getMessage() . "\n";
        }

        return null;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            // Kemeja
            [
                'category' => 'Kemeja',
                'products' => [
                    [
                        'name' => 'Kemeja Formal Pria',
                        'description' => 'Kemeja formal berkualitas tinggi dengan bahan katun premium pilihan. Didesain dengan cutting modern yang pas di badan, kemeja ini sangat cocok untuk acara formal, meeting kantor, atau presentasi penting. Bahan katun yang breathable membuat Anda tetap nyaman sepanjang hari. Dilengkapi dengan detail kancing berkualitas dan jahitan rapi yang tahan lama. Tersedia dalam berbagai ukuran dan warna untuk memenuhi kebutuhan profesional Anda.',
                        'weight' => 250,
                        'height' => 2,
                        'width' => 40,
                        'length' => 60,
                        'image_query' => 'formal shirt men',
                        'variants' => [
                            ['size' => 'S', 'color' => 'Putih', 'price' => 199000, 'stock' => 20],
                            ['size' => 'M', 'color' => 'Putih', 'price' => 199000, 'stock' => 25],
                            ['size' => 'L', 'color' => 'Putih', 'price' => 199000, 'stock' => 30],
                            ['size' => 'XL', 'color' => 'Putih', 'price' => 209000, 'stock' => 20],
                            ['size' => 'S', 'color' => 'Biru', 'price' => 199000, 'stock' => 15],
                            ['size' => 'M', 'color' => 'Biru', 'price' => 199000, 'stock' => 20],
                            ['size' => 'L', 'color' => 'Biru', 'price' => 199000, 'stock' => 25],
                            ['size' => 'XL', 'color' => 'Biru', 'price' => 209000, 'stock' => 15],
                        ]
                    ],
                    [
                        'name' => 'Kemeja Casual Wanita',
                        'description' => 'Kemeja casual dengan desain modern yang stylish dan nyaman untuk kegiatan sehari-hari. Dibuat dari bahan berkualitas yang lembut di kulit dan mudah menyerap keringat. Model yang versatile cocok dipadukan dengan celana jeans, rok, atau celana formal. Perfect untuk hangout, kuliah, kerja kantoran, atau acara santai lainnya. Desain yang timeless membuat kemeja ini tidak akan pernah ketinggalan zaman dan bisa dipakai dalam berbagai kesempatan.',
                        'weight' => 200,
                        'height' => 2,
                        'width' => 38,
                        'length' => 55,
                        'image_query' => 'casual shirt women',
                        'variants' => [
                            ['size' => 'S', 'color' => 'Putih', 'price' => 189000, 'stock' => 20],
                            ['size' => 'M', 'color' => 'Putih', 'price' => 189000, 'stock' => 25],
                            ['size' => 'L', 'color' => 'Putih', 'price' => 189000, 'stock' => 20],
                            ['size' => 'S', 'color' => 'Merah', 'price' => 189000, 'stock' => 15],
                            ['size' => 'M', 'color' => 'Merah', 'price' => 189000, 'stock' => 20],
                            ['size' => 'L', 'color' => 'Merah', 'price' => 189000, 'stock' => 15],
                        ]
                    ]
                ]
            ],
            // Kaos
            [
                'category' => 'Kaos',
                'products' => [
                    [
                        'name' => 'Kaos Polos Premium',
                        'description' => 'Kaos polos premium dengan bahan cotton combed 30s yang super lembut dan adem di kulit. Material berkualitas tinggi yang tidak mudah berbulu dan tetap nyaman dipakai seharian. Jahitan double stitch membuat kaos lebih tahan lama dan tidak mudah robek. Cutting yang pas membuat tampilan lebih rapi dan tidak kekecilan. Cocok untuk daily wear, olahraga ringan, atau sebagai inner. Basic item yang wajib ada di lemari setiap orang karena sangat versatile dan mudah dipadukan dengan outfit apapun.',
                        'weight' => 180,
                        'height' => 1,
                        'width' => 35,
                        'length' => 50,
                        'image_query' => 'plain t-shirt',
                        'variants' => [
                            ['size' => 'S', 'color' => 'Hitam', 'price' => 99000, 'stock' => 30],
                            ['size' => 'M', 'color' => 'Hitam', 'price' => 99000, 'stock' => 35],
                            ['size' => 'L', 'color' => 'Hitam', 'price' => 99000, 'stock' => 30],
                            ['size' => 'XL', 'color' => 'Hitam', 'price' => 109000, 'stock' => 25],
                            ['size' => 'S', 'color' => 'Putih', 'price' => 99000, 'stock' => 30],
                            ['size' => 'M', 'color' => 'Putih', 'price' => 99000, 'stock' => 35],
                            ['size' => 'L', 'color' => 'Putih', 'price' => 99000, 'stock' => 30],
                            ['size' => 'XL', 'color' => 'Putih', 'price' => 109000, 'stock' => 25],
                        ]
                    ],
                    [
                        'name' => 'Kaos Graphic Vintage',
                        'description' => 'Kaos dengan desain graphic vintage yang unik dan eye-catching. Dibuat dari bahan berkualitas premium dengan sablon rubber printing yang tidak mudah luntur dan retak meskipun sering dicuci. Desain vintage memberikan kesan retro dan stylish yang cocok untuk anak muda. Material yang breathable membuat kaos ini nyaman dipakai di cuaca tropis. Perfect untuk hangout, nongkrong, atau acara casual. Tersedia dalam berbagai ukuran untuk memastikan kenyamanan maksimal saat dikenakan.',
                        'weight' => 200,
                        'height' => 1,
                        'width' => 35,
                        'length' => 50,
                        'image_query' => 'graphic tshirt vintage',
                        'variants' => [
                            ['size' => 'S', 'color' => 'Hitam', 'price' => 129000, 'stock' => 20],
                            ['size' => 'M', 'color' => 'Hitam', 'price' => 129000, 'stock' => 25],
                            ['size' => 'L', 'color' => 'Hitam', 'price' => 129000, 'stock' => 20],
                            ['size' => 'XL', 'color' => 'Hitam', 'price' => 139000, 'stock' => 15],
                            ['size' => 'S', 'color' => 'Abu-abu', 'price' => 129000, 'stock' => 20],
                            ['size' => 'M', 'color' => 'Abu-abu', 'price' => 129000, 'stock' => 25],
                            ['size' => 'L', 'color' => 'Abu-abu', 'price' => 129000, 'stock' => 20],
                            ['size' => 'XL', 'color' => 'Abu-abu', 'price' => 139000, 'stock' => 15],
                        ]
                    ]
                ]
            ],
            // Celana
            [
                'category' => 'Celana',
                'products' => [
                    [
                        'name' => 'Celana Jeans Slim Fit',
                        'description' => 'Celana jeans dengan model slim fit yang mengikuti bentuk tubuh dengan sempurna. Terbuat dari bahan denim berkualitas tinggi yang kuat, tahan lama, dan tidak mudah pudar warnanya. Material yang sedikit stretch memberikan kenyamanan ekstra saat bergerak. Desain slim fit modern cocok untuk berbagai gaya, dari casual hingga semi formal. Dilengkapi dengan kantong yang fungsional dan detail stitching yang rapi. Cocok dipadukan dengan kaos, kemeja, atau jaket untuk tampilan yang stylish dan kekinian.',
                        'weight' => 500,
                        'height' => 3,
                        'width' => 30,
                        'length' => 100,
                        'image_query' => 'jeans slim fit',
                        'variants' => [
                            ['size' => 'S', 'color' => 'Biru', 'price' => 249000, 'stock' => 15],
                            ['size' => 'M', 'color' => 'Biru', 'price' => 249000, 'stock' => 20],
                            ['size' => 'L', 'color' => 'Biru', 'price' => 249000, 'stock' => 15],
                            ['size' => 'XL', 'color' => 'Biru', 'price' => 259000, 'stock' => 10],
                            ['size' => 'S', 'color' => 'Hitam', 'price' => 249000, 'stock' => 15],
                            ['size' => 'M', 'color' => 'Hitam', 'price' => 249000, 'stock' => 20],
                            ['size' => 'L', 'color' => 'Hitam', 'price' => 249000, 'stock' => 15],
                            ['size' => 'XL', 'color' => 'Hitam', 'price' => 259000, 'stock' => 10],
                        ]
                    ],
                    [
                        'name' => 'Celana Chino Panjang',
                        'description' => 'Celana chino panjang dengan bahan premium yang nyaman dipakai seharian dan tidak mudah kusut. Material yang dipilih memiliki karakteristik breathable sehingga tidak gerah meski dipakai lama. Cutting yang presisi memberikan tampilan yang rapi dan profesional. Sangat cocok untuk acara semi formal, kerja kantoran, kuliah, atau hangout. Warna-warna netral yang ditawarkan mudah dipadukan dengan berbagai atasan. Investasi fashion yang tepat karena dapat digunakan untuk berbagai kesempatan dengan tampilan yang tetap stylish.',
                        'weight' => 450,
                        'height' => 3,
                        'width' => 30,
                        'length' => 100,
                        'image_query' => 'chino pants',
                        'variants' => [
                            ['size' => 'S', 'color' => 'Hitam', 'price' => 229000, 'stock' => 15],
                            ['size' => 'M', 'color' => 'Hitam', 'price' => 229000, 'stock' => 20],
                            ['size' => 'L', 'color' => 'Hitam', 'price' => 229000, 'stock' => 15],
                            ['size' => 'XL', 'color' => 'Hitam', 'price' => 239000, 'stock' => 10],
                            ['size' => 'S', 'color' => 'Abu-abu', 'price' => 229000, 'stock' => 15],
                            ['size' => 'M', 'color' => 'Abu-abu', 'price' => 229000, 'stock' => 20],
                            ['size' => 'L', 'color' => 'Abu-abu', 'price' => 229000, 'stock' => 15],
                            ['size' => 'XL', 'color' => 'Abu-abu', 'price' => 239000, 'stock' => 10],
                        ]
                    ]
                ]
            ],
            // Jaket
            [
                'category' => 'Jaket',
                'products' => [
                    [
                        'name' => 'Jaket Denim Vintage',
                        'description' => 'Jaket denim dengan style vintage yang timeless dan tidak pernah ketinggalan zaman. Dibuat dari bahan denim tebal berkualitas tinggi yang kokoh dan tahan lama. Washing effect memberikan kesan vintage yang autentik dan unique. Cocok untuk melindungi dari angin dan cuaca dingin dengan tetap terlihat stylish. Desain klasik yang versatile mudah dipadukan dengan berbagai outfit, dari casual hingga semi formal. Detail kancing dan kantong yang fungsional menambah nilai praktis. Must have item untuk fashion enthusiast yang menghargai gaya klasik.',
                        'weight' => 700,
                        'height' => 3,
                        'width' => 45,
                        'length' => 65,
                        'image_query' => 'denim jacket vintage',
                        'variants' => [
                            ['size' => 'S', 'color' => 'Biru', 'price' => 349000, 'stock' => 10],
                            ['size' => 'M', 'color' => 'Biru', 'price' => 349000, 'stock' => 15],
                            ['size' => 'L', 'color' => 'Biru', 'price' => 349000, 'stock' => 10],
                            ['size' => 'XL', 'color' => 'Biru', 'price' => 359000, 'stock' => 8],
                        ]
                    ],
                    [
                        'name' => 'Jaket Bomber Casual',
                        'description' => 'Jaket bomber dengan desain casual modern yang sporty dan stylish. Bahan yang digunakan ringan namun tetap hangat, cocok untuk berbagai aktivitas outdoor maupun indoor. Model bomber yang iconic dengan detail ribbing di bagian pinggang dan lengan memberikan fit yang sempurna. Desain yang versatile mudah dipadukan dengan jeans, chino, atau jogger pants. Tersedia dalam pilihan warna yang trendy. Perfect untuk melengkapi gaya kasual harian Anda dengan sentuhan modern dan sporty yang kekinian.',
                        'weight' => 650,
                        'height' => 3,
                        'width' => 45,
                        'length' => 65,
                        'image_query' => 'bomber jacket',
                        'variants' => [
                            ['size' => 'S', 'color' => 'Hitam', 'price' => 329000, 'stock' => 10],
                            ['size' => 'M', 'color' => 'Hitam', 'price' => 329000, 'stock' => 15],
                            ['size' => 'L', 'color' => 'Hitam', 'price' => 329000, 'stock' => 10],
                            ['size' => 'XL', 'color' => 'Hitam', 'price' => 339000, 'stock' => 8],
                            ['size' => 'S', 'color' => 'Abu-abu', 'price' => 329000, 'stock' => 10],
                            ['size' => 'M', 'color' => 'Abu-abu', 'price' => 329000, 'stock' => 15],
                            ['size' => 'L', 'color' => 'Abu-abu', 'price' => 329000, 'stock' => 10],
                            ['size' => 'XL', 'color' => 'Abu-abu', 'price' => 339000, 'stock' => 8],
                        ]
                    ]
                ]
            ],
            // Dress
            [
                'category' => 'Dress',
                'products' => [
                    [
                        'name' => 'Dress Casual Elegant',
                        'description' => 'Dress casual dengan desain elegant yang feminine dan classy. Dibuat dari bahan berkualitas yang jatuh sempurna di badan dengan cutting yang flattering untuk berbagai bentuk tubuh. Desain yang sophisticated namun tetap casual membuat dress ini sangat versatile untuk berbagai kesempatan, mulai dari acara formal, gathering, hingga dinner date. Detail yang minimalis memberikan kesan elegan tanpa berlebihan. Nyaman dipakai seharian dan mudah dirawat. Investasi fashion yang tepat untuk tampil percaya diri dan memukau di setiap kesempatan.',
                        'weight' => 350,
                        'height' => 2,
                        'width' => 40,
                        'length' => 90,
                        'image_query' => 'elegant casual dress',
                        'variants' => [
                            ['size' => 'S', 'color' => 'Hitam', 'price' => 279000, 'stock' => 12],
                            ['size' => 'M', 'color' => 'Hitam', 'price' => 279000, 'stock' => 15],
                            ['size' => 'L', 'color' => 'Hitam', 'price' => 279000, 'stock' => 12],
                            ['size' => 'S', 'color' => 'Merah', 'price' => 279000, 'stock' => 10],
                            ['size' => 'M', 'color' => 'Merah', 'price' => 279000, 'stock' => 12],
                            ['size' => 'L', 'color' => 'Merah', 'price' => 279000, 'stock' => 10],
                        ]
                    ],
                    [
                        'name' => 'Dress Floral Summer',
                        'description' => 'Dress dengan motif floral yang fresh dan ceria, perfect untuk menemani hari-hari cerah Anda. Bahan yang ringan dan airy membuat dress ini sangat nyaman dipakai di cuaca panas. Pattern bunga yang colorful memberikan kesan cheerful dan feminine. Model yang flowing cocok untuk berbagai aktivitas, dari jalan-jalan, piknik, hingga beach vacation. Material yang breathable tidak membuat gerah meski dipakai seharian. Easy to style dan bisa dipadukan dengan berbagai aksesoris. Must have item untuk summer wardrobe yang membuat Anda tampil fresh dan stylish.',
                        'weight' => 300,
                        'height' => 2,
                        'width' => 40,
                        'length' => 85,
                        'image_query' => 'floral summer dress',
                        'variants' => [
                            ['size' => 'S', 'color' => 'Biru', 'price' => 259000, 'stock' => 12],
                            ['size' => 'M', 'color' => 'Biru', 'price' => 259000, 'stock' => 15],
                            ['size' => 'L', 'color' => 'Biru', 'price' => 259000, 'stock' => 12],
                            ['size' => 'S', 'color' => 'Merah', 'price' => 259000, 'stock' => 10],
                            ['size' => 'M', 'color' => 'Merah', 'price' => 259000, 'stock' => 12],
                            ['size' => 'L', 'color' => 'Merah', 'price' => 259000, 'stock' => 10],
                        ]
                    ]
                ]
            ]
        ];

        foreach ($products as $categoryProducts) {
            $category = Category::where('name', $categoryProducts['category'])->first();

            if (!$category) {
                continue;
            }

            foreach ($categoryProducts['products'] as $product) {
                $sku = Product::generateSku($product['name']);

                // Copy gambar dari folder seeders/images/products
                $images = [];
                echo "Copying images for {$product['name']}...\n";

                // Copy 3 gambar untuk setiap produk
                for ($i = 1; $i <= 3; $i++) {
                    $imagePath = $this->copyImageToStorage($product['name'], $i);
                    if ($imagePath) {
                        $images[] = $imagePath;
                        echo "  ✓ Image {$i} copied\n";
                    } else {
                        echo "  ✗ Image {$i} not found\n";
                    }
                }

                $createdProduct = Product::create([
                    'category_id' => $category->id,
                    'name' => $product['name'],
                    'slug' => Product::generateUniqueSlug($product['name']),
                    'sku' => $sku,
                    'description' => $product['description'],
                    'price' => null,
                    'stock' => null,
                    'is_active' => true,
                    'images' => $images,
                    'weight' => $product['weight'],
                    'height' => $product['height'],
                    'width' => $product['width'],
                    'length' => $product['length'],
                    'has_variants' => true,
                ]);

                // Create variants untuk produk ini
                if (isset($product['variants'])) {
                    foreach ($product['variants'] as $variant) {
                        // Generate SKU variant berdasarkan SKU produk
                        $colorAbbr = $variant['color'] === 'Abu-abu' ? 'ABU' : strtoupper($variant['color']);
                        $variantSku = $sku . '-' . $variant['size'] . '-' . $colorAbbr;

                        ProductVariant::create([
                            'product_id' => $createdProduct->id,
                            'variant_type1' => 'Ukuran',
                            'variant_option1' => $variant['size'],
                            'variant_type2' => 'Warna',
                            'variant_option2' => $variant['color'],
                            'sku' => $variantSku,
                            'price' => $variant['price'],
                            'stock' => $variant['stock'],
                            'is_active' => true,
                        ]);
                    }
                }
            }
        }
    }
}
