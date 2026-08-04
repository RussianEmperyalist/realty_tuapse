<?php

namespace Database\Factories;

use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Property>
 */
class PropertyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(6);

        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(6),
            'deal_type' => fake()->randomElement(['sale', 'rent']),
            'property_type' => fake()->randomElement(['apartment', 'house', 'land', 'room']),
            'city' => fake()->randomElement(['tuapse', 'tuapsinskij-rajon']),
            'address' => fake()->address(),
            'price' => fake()->numberBetween(1000000, 50000000),
            'price_label' => null,
            'currency' => 'руб.',
            'rooms' => fake()->numberBetween(1, 5),
            'floor' => fake()->numberBetween(1, 10),
            'floors_total' => fake()->numberBetween(1, 10),
            'square' => fake()->randomFloat(2, 20, 200),
            'windows' => null,
            'description' => fake()->paragraphs(3, true),
            'latitude' => fake()->latitude(43.5, 44.5),
            'longitude' => fake()->longitude(38.5, 40.5),
            'phone_override' => null,
            'is_published' => true,
            'is_featured' => false,
            'published_at' => now(),
        ];
    }

    /**
     * Mark property as unpublished.
     */
    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
            'published_at' => null,
        ]);
    }

    /**
     * Mark property as featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }
}
