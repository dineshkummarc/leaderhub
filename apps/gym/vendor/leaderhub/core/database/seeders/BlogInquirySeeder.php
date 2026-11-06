<?php

namespace Leaderhub\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Leaderhub\Core\Models\BlogCategory;
use Leaderhub\Core\Models\BlogTag;
use Leaderhub\Core\Models\BlogPost;
use Leaderhub\Core\Models\Inquiry;
use Leaderhub\Core\Models\InquiryItem;
use Leaderhub\Core\Models\Product;
use Leaderhub\Core\Models\User;
use Illuminate\Support\Str;

class BlogInquirySeeder extends Seeder
{
    public function run()
    {
        // Clear existing demo data
        InquiryItem::query()->delete();
        Inquiry::query()->delete();
        BlogPost::query()->delete();
        BlogTag::query()->delete();
        BlogCategory::query()->delete();

        // --- Blog Categories ---
        $catTips = BlogCategory::create([
            'name' => 'Tips Fitness',
            'slug' => 'tips-fitness',
            'description' => 'Tips dan trik seputar fitness dan gaya hidup sehat.',
        ]);

        BlogCategory::create([
            'name' => 'Produk',
            'slug' => 'produk',
            'description' => 'Informasi terbaru tentang produk fitness LeaderHub.',
        ]);

        BlogCategory::create([
            'name' => 'Industri',
            'slug' => 'industri',
            'description' => 'Berita dan tren industri fitness terbaru.',
        ]);

        // --- Blog Tags ---
        $tagStrength = BlogTag::create(['name' => 'Strength', 'slug' => 'strength']);
        $tagCardio = BlogTag::create(['name' => 'Cardio', 'slug' => 'cardio']);
        $tagTips = BlogTag::create(['name' => 'Tips', 'slug' => 'tips']);
        $tagEquipment = BlogTag::create(['name' => 'Equipment', 'slug' => 'equipment']);

        // --- Get first user as author ---
        $author = User::first();
        if (!$author) {
            $this->command->warn('No users found. Skipping blog post creation.');
            return;
        }

        // --- Get first product ---
        $product = Product::withoutGlobalScope('published')->first();

        // --- Blog Posts ---
        $posts = [
            [
                'title' => 'Tips Fitness #1: Memulai Rutinitas Cardio untuk Pemula',
                'slug' => 'tips-fitness-memulai-rutinitas-cardio',
                'excerpt' => 'Panduan lengkap memulai rutinitas cardio untuk pemula, dari pemilihan alat hingga teknik yang benar.',
                'content' => '<h2>Mengapa Cardio Penting?</h2><p>Cardio atau latihan kardiovaskular adalah salah satu komponen paling penting dalam kebugaran fisik. Latihan ini membantu meningkatkan kesehatan jantung, paru-paru, dan sistem peredaran darah.</p><h2>Alat Cardio yang Direkomendasikan</h2><p>Untuk pemula, kami merekomendasikan treadmill atau sepeda statis. Kedua alat ini mudah digunakan dan memiliki risiko cedera yang rendah.</p><ul><li>Treadmill: Cocok untuk jalan cepat atau lari ringan</li><li>Sepeda Statis: Rendah dampak, baik untuk sendi</li><li>Elliptical: Kombinasi gerakan tangan dan kaki</li></ul><h2>Tips Memulai</h2><p>Mulailah dengan 15-20 menit per sesi, 3 kali seminggu. Tingkatkan durasi secara bertahap. Jangan lupa pemanasan dan pendinginan.</p>',
                'category' => $catTips,
                'tags' => [$tagCardio, $tagTips],
                'reading_time' => 5,
                'status' => 'published',
            ],
            [
                'title' => 'Tips Fitness #2: Panduan Memilih Treadmill untuk Gym Komersial',
                'slug' => 'panduan-memilih-treadmill-komersial',
                'excerpt' => 'Faktor-faktor penting yang perlu dipertimbangkan saat memilih treadmill untuk gym komersial atau hotel.',
                'content' => '<h2>Faktor Kunci Treadmill Komersial</h2><p>Treadmill komersial berbeda dari treadmill rumahan. Mereka dirancang untuk penggunaan berat dan tahan lama.</p><h2>Spesifikasi yang Perlu Diperhatikan</h2><ol><li><strong>Motor</strong>: Minimal 4.0 HP AC untuk penggunaan komersial</li><li><strong>Running Area</strong>: Minimal 1500 x 550 mm untuk kenyamanan</li><li><strong>Max User Weight</strong>: Minimal 180 kg untuk keamanan</li><li><strong>Warranty</strong>: Cari yang menawarkan warranty minimal 3 tahun</li></ol><p>LeaderHub Pro RCT-900A adalah pilihan tepat untuk kebutuhan komersial dengan motor 4.5 HP AC dan area lari 1600 x 600 mm.</p>',
                'category' => $catTips,
                'tags' => [$tagEquipment, $tagTips],
                'reading_time' => 4,
                'status' => 'published',
            ],
            [
                'title' => 'Tips Fitness #3: 5 Latihan Efektif dengan Plate-Loaded Machines',
                'slug' => '5-latihan-plate-loaded-machines',
                'excerpt' => 'Maksimalkan latihan kekuatan Anda dengan 5 gerakan plate-loaded machines yang efektif.',
                'content' => '<h2>Keunggulan Plate-Loaded Machines</h2><p>Plate-loaded machines memberikan gerakan yang lebih alami dibandingkan pin-loaded machines karena beban bergerak bebas mengikuti gerakan Anda.</p><h2>5 Latihan Wajib</h2><h3>1. Chest Press</h3><p>Latin otot dada dengan plate-loaded chest press. Gerakan ini lebih aman daripada barbell bench press karena jalur gerak yang terkontrol.</p><h3>2. Shoulder Press</h3><p>Target utama otot bahu (deltoid) dengan gerakan overhead press.</p><h3>3. Leg Press</h3><p>Latin otot paha depan, belakang, dan bokong. Plate-loaded leg press memberikan rentang gerak yang lebih luas.</p><h3>4. Lat Pulldown</h3><p>Latin otot punggung lebar untuk membentuk V-shape tubuh.</p><h3>5. Row Machine</h3><p>Latihan compound yang melibatkan punggung, lengan, dan inti tubuh.</p>',
                'category' => $catTips,
                'tags' => [$tagStrength, $tagEquipment],
                'reading_time' => 6,
                'status' => 'published',
            ],
            [
                'title' => 'Tips Fitness #4: Pentingnya Recovery dalam Program Latihan',
                'slug' => 'pentingnya-recovery-program-latihan',
                'excerpt' => 'Recovery sama pentingnya dengan latihan itu sendiri. Pelajari cara memulihkan tubuh dengan optimal.',
                'content' => '<h2>Mengapa Recovery Penting?</h2><p>Saat Anda berolahraga, serat otot mengalami kerusakan mikroskopis. Recovery adalah saat tubuh memperbaiki dan memperkuat otot-otot tersebut.</p><h2>Tips Recovery Optimal</h2><ul><li><strong>Tidur cukup</strong>: 7-9 jam per malam untuk pemulihan optimal</li><li><strong>Nutrisi</strong>: Konsumsi protein dalam 30 menit setelah latihan</li><li><strong>Hidrasi</strong>: Minum air yang cukup sebelum, selama, dan setelah latihan</li><li><strong>Active recovery</strong>: Jalan santai atau stretching ringan di hari istirahat</li><li><strong>Pijat dan foam rolling</strong>: Membantu melepaskan ketegangan otot</li></ul><p>Ingatlah bahwa otot tumbuh saat istirahat, bukan saat latihan!</p>',
                'category' => $catTips,
                'tags' => [$tagTips],
                'reading_time' => 4,
                'status' => 'published',
            ],
            [
                'title' => 'Tips Fitness #5: Inovasi Terbaru Alat Fitness Komersial 2026',
                'slug' => 'inovasi-alat-fitness-komersial-2026',
                'excerpt' => 'Tren dan inovasi terbaru dalam industri alat fitness komersial yang perlu Anda ketahui.',
                'content' => '<h2>Tren Fitness 2026</h2><p>Industri fitness terus berkembang. Berikut adalah beberapa inovasi terbaru yang membentuk masa depan gym komersial.</p><h2>1. Smart Touchscreen Consoles</h2><p>Treadmill dan alat cardio kini dilengkapi dengan layar sentuh HD 21.5 inci yang terintegrasi dengan aplikasi fitness populer, streaming virtual routes, dan fitur interaktif lainnya.</p><h2>2. Self-Powered Equipment</h2><p>Alat-alat yang tidak memerlukan listrik (self-powered) semakin populer karena hemat energi dan ramah lingkungan. Cocok untuk gym dengan kapasitas listrik terbatas.</p><h2>3. Data Analytics & IoT</h2><p>Alat fitness modern dilengkapi sensor yang mengumpulkan data penggunaan, membantu pengelola gym memahami pola penggunaan dan merencanakan perawatan.</p><h2>4. Desain Ergonomis</h2><p>Fokus pada biomekanika alami menghasilkan alat yang lebih nyaman dan mengurangi risiko cedera.</p>',
                'category' => $catTips,
                'tags' => [$tagEquipment],
                'reading_time' => 5,
                'status' => 'published',
            ],
        ];

        foreach ($posts as $index => $data) {
            $publishedAt = now()->subDays(count($posts) - $index);

            $post = BlogPost::withoutGlobalScope('published')->create([
                'blog_category_id' => $data['category']->id,
                'author_id' => $author->id,
                'slug' => $data['slug'],
                'title' => $data['title'],
                'excerpt' => $data['excerpt'],
                'content' => $data['content'],
                'status' => $data['status'],
                'published_at' => $publishedAt,
                'reading_time' => $data['reading_time'],
                'seo_title' => $data['title'],
                'seo_description' => $data['excerpt'],
            ]);

            $post->tags()->sync(collect($data['tags'])->pluck('id'));
        }

        // --- Demo Inquiry ---
        if ($product) {
            $inquiry = Inquiry::create([
                'inquiry_number' => 'INQ-2026-00001',
                'uuid' => Str::uuid(),
                'customer_name' => 'John Doe',
                'customer_phone' => '081234567890',
                'customer_email' => 'john@example.com',
                'message' => 'Saya tertarik dengan produk ini. Mohon informasi harga dan ketersediaan stok.',
                'status' => 'new',
            ]);

            InquiryItem::create([
                'inquiry_id' => $inquiry->id,
                'product_id' => $product->id,
                'quantity' => 2,
                'notes' => 'Minat untuk pembelian komersial',
            ]);
        }
    }
}
