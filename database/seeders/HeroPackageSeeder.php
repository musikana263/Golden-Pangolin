<?php

namespace Database\Seeders;

use App\HeroPackage;
use Illuminate\Database\Seeder;

class HeroPackageSeeder extends Seeder
{
    public function run(): void
    {
        HeroPackage::firstOrCreate(
            ['id' => 1],
            [
                'badge_text' => 'Popular Choice',
                'title' => 'Chobe Day Trip',
                'duration' => '1 Days',
                'group_size' => 'Max 8 People',
                'price_label' => 'From',
                'price_value' => 'USD 120 per person',
                'link_url' => null,
                'is_active' => true,
            ]
        );
    }
}
