<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class DigitalProductSeeder extends Seeder
{
    /**
     * Download image from URL and save to storage
     */
    private function downloadImageFromUrl($url, $productName, $imageNumber = 1): ?string
    {
        try {
            $slug = \Illuminate\Support\Str::slug($productName);
            $filename = 'products/' . $slug . '-' . $imageNumber . '.jpg';
            
            // Download image using curl
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
            $imageData = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200 && $imageData) {
                Storage::disk('public')->put($filename, $imageData);
                return $filename;
            }
            
            echo "    Warning: Failed to download image from {$url}\n";
            return null;
        } catch (\Exception $e) {
            echo "    Warning: " . $e->getMessage() . "\n";
            return null;
        }
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "\n=== Seeding Digital Products ===\n";
        
        $digitalCategory = Category::firstOrCreate(
            ['name' => 'Digital Products'],
            ['slug' => 'digital-products']
        );

        $digitalProducts = [
            [
                'name' => 'Netflix Premium 1 Bulan',
                'description' => 'Subscription Netflix Premium untuk 1 bulan. Nikmati streaming film dan series tanpa batas dengan kualitas Ultra HD 4K. Dapat digunakan di 4 perangkat sekaligus. Akses ke semua konten Netflix termasuk Netflix Originals. Kode aktivasi akan dikirim melalui email setelah pembayaran dikonfirmasi.',
                'price' => 186000,
                'stock' => 50,
                'image_url' => 'https://images.unsplash.com/photo-1574375927938-d5a98e8ffe85?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'Netflix Premium 3 Bulan',
                'description' => 'Subscription Netflix Premium untuk 3 bulan. Nikmati streaming film dan series tanpa batas dengan kualitas Ultra HD 4K untuk 3 bulan penuh. Hemat lebih banyak dengan paket 3 bulan. Dapat digunakan di 4 perangkat sekaligus. Akses ke semua konten Netflix termasuk Netflix Originals, film Hollywood terbaru, dan series eksklusif. Kode aktivasi akan dikirim melalui email setelah pembayaran dikonfirmasi.',
                'price' => 495000,
                'stock' => 35,
                'image_url' => 'https://images.unsplash.com/photo-1522869635100-9f4c5e86aa37?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'Netflix Premium 1 Tahun',
                'description' => 'Subscription Netflix Premium untuk 1 tahun penuh. Paket terbaik dan paling hemat untuk streaming sepanjang tahun. Nikmati streaming film dan series tanpa batas dengan kualitas Ultra HD 4K. Dapat digunakan di 4 perangkat sekaligus. Akses penuh ke semua konten Netflix di seluruh dunia. Kode aktivasi akan dikirim melalui email setelah pembayaran dikonfirmasi.',
                'price' => 1800000,
                'stock' => 20,
                'image_url' => 'https://images.unsplash.com/photo-1611162616475-46b635cb6868?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'Spotify Premium 3 Bulan',
                'description' => 'Subscription Spotify Premium untuk 3 bulan. Streaming musik tanpa iklan, download lagu offline, dan kualitas audio tinggi. Akses ke lebih dari 100 juta lagu dan podcast. Skip tanpa batas dan dengarkan musik tanpa gangguan. Kode voucher akan dikirim setelah pembayaran.',
                'price' => 149000,
                'stock' => 100,
                'image_url' => 'https://images.unsplash.com/photo-1614680376593-902f74cf0d41?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'Spotify Premium 1 Tahun',
                'description' => 'Subscription Spotify Premium untuk 1 tahun penuh. Paket hemat untuk pecinta musik. Streaming musik tanpa iklan sepanjang tahun, download lagu offline unlimited, dan kualitas audio tinggi. Akses ke lebih dari 100 juta lagu dan podcast dari seluruh dunia. Skip tanpa batas, dengarkan musik tanpa gangguan, dan nikmati fitur premium lainnya. Kode voucher akan dikirim setelah pembayaran.',
                'price' => 550000,
                'stock' => 50,
                'image_url' => 'https://images.unsplash.com/photo-1528490937728-a4aea6b1d8b8?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'Zoom Pro 1 Bulan',
                'description' => 'Lisensi Zoom Pro untuk 1 bulan. Meeting tanpa batas durasi waktu hingga 30 jam per meeting. Maksimal 100 peserta per meeting. Cloud recording 1GB, custom meeting ID, dan reporting. Cocok untuk profesional, pengajar, freelancer, dan bisnis kecil yang butuh meeting online berkualitas. Lisensi akan dikirim via email setelah pembayaran.',
                'price' => 150000,
                'stock' => 45,
                'image_url' => 'https://images.unsplash.com/photo-1588196749597-9ff075ee6b5b?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'Zoom Pro 1 Tahun',
                'description' => 'Lisensi Zoom Pro untuk 1 tahun penuh. Meeting tanpa batas durasi waktu hingga 30 jam per meeting. Maksimal 100 peserta per meeting. Cloud recording 1GB, custom meeting ID, advanced reporting, dan priority support. Cocok untuk profesional, pengajar, dan bisnis yang membutuhkan solusi meeting online jangka panjang. Hemat lebih banyak dengan paket tahunan. Lisensi akan dikirim via email.',
                'price' => 1500000,
                'stock' => 20,
                'image_url' => 'https://images.unsplash.com/photo-1611606063065-ee7946f0787a?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'Zoom Business 1 Tahun',
                'description' => 'Lisensi Zoom Business untuk 1 tahun. Untuk tim minimal 10 user. Meeting hingga 300 peserta, unlimited cloud recording, branded URL, dan company branding. Fitur SSO, managed domains, dan dashboard admin. Cocok untuk perusahaan dan organisasi besar. Lisensi akan dikirim via email setelah pembayaran.',
                'price' => 15000000,
                'stock' => 5,
                'image_url' => 'https://images.unsplash.com/photo-1587825140708-dfaf72ae4b04?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'Microsoft Office 365 Personal 1 Tahun',
                'description' => 'Microsoft Office 365 Personal untuk 1 tahun. Akses ke Word, Excel, PowerPoint, Outlook, dan OneNote versi terbaru. Cloud storage OneDrive 1TB untuk backup file Anda. Dapat digunakan di 1 PC/Mac dan 1 tablet. Update otomatis fitur terbaru. Support teknis dari Microsoft. Product key dikirim via email.',
                'price' => 899000,
                'stock' => 30,
                'image_url' => 'https://images.unsplash.com/photo-1633409361618-c73427e4e206?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'Microsoft Office 365 Family 1 Tahun',
                'description' => 'Microsoft Office 365 Family untuk 1 tahun. Dapat digunakan hingga 6 user/orang. Akses ke Word, Excel, PowerPoint, Outlook, dan OneNote versi terbaru. Cloud storage OneDrive 1TB per user (total 6TB). Update otomatis dan support teknis. Cocok untuk keluarga atau tim kecil. Product key dikirim via email.',
                'price' => 1299000,
                'stock' => 25,
                'image_url' => 'https://images.unsplash.com/photo-1586717799252-bd134ad00e26?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'Adobe Creative Cloud 1 Bulan',
                'description' => 'Subscription Adobe Creative Cloud All Apps untuk 1 bulan. Akses ke semua aplikasi Adobe termasuk Photoshop, Illustrator, Premiere Pro, After Effects, dan 20+ aplikasi lainnya. Cloud storage 100GB. Font Adobe dan Adobe Stock. Cocok untuk desainer dan content creator.',
                'price' => 750000,
                'stock' => 15,
                'image_url' => 'https://images.unsplash.com/photo-1626785774573-4b799315345d?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'Adobe Creative Cloud 1 Tahun',
                'description' => 'Subscription Adobe Creative Cloud All Apps untuk 1 tahun penuh. Hemat lebih banyak dengan paket tahunan. Akses ke semua aplikasi Adobe termasuk Photoshop, Illustrator, Premiere Pro, After Effects, InDesign, XD, dan 20+ aplikasi lainnya. Cloud storage 100GB, Adobe Fonts unlimited, dan akses Adobe Stock. Cocok untuk desainer profesional, video editor, dan content creator. Update aplikasi otomatis.',
                'price' => 7500000,
                'stock' => 8,
                'image_url' => 'https://images.unsplash.com/photo-1611162618071-b39a2ec055fb?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'Disney+ Hotstar Premium 1 Bulan',
                'description' => 'Subscription Disney+ Hotstar Premium untuk 1 bulan. Streaming film Disney, Marvel, Star Wars, Pixar, dan National Geographic. Konten HBO, sports live (Premier League, Formula 1), dan serial Asia. Kualitas 4K UHD. 4 concurrent streams dan download offline. Kode aktivasi dikirim setelah pembayaran.',
                'price' => 49000,
                'stock' => 100,
                'image_url' => 'https://images.unsplash.com/photo-1626814026160-2237a95fc5a0?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'Disney+ Hotstar Premium 1 Tahun',
                'description' => 'Subscription Disney+ Hotstar Premium untuk 1 tahun penuh. Paket paling hemat untuk hiburan keluarga. Streaming film Disney, Marvel, Star Wars, Pixar, dan National Geographic sepanjang tahun. Konten HBO eksklusif, sports live (Premier League, Formula 1, Badminton), dan serial Asia terpopuler. Kualitas 4K UHD. 4 concurrent streams dan download offline unlimited. Kode aktivasi dikirim setelah pembayaran.',
                'price' => 499000,
                'stock' => 40,
                'image_url' => 'https://images.unsplash.com/photo-1522869635100-9f4c5e86aa37?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'Canva Pro 1 Bulan',
                'description' => 'Subscription Canva Pro untuk 1 bulan. Akses ke 420,000+ template premium, 100+ juta stock photos dan graphics. Background remover, resize designs, brand kit, dan collaboration tools. Cloud storage unlimited. Perfect untuk marketing, social media, dan presentasi. Kode aktivasi dikirim setelah pembayaran.',
                'price' => 120000,
                'stock' => 60,
                'image_url' => 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'Canva Pro 1 Tahun',
                'description' => 'Subscription Canva Pro untuk 1 tahun penuh. Hemat dengan paket tahunan. Akses ke 420,000+ template premium, 100+ juta stock photos dan graphics. Background remover, magic resize, brand kit, collaboration tools untuk tim. Cloud storage unlimited. Perfect untuk marketing, social media, dan presentasi. Cocok untuk content creator, marketer, dan business owner.',
                'price' => 1200000,
                'stock' => 25,
                'image_url' => 'https://images.unsplash.com/photo-1558655146-364adaf1fcc9?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'YouTube Premium 3 Bulan',
                'description' => 'Subscription YouTube Premium untuk 3 bulan. Nonton video tanpa iklan, download video offline, background play. Akses YouTube Music Premium untuk streaming musik unlimited. Cocok untuk yang suka menonton video dan mendengarkan musik di YouTube. Voucher code dikirim via email setelah konfirmasi pembayaran.',
                'price' => 180000,
                'stock' => 70,
                'image_url' => 'https://images.unsplash.com/photo-1611162616475-46b635cb6868?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'YouTube Premium 6 Bulan',
                'description' => 'Subscription YouTube Premium untuk 6 bulan. Nonton video tanpa iklan sepanjang semester, download video offline, background play. Akses YouTube Music Premium untuk streaming musik unlimited. Hemat lebih banyak dengan paket 6 bulan. Cocok untuk yang suka menonton video dan mendengarkan musik di YouTube. Voucher code dikirim via email setelah konfirmasi pembayaran.',
                'price' => 350000,
                'stock' => 45,
                'image_url' => 'https://images.unsplash.com/photo-1614680376593-902f74cf0d41?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'GitHub Copilot Individual 1 Tahun',
                'description' => 'Subscription GitHub Copilot Individual untuk 1 tahun. AI pair programmer yang membantu menulis code lebih cepat. Autocomplete code, suggest functions, dan generate code dari comment. Support banyak bahasa pemrograman dan IDE. Cocok untuk developer, programmer, dan software engineer. Lisensi dikirim via email.',
                'price' => 1200000,
                'stock' => 15,
                'image_url' => 'https://images.unsplash.com/photo-1618401471353-b98afee0b2eb?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'Grammarly Premium 1 Tahun',
                'description' => 'Subscription Grammarly Premium untuk 1 tahun. AI writing assistant untuk menulis dalam bahasa Inggris dengan lebih baik. Cek grammar, spelling, punctuation, clarity, engagement, dan tone. Plagiarism checker unlimited. Cocok untuk content writer, mahasiswa, dan profesional. Akses di browser, desktop, dan mobile.',
                'price' => 1500000,
                'stock' => 20,
                'image_url' => 'https://images.unsplash.com/photo-1455390582262-044cdead277a?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'ChatGPT Plus 1 Bulan',
                'description' => 'Subscription ChatGPT Plus untuk 1 bulan. Akses ke GPT-4, respons lebih cepat, akses prioritas saat traffic tinggi. Fitur advanced seperti DALL-E 3 untuk generate image, browsing internet, dan plugin. Cocok untuk produktivitas, research, programming, dan creative work. Akses 24/7 tanpa gangguan.',
                'price' => 250000,
                'stock' => 80,
                'image_url' => 'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'ChatGPT Plus 3 Bulan',
                'description' => 'Subscription ChatGPT Plus untuk 3 bulan. Hemat dengan paket 3 bulan. Akses ke GPT-4 unlimited, respons lebih cepat, akses prioritas. Fitur advanced seperti DALL-E 3, browsing, code interpreter, dan plugin. Perfect untuk produktivitas jangka menengah. Cocok untuk profesional, mahasiswa, dan content creator.',
                'price' => 700000,
                'stock' => 35,
                'image_url' => 'https://images.unsplash.com/photo-1655720828018-edd2daec9349?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'LinkedIn Premium 1 Bulan',
                'description' => 'Subscription LinkedIn Premium Career untuk 1 bulan. InMail credits untuk menghubungi recruiter dan profesional. See who viewed your profile, salary insights, dan applicant insights. Premium badge di profile. Cocok untuk job seeker dan profesional yang ingin expand network. Lisensi dikirim via email.',
                'price' => 450000,
                'stock' => 25,
                'image_url' => 'https://images.unsplash.com/photo-1611944212129-29977ae1398c?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'Udemy Business 1 Tahun (5 User)',
                'description' => 'Subscription Udemy Business untuk 1 tahun dengan 5 user seat. Akses ke 11,000+ kursus top-rated di berbagai kategori: programming, business, design, marketing, dan lainnya. Learning paths, analytics, dan progress tracking. Cocok untuk tim dan perusahaan yang ingin upskill karyawan. Dashboard admin untuk monitoring.',
                'price' => 8500000,
                'stock' => 3,
                'image_url' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=800&h=600&fit=crop',
            ],
            [
                'name' => 'Notion Plus 1 Tahun',
                'description' => 'Subscription Notion Plus untuk 1 tahun. Unlimited blocks, file uploads unlimited, 30 day version history, dan bulk PDF export. Workspace untuk personal dan small team. Perfect untuk note-taking, project management, dan knowledge base. Dapat digunakan untuk 1 user. Lisensi dikirim via email.',
                'price' => 600000,
                'stock' => 30,
                'image_url' => 'https://images.unsplash.com/photo-1484480974693-6ca0a78fb36b?w=800&h=600&fit=crop',
            ],
        ];

        foreach ($digitalProducts as $productData) {
            echo "Creating digital product: {$productData['name']}...\n";
            
            $sku = Product::generateSku($productData['name']);
            $images = [];
            
            // Download image from URL
            $imagePath = $this->downloadImageFromUrl($productData['image_url'], $productData['name'], 1);
            if ($imagePath) {
                $images[] = $imagePath;
                echo "  ✓ Image downloaded\n";
            } else {
                echo "  ✗ Failed to download image\n";
            }
            
            Product::create([
                'category_id' => $digitalCategory->id,
                'name' => $productData['name'],
                'slug' => Product::generateUniqueSlug($productData['name']),
                'sku' => $sku,
                'description' => $productData['description'],
                'price' => $productData['price'],
                'stock' => $productData['stock'],
                'is_active' => true,
                'is_product_digital' => true,
                'images' => $images,
                'weight' => null,
                'height' => null,
                'width' => null,
                'length' => null,
                'has_variants' => false,
            ]);
            
            echo "  ✓ Digital product created\n";
        }
        
        echo "\n=== Digital Products Seeding Completed ===\n";
    }
}
