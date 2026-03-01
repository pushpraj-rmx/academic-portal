<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DownloadableForm>
 */
class DownloadableFormFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'description' => fake()->sentence(),
            'file_path' => 'forms/sample.pdf',
            'category' => fake()->randomElement(['exam', 'admission', 'general']),
            'sort_order' => fake()->numberBetween(0, 20),
            'is_published' => true,
        ];
    }

    public function unpublished(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_published' => false,
        ]);
    }
}
