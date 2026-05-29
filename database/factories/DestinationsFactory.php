<?php

namespace Database\Factories;

use App\Destinations;
use Illuminate\Database\Eloquent\Factories\Factory;

class DestinationsFactory extends Factory
{
    protected $model = Destinations::class;

    public function definition(): array
    {
        return [
            'title' => fake()->unique()->city().', '.fake()->country(),
            'description' => fake()->sentence(12),
            'content' => fake()->paragraphs(3, true),
            'image' => 'images/destination-'.fake()->numberBetween(1, 12).'.jpg',
            'published_at' => now(),
        ];
    }

    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'published_at' => null,
        ]);
    }
}
