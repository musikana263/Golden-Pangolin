<?php

namespace Database\Seeders;

use App\AccommodationType;
use Illuminate\Database\Seeder;

class AccommodationTypesSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Safari Lodge', 'icon' => 'fas fa-tree', 'description' => 'Luxury lodges in or beside national parks.'],
            ['name' => 'Tented Camp', 'icon' => 'fas fa-campground', 'description' => 'Comfortable canvas suites under African skies.'],
            ['name' => 'Boutique Hotel', 'icon' => 'fas fa-hotel', 'description' => 'Stylish small hotels with character.'],
            ['name' => 'Resort', 'icon' => 'fas fa-umbrella-beach', 'description' => 'Full-service resorts with on-site activities.'],
            ['name' => 'Guesthouse', 'icon' => 'fas fa-home', 'description' => 'Family-run, intimate stays.'],
            ['name' => 'Self-Catering', 'icon' => 'fas fa-utensils', 'description' => 'Cottages and villas where you cook for yourself.'],
        ];

        foreach ($types as $type) {
            AccommodationType::firstOrCreate(
                ['name' => $type['name']],
                $type
            );
        }
    }
}
