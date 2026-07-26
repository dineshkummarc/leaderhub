<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Leaderhub\Core\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['group' => 'general', 'key' => 'site_name', 'value' => 'LeaderHub'],
            ['group' => 'general', 'key' => 'tagline', 'value' => 'Premium Fitness Equipment'],
            ['group' => 'general', 'key' => 'currency_default', 'value' => 'IDR'],
            ['group' => 'general', 'key' => 'exchange_rate', 'value' => '1'],
            ['group' => 'contact', 'key' => 'email', 'value' => 'info@leaderhub.id'],
            ['group' => 'contact', 'key' => 'phone', 'value' => '+6281234567890'],
            ['group' => 'contact', 'key' => 'whatsapp', 'value' => '+6281234567890'],
            ['group' => 'contact', 'key' => 'address', 'value' => 'Jakarta, Indonesia'],
            ['group' => 'seo', 'key' => 'seo_title', 'value' => 'LeaderHub - Premium Fitness Equipment'],
            ['group' => 'seo', 'key' => 'seo_description', 'value' => 'Distributor alat fitness premium terbaik di Indonesia.'],
            ['group' => 'social', 'key' => 'instagram', 'value' => 'https://instagram.com/leaderhub'],
        ];

        foreach ($settings as $setting) {
            Setting::set($setting['key'], $setting['value'], $setting['group']);
        }
    }
}
