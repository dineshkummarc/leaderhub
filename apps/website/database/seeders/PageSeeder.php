<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Leaderhub\Core\Models\Page;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            ['slug' => 'about', 'title' => 'Tentang Kami', 'content' => '<p>LeaderHub adalah distributor alat fitness premium terbaik di Indonesia.</p>', 'status' => 'published'],
            ['slug' => 'contact', 'title' => 'Hubungi Kami', 'content' => '<p>Silakan hubungi kami untuk informasi lebih lanjut mengenai produk fitness premium.</p>', 'status' => 'published'],
            ['slug' => 'terms', 'title' => 'Syarat & Ketentuan', 'content' => '<p>Berikut adalah syarat dan ketentuan penggunaan layanan dan pembelian produk kami.</p>', 'status' => 'published'],
            ['slug' => 'privacy', 'title' => 'Kebijakan Privasi', 'content' => '<p>Kami sangat menghargai privasi dan keamanan data pengguna kami.</p>', 'status' => 'published'],
        ];

        foreach ($pages as $page) {
            Page::updateOrCreate(['slug' => $page['slug']], $page);
        }
    }
}
