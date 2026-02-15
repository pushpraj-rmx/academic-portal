<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Specialization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Specialization>
 */
class SpecializationFactory extends Factory
{
    protected $model = Specialization::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(3, true);

        return [
            'course_id' => Course::factory(),
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'description' => fake()->optional()->paragraph(),
            'industry_relevance' => fake()->optional()->sentence(),
            'career_outcomes' => fake()->optional()->sentence(),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['is_active' => false]);
    }
}
