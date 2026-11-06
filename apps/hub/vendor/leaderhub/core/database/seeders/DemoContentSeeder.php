<?php

namespace Leaderhub\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Leaderhub\Core\Models\Category;
use Leaderhub\Core\Models\Brand;
use Leaderhub\Core\Models\Product;
use Leaderhub\Core\Models\ProductImage;
use Leaderhub\Core\Models\ProductSpec;
use Leaderhub\Core\Models\GalleryAlbum;
use Leaderhub\Core\Models\GalleryItem;
use Leaderhub\Core\Models\Banner;
use Illuminate\Support\Str;

class DemoContentSeeder extends Seeder
{
    public function run()
    {
        // Clear existing demo records safely
        Banner::query()->delete();
        GalleryItem::query()->delete();
        GalleryAlbum::query()->delete();
        ProductSpec::query()->delete();
        ProductImage::query()->delete();
        Product::query()->delete();
        Category::query()->delete();
        Brand::query()->delete();

        // Brands
        $brandLeader = Brand::create(['name' => 'LeaderHub Pro', 'slug' => 'leaderhub-pro', 'is_active' => true]);
        $brandIron = Brand::create(['name' => 'IronPower Commercial', 'slug' => 'ironpower-commercial', 'is_active' => true]);

        // Categories
        $catCardio = Category::create([
            'name' => 'Cardio Series',
            'slug' => 'cardio',
            'is_active' => true,
            'sort_order' => 1,
            'image' => 'products-image/Cardio/RCT-900A.webp'
        ]);

        $catPin = Category::create([
            'name' => 'Pin-Loaded Machines',
            'slug' => 'pin-loaded-machines',
            'is_active' => true,
            'sort_order' => 2,
            'image' => 'products-image/Cardio/RE-6600E.webp'
        ]);

        $catPlate = Category::create([
            'name' => 'Plate-Loaded Machines',
            'slug' => 'plate-loaded-machines',
            'is_active' => true,
            'sort_order' => 3,
            'image' => 'products-image/Cardio/RS-800.webp'
        ]);

        // Products
        $productsData = [
            [
                'name' => 'Leader Pro Treadmill RCT-900A',
                'sku' => 'RCT-900A',
                'price' => 3499.00,
                'category_id' => $catCardio->id,
                'brand_id' => $brandLeader->id,
                'is_featured' => true,
                'image' => 'products-image/Cardio/RCT-900A.webp',
                'description' => 'Commercial grade heavy duty treadmill designed for high-traffic fitness clubs. Equipped with quiet AC motor and ergonomic running belt.',
                'specs' => [
                    ['label' => 'Motor', 'value' => '4.5 HP AC Commercial'],
                    ['label' => 'Speed Range', 'value' => '1.0 - 22.0 km/h'],
                    ['label' => 'Incline Range', 'value' => '0 - 18% Auto-Incline'],
                    ['label' => 'Max User Weight', 'value' => '180 kg'],
                    ['label' => 'Running Area', 'value' => '1600 x 600 mm']
                ]
            ],
            [
                'name' => 'Leader Touchscreen Treadmill RCT-950A',
                'sku' => 'RCT-950A-21',
                'price' => 4299.00,
                'category_id' => $catCardio->id,
                'brand_id' => $brandLeader->id,
                'is_featured' => true,
                'image' => 'products-image/Cardio/RCT-950A-21.webp',
                'description' => 'Flagship commercial treadmill featuring a 21.5 inch HD smart touchscreen, interactive workout virtual routes, and wireless charging dock.',
                'specs' => [
                    ['label' => 'Motor', 'value' => '5.0 HP AC Peak Commercial'],
                    ['label' => 'Display', 'value' => '21.5 inch Full HD Capacitive Touch Screen'],
                    ['label' => 'Speed Range', 'value' => '0.8 - 25.0 km/h'],
                    ['label' => 'Max User Weight', 'value' => '200 kg']
                ]
            ],
            [
                'name' => 'Leader Commercial Elliptical RE-6600E',
                'sku' => 'RE-6600E',
                'price' => 2899.00,
                'category_id' => $catCardio->id,
                'brand_id' => $brandLeader->id,
                'is_featured' => true,
                'image' => 'products-image/Cardio/RE-6600E.webp',
                'description' => 'Self-powered commercial elliptical trainer with smooth magnetic resistance and biomechanically natural stride path.',
                'specs' => [
                    ['label' => 'Resistance System', 'value' => 'Self-Powered Electromagnetic'],
                    ['label' => 'Resistance Levels', 'value' => '24 Levels'],
                    ['label' => 'Stride Length', 'value' => '520 mm'],
                    ['label' => 'Max User Weight', 'value' => '160 kg']
                ]
            ],
            [
                'name' => 'Leader Recumbent Bike RE-6600R',
                'sku' => 'RE-6600R',
                'price' => 2199.00,
                'category_id' => $catCardio->id,
                'brand_id' => $brandLeader->id,
                'is_featured' => true,
                'image' => 'products-image/Cardio/RE-6600R.webp',
                'description' => 'Ergonomic recumbent exercise bike providing optimal lumbar support, low step-through frame, and quiet magnetic fly-wheel drive.',
                'specs' => [
                    ['label' => 'Flywheel Weight', 'value' => '14.5 kg Heavy Flywheel'],
                    ['label' => 'Seat Adjustment', 'value' => 'Horizontal Rail Quick Adjust'],
                    ['label' => 'Max User Weight', 'value' => '160 kg']
                ]
            ],
            [
                'name' => 'Leader Upright Bike RE-6600U',
                'sku' => 'RE-6600U',
                'price' => 1899.00,
                'category_id' => $catCardio->id,
                'brand_id' => $brandLeader->id,
                'is_featured' => false,
                'image' => 'products-image/Cardio/RE-6600U.webp',
                'description' => 'Compact commercial upright bike suitable for hotel fitness rooms and boutique studios.',
                'specs' => [
                    ['label' => 'Display', 'value' => 'LED Matrix Window Console'],
                    ['label' => 'Heart Rate', 'value' => 'Hand Pulse Grips + Telemetry']
                ]
            ],
            [
                'name' => 'Leader Water Resistance Rower RS-800',
                'sku' => 'RS-800',
                'price' => 1699.00,
                'category_id' => $catCardio->id,
                'brand_id' => $brandIron->id,
                'is_featured' => true,
                'image' => 'products-image/Cardio/RS-800.webp',
                'description' => 'Premium air and magnetic hybrid rowing machine offering realistic rowing feel and heavy aluminum rail construction.',
                'specs' => [
                    ['label' => 'Drive System', 'value' => 'Poly-V Belt Drive'],
                    ['label' => 'Rail Material', 'value' => 'Commercial Aluminum Alloy']
                ]
            ],
            [
                'name' => 'Leader Studio Spinning Bike RSB-260',
                'sku' => 'RSB-260',
                'price' => 1299.00,
                'category_id' => $catCardio->id,
                'brand_id' => $brandIron->id,
                'is_featured' => false,
                'image' => 'products-image/Cardio/RSB-260.webp',
                'description' => 'High intensity indoor cycle bike designed for group cycling classes.',
                'specs' => [
                    ['label' => 'Flywheel', 'value' => '20 kg Perimeter Weighted Flywheel'],
                    ['label' => 'Brake System', 'value' => 'Magnetic Resistance & Emergency Stop']
                ]
            ],
            [
                'name' => 'Leader Pro Spinning Bike RSB-280',
                'sku' => 'RSB-280',
                'price' => 1499.00,
                'category_id' => $catCardio->id,
                'brand_id' => $brandIron->id,
                'is_featured' => true,
                'image' => 'products-image/Cardio/RSB-280.webp',
                'description' => 'Commercial indoor cycle with dual SPD pedals, multi-position handlebars, and integrated tablet holder.',
                'specs' => [
                    ['label' => 'Frame', 'value' => 'Heavy Duty Zinc Coated Steel Frame'],
                    ['label' => 'Pedals', 'value' => 'Dual Sided Toe Cage & SPD Cleats']
                ]
            ]
        ];

        foreach ($productsData as $data) {
            $product = Product::create([
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'sku' => $data['sku'],
                'description' => $data['description'],
                'status' => 'published',
                'price' => $data['price'],
                'brand_id' => $data['brand_id'],
                'category_id' => $data['category_id'],
                'is_featured' => $data['is_featured'],
            ]);

            ProductImage::create([
                'product_id' => $product->id,
                'path' => $data['image'],
                'sort_order' => 1,
            ]);

            if (isset($data['specs'])) {
                foreach ($data['specs'] as $idx => $spec) {
                    ProductSpec::create([
                        'product_id' => $product->id,
                        'label' => $spec['label'],
                        'value' => $spec['value'],
                        'sort_order' => $idx + 1,
                    ]);
                }
            }
        }

        // Banners
        Banner::create([
            'title' => 'Leader Pro Commercial Fitness Equipment',
            'subtitle' => 'Standar tertinggi peralatan fitness komersial untuk commercial gym & hotel kelas bintang 5.',
            'cta_text' => 'Jelajahi Produk',
            'link' => '/products',
            'image' => 'products-image/Cardio/RCT-950A-21.webp',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Banner::create([
            'title' => 'Solusi Cardio Komersial Presisi Tinggi',
            'subtitle' => 'Desain ergonomis & biomekanik alami untuk pengalaman olahraga tanpa batas.',
            'cta_text' => 'Lihat Katalog Cardio',
            'link' => '/products?category=cardio',
            'image' => 'products-image/Cardio/RE-6600E.webp',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        // Gallery Albums
        $album1 = GalleryAlbum::create([
            'title' => 'Instalasi Commercial Gym',
            'slug' => 'commercial-gym-installations',
            'cover_image' => 'products-image/Cardio/RCT-900A.webp',
            'sort_order' => 1,
        ]);

        $album1->items()->createMany([
            [
                'path' => 'products-image/Cardio/RCT-900A.webp',
                'alt' => 'Treadmill Zone Installation',
                'caption' => 'Area Cardio Treadmill RCT-900A',
                'sort_order' => 1,
            ],
            [
                'path' => 'products-image/Cardio/RE-6600R.webp',
                'alt' => 'Recumbent Bike Setup',
                'caption' => 'Recumbent Bike Lineup',
                'sort_order' => 2,
            ],
            [
                'path' => 'products-image/Cardio/RS-800.webp',
                'alt' => 'Rower Setup',
                'caption' => 'Water Rower Zone',
                'sort_order' => 3,
            ]
        ]);

        $album2 = GalleryAlbum::create([
            'title' => 'Showroom & Hotel Installations',
            'slug' => 'showroom-hotel-installations',
            'cover_image' => 'products-image/Cardio/RCT-950A-21.webp',
            'sort_order' => 2,
        ]);

        $album2->items()->createMany([
            [
                'path' => 'products-image/Cardio/RCT-950A-21.webp',
                'alt' => 'Hotel Fitness Center',
                'caption' => 'Touchscreen Cardio Suite',
                'sort_order' => 1,
            ],
            [
                'path' => 'products-image/Cardio/RE-6600E.webp',
                'alt' => 'Elliptical Area',
                'caption' => 'Self-Powered Elliptical Suite',
                'sort_order' => 2,
            ]
        ]);
    }
}
