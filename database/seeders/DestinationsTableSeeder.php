<?php

namespace Database\Seeders;

use App\Destinations;
use Illuminate\Database\Seeder;

class DestinationsTableSeeder extends Seeder
{
    public function run(): void
    {
        Destinations::create([
            'country' => 'Zimbabwe',
            'title' => 'Victoria Falls',
            'description' => 'One of the Seven Natural Wonders of the World — thunderous mist, rainbow arcs, and the mighty Zambezi roaring below.',
            'content' => 'Stand at the edge of Mosi-oa-Tunya, "The Smoke That Thunders," and feel the spray of one of the largest waterfalls on Earth. Walk the rainforest trails for breathtaking viewpoints, take a sunset cruise on the Zambezi River, or get your adrenaline going with white-water rafting and bungee jumping from the iconic bridge.',
            'image' => 'images/destination-1.jpg',
            'published_at' => now(),
        ]);

        Destinations::create([
            'country' => 'Zimbabwe',
            'title' => 'Hwange National Park',
            'description' => 'Zimbabwe\'s largest game reserve — vast plains roamed by elephants, lions, and over 400 bird species.',
            'content' => 'Hwange is home to one of Africa\'s largest elephant populations and offers some of the best game viewing on the continent. Experience morning and evening game drives, guided bush walks with expert rangers, and overnight stays in luxury tented camps under star-filled skies.',
            'image' => 'images/destination-2.jpg',
            'published_at' => now(),
        ]);

        Destinations::create([
            'country' => 'Botswana',
            'title' => 'Chobe National Park',
            'description' => 'Cross into Botswana for an unforgettable day on the Chobe River, with elephants bathing on its banks.',
            'content' => 'The Chobe Day Trip is a Southern African classic — combining a morning game drive through one of Africa\'s most wildlife-dense reserves with an afternoon boat safari along the Chobe River.',
            'image' => 'images/destination-3.jpg',
            'published_at' => now(),
        ]);

        Destinations::create([
            'country' => 'Zambia',
            'title' => 'Livingstone',
            'description' => 'The Zambian side of Victoria Falls — adventure capital with Devil\'s Pool, microlight flights, and walking safaris.',
            'content' => 'Livingstone offers a different vantage of the Falls and a wealth of unique experiences: swim in the legendary Devil\'s Pool on the very edge of the waterfall (seasonal), soar over the gorge in a microlight, or take a guided walking safari in Mosi-oa-Tunya National Park to track white rhino on foot.',
            'image' => 'images/destination-1.jpg',
            'published_at' => now(),
        ]);
    }
}
