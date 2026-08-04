<?php

namespace Database\Factories;

use App\Models\NewsPost;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<NewsPost>
 */
class NewsPostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(8);

        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(6),
            'legacy_path' => null,
            'excerpt' => fake()->paragraph(),
            'body' => fake()->paragraphs(5, true),
            'image_path' => null,
            'is_published' => true,
            'published_at' => now(),
        ];
    }

    /**
     * Mark news post as unpublished.
     */
    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
            'published_at' => null,
        ]);
    }
}
