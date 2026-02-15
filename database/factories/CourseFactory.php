<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'course_category_id' => CourseCategory::factory(),
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'duration' => fake()->randomElement(['2 Years', '3 Years', '4 Years']),
            'intake' => fake()->numberBetween(30, 120),
            'eligibility' => fake()->optional()->sentence(),
            'description' => fake()->optional()->paragraph(),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['is_active' => false]);
    }
}
